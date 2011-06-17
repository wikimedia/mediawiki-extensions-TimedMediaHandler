<?php
/**
 * Job for transcode jobs
 *
 * @file
 * @ingroup JobQueue
 */

/**
 * Job for web video transcode
 *
 * Support two modes
 * 1) non-free media transcode ( delays the media file being inserted, adds note to talk page once ready)
 * 2) derivatives for video ( makes new sources for the asset )
 *
 * @ingroup JobQueue
 */


/**
 * Unless I am unaware of some command line trickery, we have to wrap the background
 * commands in a script to grab the background exit code ( echo $? )
 *
 * Ie in the bellow code "echo $?" gives us the status of "echo $1" not "nohub" call
 * a wait $pid & echo $? of course does not work since the background taks is not part
 * of a child shell call
 *
 * pseudo shell code for backgroundTask.sh
 *
#/bin/bash

# simple script to run a background and writing output and exit status to a file
# returns its "pid" for proccess monitoring
# Usage: backgroundTask.sh [command] [stdout target file] [pid target file] [exit status target file]

# Run the command with output redirected to target file ( in the background )
$1 > $2 &
# Output the pid to target file:
echo $! > $3
# wait until task is done
wait $!
# output exit status:
echo $? > $4

*/
class WebVideoTranscodeJob extends Job {
	var $targetEncodePath = null;
	var $sourceFilePath = null;

	public function __construct( $title, $params, $id = 0 ) {
		parent::__construct( 'webVideoTranscode', $title, $params, $id );
	}

	// Local function to debug output ( jobs don't have access to the maintenance output class )
	private function output( $msg ){
		print $msg . "\n";
	}
	private function getTargetEncodePath(){
		if( !$this->targetEncodePath ){
			$file = wfLocalFile( $this->title );
			$transcodeKey = $this->params['transcodeKey'];
			$this->targetEncodePath = WebVideoTranscode::getTargetEncodePath( $file, $transcodeKey );
		}
		return $this->targetEncodePath;
	}
	private function getSourceFilePath(){
		if( !$this->sourceFilePath ){
			$file = wfLocalFile( $this->title );
			$this->sourceFilePath = $file->getPath();
		}
		return $this->sourceFilePath;
	}
	// Run the transcode request
	public function run() {
		// get a local pointer to the file
		$file = wfLocalFile( $this->title );

		// Validate the file exists :
		if( !$file || !is_file( $this->getSourceFilePath() ) ){
			$this->output( 'File not found: ' . $this->title );
			return false;
		}

		// Validate the transcode key param:
		$transcodeKey = $this->params['transcodeKey'];
		// Build the destination target
		if( ! isset(  WebVideoTranscode::$derivativeSettings[ $transcodeKey ] )){
			$this->output( "Transcode key $transcodeKey not found, skipping" );
			return false;
		}

		$options = WebVideoTranscode::$derivativeSettings[ $transcodeKey ];

		$this->output( "Encoding to codec: " . $options['videoCodec'] );

		$dbw = wfGetDB( DB_MASTER );
		$db = wfGetDB( DB_SLAVE );

		// Check if we have "already started" the transcode ( possible error )
		$dbStartTime = $db->selectField( 'transcode', 'transcode_time_startwork',
			array( 	'transcode_image_name = ' . $db->addQuotes( $this->title->getDBKey() ),
					'transcode_key =' . $db->addQuotes( $transcodeKey ) )
		);
		if( ! is_null( $dbStartTime ) ){
			$this->output( 'Error, running transcode job, for job that has already started' );
			// back out of this job. ( if there was a transcode error it should be restarted with api transcode-reset )
			// not some strange out-of-order error.
			return false;
		}

		// Update the transcode table letting it know we have "started work":
		$jobStartTimeCache = $db->timestamp();
		$dbw->update(
			'transcode',
			array( 'transcode_time_startwork' => $jobStartTimeCache ),
			array(
				'transcode_image_name' => $this->title->getDBkey(),
				'transcode_key' => $transcodeKey
			),
			__METHOD__,
			array( 'LIMIT' => 1 )
		);


		// Check the codec see which encode method to call;
		if( $options['videoCodec'] == 'theora' ){
			$status = $this->ffmpeg2TheoraEncode( $options );
		} elseif( $options['videoCodec'] == 'vp8' ){
			// Check for twopass:
			if( isset( $options['twopass'] ) ){
				// ffmpeg requires manual two pass
				$status = $this->ffmpegEncode( $options, 1 );
				if( $status ){
					$status = $this->ffmpegEncode( $options, 2 );
				}
			} else {
				$status = $this->ffmpegEncode( $options );
			}
		} else {
			wfDebug( 'Error unknown codec:' . $options['codec'] );
			$status =  'Error unknown target encode codec:' . $options['codec'];
		}

		// Remove any log files all useful info should be in status and or we are done with 2 passs encoding
		$this->removeFffmpgeLogFiles();

		// Do a quick check to confirm the job was not restarted or removed while we were transcoding
		// Confirm the in memory $jobStartTimeCache matches db start time
		$dbStartTime = $db->selectField( 'transcode', 'transcode_time_startwork',
			array( 	'transcode_image_name = ' . $db->addQuotes( $this->title->getDBKey() ),
					'transcode_key =' . $db->addQuotes( $transcodeKey )
			)
		);

		// Check for ( hopefully rare ) issue of or job restarted while transcode in progress
		if(  $db->timestamp( $jobStartTimeCache ) != $db->timestamp( $dbStartTime ) ){
			$this->output('Possible Error, transcode task restarted, removed, or completed while transcode was in progress');
			// if an error; just error out, we can't remove temp files or update states, because the new job may be doing stuff.
			if( $status !== true ){
				return false;
			}
			// else just continue with db updates, and when the new job comes around it won't start because it will see
			// that the job has already been started.
		}

		// If status is oky move the file to its final destination. ( timedMediaHandler will look for it there )
		if( $status === true ){
			$finalDerivativeFilePath = WebVideoTranscode::getDerivativeFilePath( $file, $transcodeKey);
			wfSuppressWarnings();
			$status = rename( $this->getTargetEncodePath(), $finalDerivativeFilePath );
			wfRestoreWarnings();
			$bitrate = round( intval( filesize( $finalDerivativeFilePath ) /  $file->getLength() ) * 8 );
			// Update the transcode table with success time:
			$dbw->update(
				'transcode',
				array(
					'transcode_time_success' => $db->timestamp(),
					'transcode_final_bitrate' => $bitrate
				),
				array(
					'transcode_image_name' => $this->title->getDBkey(),
					'transcode_key' => $transcodeKey,
				),
				__METHOD__,
				array( 'LIMIT' => 1 )
			);
			WebVideoTranscode::invalidatePagesWithFile( $this->title );
		} else {
			// Update the transcode table with failure time and error
			$dbw->update(
				'transcode',
				array(
					'transcode_time_error' => $db->timestamp(),
					'transcode_error' => $status
				),
				array(
						'transcode_image_name' => $this->title->getDBkey(),
						'transcode_key' => $transcodeKey
				),
				__METHOD__,
				array( 'LIMIT' => 1 )
			);
			// no need to invalidate all pages with video. Because all pages remain valid ( no $transcodeKey derivative )
			// just clear the file page ( so that the transcode table shows the error )
			$this->title->invalidateCache();
		}
		// Clear the webVideoTranscode cache ( so we don't keep out dated table cache around )
		webVideoTranscode::clearTranscodeCache( $this->title->getDBkey() );

		// pass along result status:
		return $status;
	}
	function removeFffmpgeLogFiles(){
		$dir = dirname( $this->getTargetEncodePath() );
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$ext = strtolower( pathinfo("$dir/$file", PATHINFO_EXTENSION) );
					if( $ext == 'log' ){
						wfSuppressWarnings();
						unlink( "$dir/$file");
						wfRestoreWarnings();
					}
				}
		        closedir($dh);
		    }
		}
	}
	/** Utility helper for ffmpeg and ffmpeg2theora mapping **/
	function ffmpegEncode( $options, $pass=0 ){
		global $wgFFmpegLocation;

		// Set up the base command
		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . ' -i ' . wfEscapeShellArg( $this->getSourceFilePath() );

		if( isset($options['preset']) ){
			if ($options['preset'] == "360p") {
				$cmd.= " -vpre libvpx-360p";
			} elseif ( $options['preset'] == "720p" ) {
				$cmd.= " -vpre libvpx-720p";
			} elseif ( $options['preset'] == "1080p" ) {
				$cmd.= " -vpre libvpx-1080p";
			}
		}
		if ( isset( $options['novideo'] )  ) {
			$cmd.= " -vn ";
		} else {
			$cmd.= $this->ffmpegAddVideoOptions( $options, $pass );
		}

		// Check for start time
		if( isset( $options['starttime'] ) ){
			$cmd.= ' -ss ' . wfEscapeShellArg( $options['starttime'] );
		} else {
			$options['starttime'] = 0;
		}
		// Check for end time:
		if( isset( $options['endtime'] ) ){
			$cmd.= ' -t ' . intval( $options['endtime'] )  - intval($options['starttime'] ) ;
		}

		if ( $pass == 1 || isset( $options['noaudio'] ) ) {
			$cmd.= ' -an';
		} else {
			$cmd.= $this->ffmpegAddAudioOptions( $options, $pass );
		}

		// Output WebM
		$cmd.=" -f webm";

		if ( $pass != 0 ) {
			$cmd.=" -pass " .wfEscapeShellArg( $pass ) ;
			$cmd.=" -passlogfile " . wfEscapeShellArg( $this->getTargetEncodePath() .'.log' );
		}
		// And the output target:
		if ($pass==1) {
			$cmd.= ' /dev/null';
		} else{
			$cmd.= " " . $this->getTargetEncodePath();
		}

		$this->output( "Running cmd: \n\n" .$cmd . "\n" );

		// Right before we output remove the old file
		wfProfileIn( 'ffmpeg_encode' );
		$retval = 0;
		$shellOutput = $this->runShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg_encode' );

		if( $retval != 0 ){
			return $cmd . "\n\n" . $shellOutput;
		}
		return true;
	}
	function ffmpegAddVideoOptions( $options, $pass){

		// Get a local pointer to the file object
		$file = wfLocalFile( $this->title );

		$cmd ='';
		// Add the boiler plate vp8 ffmpeg command:
		$cmd.=" -y -skip_threshold 0 -rc_buf_aggressivity 0 -bufsize 6000k -rc_init_occupancy 4000 -threads 4";

		// Check for video quality:
		if ( isset( $options['videoQuality'] ) && $options['videoQuality'] >= 0 ) {
			// Map 0-10 to 63-0, higher values worse quality
			$quality = 63 - intval( intval( $options['videoQuality'] )/10 * 63 );
			$cmd .= " -qmin " . wfEscapeShellArg( $quality );
			$cmd .= " -qmax " . wfEscapeShellArg( $quality );
		}

		// Check for video bitrate:
		if ( isset( $options['videoBitrate'] ) ) {
			$cmd.= " -qmin 1 -qmax 51";
			$cmd.= " -vb " . wfEscapeShellArg( $options['videoBitrate'] * 1000 );
		}
		// Set the codec:
		$cmd.= " -vcodec libvpx";

		// Check for aspect ratio ( we don't do anything with this right now)
		if ( isset( $options['aspect'] ) ) {
			$aspectRatio = $options['aspect'];
		} else {
			$aspectRatio = $file->getWidth() . ':' . $file->getHeight();
		}
		// First check targetSize
		if( isset( $options['targetSize'] ) ){
			list( $width, $height ) = WebVideoTranscode::getTargetSizeTransform( $file, $options['targetSize'] );
			$cmd.= ' -s ' . intval( $width ) . 'x' . intval( $height );
		} elseif (isset( $options['maxSize'] ) && intval( $options['maxSize'] ) > 0) {
			// Get size transform ( if maxSize is > file, file size is used:
			list( $width, $height ) = WebVideoTranscode::getMaxSizeTransform( $file, $options['maxSize'] );
			$cmd.= ' -s ' . intval( $width ) . 'x' . intval( $height );
		} elseif (
			(isset( $options['width'] ) && $options['width'] > 0 )
			&&
			(isset( $options['height'] ) && $options['height'] > 0 )
		){
			$cmd.= ' -s ' . intval( $options['width'] ) . 'x' . intval( $options['height'] );
		}

		// Handle crop:
		$optionMap = array(
			'cropTop' => '-croptop',
			'cropBottom' => '-cropbottom',
			'cropLeft' => '-cropleft',
			'cropRight' => '-cropright'
		);
		foreach( $optionMap as $name => $cmdArg ){
			if( isset($options[$name]) ){
				$cmd.= " $cmdArg " .  wfEscapeShellArg( $options[$name] );
			}
		}

		// Check for keyframeInterval
		if( isset( $options['keyframeInterval'] ) ){
			$cmd.= ' -g ' . wfEscapeShellArg( $options['keyframeInterval'] );
			$cmd.= ' -keyint_min ' . wfEscapeShellArg( $options['keyframeInterval'] );
		}
		if( isset( $options['deinterlace'] ) ){
			$cmd.= ' -deinterlace';
		}

		return $cmd;
	}

	function ffmpegAddAudioOptions( $options, $pass){
		$cmd ='';
		if( isset( $options['audioQuality'] ) ){
			$cmd.= " -aq " . wfEscapeShellArg( $options['audioQuality'] );
		}
		if( isset( $options['audioBitrate'] )){
			$cmd.= ' -ab ' . intval( $options['audioBitrate'] ) * 1000;
		}
		if( isset( $options['samplerate'] ) ){
			$cmd.= " -ar " .  wfEscapeShellArg( $options['samplerate'] );
		}
		if( isset( $options['channels'] )){
			$cmd.= " -ac " . wfEscapeShellArg( $options['channels'] );
		}
		// Always use vorbis for audio:
		$cmd.= " -acodec libvorbis ";
		return $cmd;
	}



	/**
	 * ffmpeg2Theora mapping is much simpler since it is the basis of the the firefogg API
	 */
	function ffmpeg2TheoraEncode( $options){
		global $wgFFmpeg2theoraLocation;

		// Set up the base command
		$cmd = wfEscapeShellArg( $wgFFmpeg2theoraLocation ) . ' ' . wfEscapeShellArg( $this->getSourceFilePath() );

		$file = wfLocalFile( $this->title );

		// Check special case options like targetSize
		if( isset( $options['targetSize'] ) ){
			list( $width, $height ) = WebVideoTranscode::getTargetSizeTransform( $file, $options['targetSize'] );
			$options['width'] = $width;
			$options['height'] = $height;
			unset( $options['targetSize'] );
			unset( $options['maxSize'] );
		}

		// Add in the encode settings
		foreach( $options as $key => $val ){
			if( isset( self::$foggMap[$key] ) ){
				if( is_array(  self::$foggMap[$key] ) ){
					$cmd.= ' '. implode(' ', WebVideoTranscode::$foggMap[$key] );
				} elseif ($val == 'true' || $val === true){
			 		$cmd.= ' '. self::$foggMap[$key];
				} elseif ( $val === false){
					//ignore "false" flags
				} else {
					//normal get/set value
					$cmd.= ' '. self::$foggMap[$key] . ' ' . wfEscapeShellArg( $val );
				}
			}
		}

		// Add the output target:
		$cmd.= ' -o ' . wfEscapeShellArg ( $this->getTargetEncodePath() );

		$this->output( "Running cmd: \n\n" .$cmd . "\n" );

		wfProfileIn( 'ffmpeg2theora_encode' );
		$retval = 0;
		$shellOutput = $this->runShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg2theora_encode' );
		if( $retval != 0 ){
			return $cmd . "\n\n" . $shellOutput;
		}
		return true;
	}
	/**
	 * Runs the shell exec command.
	 * if $wgEnableBackgroundTranscodeJobs is enabled will mannage a background transcode task
	 * else it just directly passes off to wfShellExec
	 *
	 * @param $cmd String Command to be run
	 * @param $retval String, refrence variable to return the exit code
	 */
	public function runShellExec( $cmd, &$retval){
		global $wgEnableNiceBackgroundTranscodeJobs, $wgTranscodeBackgroundPriority,
		$wgTranscodeBackgroundTimeLimit;

		// Check if background tasks are enabled
		if( $wgEnableNiceBackgroundTranscodeJobs === false ){
			// Dont display shell output
			$cmd .= ' 2>&1';
			// Directly execute the shell command:
			return wfShellExec( $cmd, $retval );
		}
		// Setup pointers for status and encoding log:
		$encodingLog = $this->getTargetEncodePath() . '.stdout.log';
		$exitStatusLog = $this->getTargetEncodePath() . '.exit.log';

		// Start background shell proc
		$pid = wfShellExec(
			"nohup nice -n $wgTranscodeBackgroundPriority $cmd > " . wfEscapeShellArg( $encodingLog ) . ' ' .
			// ideally we could store the exit status here ( but it causes & echo $! to give us a bad pid )
			// "& echo $? > " . wfEscapeShellArg( $exitStatusLog ) . ' ' .
			"& echo $!",
			$retval
		);
		$pid = trim( $pid );
		$errorMsg = '';

		if( $pid == '' || $retval != 0){
			$errorMsg = "Failed to start, check $wgMaxShellMemory settings";
			$this->output( $errorMsg);
			$retval = 1;
			return $errorMsg;
		}
		$loopCount = 0;
		$oldFileSize = 0;
		$startTime = time();
		$fileIsNotGrowing = false;

		while( true ){

			// Check if pid still runing
			if( ! self::isProcessRunning( $pid ) ){
				// see large note above about background tasks ( echo $? is probably not the exit status we want )
				$retval = wfShellExec( "echo $?");
				//$this->output( $pid . ' is done, $retval: ' .$retval );
				break;
			}

			// Check that the target file is growing ( every 5 seconds )
			if( $loopCount == 5 ){
				// only run check if we are outputing to target file
				// ( two pass encoding does not output to target on first pass )
				if( is_file( $this->getTargetEncodePath() ) ){
					clearstatcache();
					$newFileSize = filesize( $this->getTargetEncodePath() );
					if( $newFileSize == $oldFileSize ){
						if( $fileIsNotGrowing ){
							$errorMsg = "Target File is not increasing in size, kill process.";
							$this->output( $errorMsg );
							// file is not growing in size, kill proccess
							$retval = 1;
							self::killProcess( $pid );
							break;
						}
						// Wait an additional 5 seconds of the file not growing to confirm
						// the transcode is frozen.
						$fileIsNotGrowing = true;
					}
					$oldFileSize = $newFileSize;
				}
				// reset the loop counter
				$loopCount = 0;
			}

			// Check if we have global job run-time has been exceeded:
			if ( $wgTranscodeBackgroundTimeLimit && time() - $startTime  > $wgTranscodeBackgroundTimeLimit ){
				$errorMsg = "Encoding exceeded max job run time ( " . TimedMediaHandler::seconds2npt( $maxTime ) . " ), kill process.";
				$this->output( $errorMsg );
				// File is not growing in size, kill proccess
				$retval = 1;
				self::killProcess( $pid );
				break;
			}

			// Sleep for one second before repeating loop
			$loopCount++;
			sleep( 1 );
		}

		// return the encoding log contents ( will be inserted into error table if an error )
		// ( will be ignored and removed if success )
		if( $errorMsg!= '' ){
			$errorMsg.="\n\n";
		}
		return $errorMsg . file_get_contents( $encodingLog );
	}
	public static function isProcessRunning( $pid ){
       exec( "ps $pid", $processState );
       return( count( $processState ) >= 2 );
	}
	public static function killProcess( $pid ){
		// we are killing a hung prcoces send -9 signal
		wfShellExec( "kill -9 $pid");
	}

	 /**
	 * Mapping between firefogg api and ffmpeg2theora command line
	 *
	 * This lets us share a common api between firefogg and WebVideoTranscode
	 * also see: http://firefogg.org/dev/index.html
	 */
	 public static $foggMap = array(
		// video
		'width'			=> "--width",
		'height'		=> "--height",
		'maxSize'		=> "--max_size",
		'noUpscaling'	=> "--no-upscaling",
		'videoQuality'=> "-v",
		'videoBitrate'	=> "-V",
		'twopass'		=> "--two-pass",
		'framerate'		=> "-F",
		'aspect'		=> "--aspect",
		'starttime'		=> "--starttime",
		'endtime'		=> "--endtime",
		'cropTop'		=> "--croptop",
		'cropBottom'	=> "--cropbottom",
		'cropLeft'		=> "--cropleft",
		'cropRight'		=> "--cropright",
		'keyframeInterval'=> "--key",
		'denoise'		=> array("--pp", "de"),
	 	'deinterlace'	=> "--deinterlace",
		'novideo'		=> array("--novideo", "--no-skeleton"),
		'bufDelay'		=> "--buf-delay",
		 // audio
		'audioQuality'	=> "-a",
		'audioBitrate'	=> "-A",
		'samplerate'	=> "-H",
		'channels'		=> "-c",
		'noaudio'		=> "--noaudio",
		 // metadata
		'artist'		=> "--artist",
		'title'			=> "--title",
		'date'			=> "--date",
		'location'		=> "--location",
		'organization'	=> "--organization",
		'copyright'		=> "--copyright",
		'license'		=> "--license",
		'contact'		=> "--contact"
	);

}
