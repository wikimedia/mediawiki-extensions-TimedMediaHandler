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
class WebVideoTranscodeJob extends Job {
	
	public function __construct( $title, $params, $id = 0 ) {
		parent::__construct( 'webVideoTranscode', $title, $params, $id );
	}
	
	// Local function to debug output ( jobs don't have access to the maintenance output class )
	private function output( $msg ){
		print $msg . "\n";
	}
	
	// Run the transcode request
	public function run() {
		// Get the file object
		$file = wfLocalFile( $this->title );		
		
		$source = $file->getPath();
		if( !is_file($source ) ){
			$this->output( 'File not found: ' . $this->title );
			return false;
		}
		$transcodeKey = $this->params['transcodeKey'];
		
		// Build the destination target
		$destinationFile = WebVideoTranscode::getTargetEncodePath( $file, $transcodeKey );
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
			$status = $this->ffmpeg2TheoraEncode( $file, $destinationFile, $options );
		} else if( $options['videoCodec'] == 'vp8' ){			
			// Check for twopass:
			if( isset( $options['twopass'] ) ){
				// ffmpeg requires manual two pass
				$status = $this->ffmpegEncode( $file, $destinationFile, $options, 1 );
				if( $status ){
					$status = $this->ffmpegEncode( $file, $destinationFile, $options, 2 );
					// unlink the .log file used in two pass encoding: 
					wfSuppressWarnings();
					unlink( $destinationFile . '.log' );
					// Sometimes ffmpeg gives the file log-0.log extension 
					unlink( $destinationFile . 'log-0.log');
					wfRestoreWarnings();
				}
				// remove any log files
				$this->removeFffmpgeLogFiles( dirname( $destinationFile) );
				
			} else {
				$status = $this->ffmpegEncode( $file, $destinationFile, $options );
			}
		} else {
			wfDebug( 'Error unknown codec:' . $options['codec'] );
			$status =  'Error unknown target encode codec:' . $options['codec'];
		}
		
		// Do a quick check to confirm the job was not restarted or removed while we were transcoding
		// Confirm the in memory $jobStartTimeCache matches db start time
		$dbStartTime = $db->selectField( 'transcode', 'transcode_time_startwork',
			array( 	'transcode_image_name = ' . $db->addQuotes( $this->title->getDBKey() ),
					'transcode_key =' . $db->addQuotes( $transcodeKey )
			)
		);
		
		// Check for ( hopefully rare ) issue of or job restarted while transcode in progress
		if( $jobStartTimeCache != $dbStartTime ){
			wfDebug('Possible Error, transcode task restarted, removed, or completed while transcode was in progress');
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
			$status = rename( $destinationFile, $finalDerivativeFilePath );			
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
			WebVideoTranscode::invalidatePagesWithAsset( $this->title );
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
			// no need to invalidate cache. Because all pages remain valid ( no $transcodeKey derivative ) 
		}
		// Clear the webVideoTranscode cache ( so we don't keep out dated table cache around ) 
		webVideoTranscode::clearTranscodeCache( $this->title->getDBkey() );
		
		// pass along result status: 
		return $status;
	}
	function removeFffmpgeLogFiles( $dir ){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$ext = strtolower( pathinfo("$dir/$file", PATHINFO_EXTENSION) );
					if( $ext == '.log' ){
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
	function ffmpegEncode( $file, $target, $options, $pass=0 ){
		global $wgFFmpegLocation;	
		// Get the source
		$source = $file->getPath();
		$this->output( "Encode:\n source:$source\n target:$target\n" );
		
		// Set up the base command
		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . ' -i ' . wfEscapeShellArg( $source );
		
		if( isset($options['preset']) ){
			if ($options['preset'] == "360p") {
				$cmd.= " -vpre libvpx-360p";
			} else if ( $options['preset'] == "720p" ) {
				$cmd.= " -vpre libvpx-720p";
			} else if ( $options['preset'] == "1080p" ) {
				$cmd.= " -vpre libvpx-1080p";
			}
		}
		if ( isset( $options['novideo'] )  ) {
			$cmd.= " -vn ";
		} else {
			$cmd.= $this->ffmpegAddVideoOptions( $file, $target, $options, $pass );
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
		$cmd.= $this->ffmpegAddAudioOptions( $file, $target, $options, $pass );
		}	    	    

		// Output WebM
		$cmd.=" -f webm";
		
		if ( $pass != 0 ) {
			$cmd.=" -pass " .wfEscapeShellArg( $pass ) ;
			$cmd.=" -passlogfile " . wfEscapeShellArg( $target .'.log' );
		}
		// And the output target: 
		if ($pass==1) {
			$cmd.= ' /dev/null';
		} else{
			$cmd.= " $target";
		}	
		
		// Don't display shell output
		$cmd .= ' 2>&1';
		
		$this->output( "Running cmd: \n\n" .$cmd . "\n" );
		
		// Right before we output remove the old file
		wfProfileIn( 'ffmpeg_encode' );
		$shellOutput = wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg_encode' );

		if( $retval ){
			return $shellOutput;
		}
		return true;
	}
	function ffmpegAddVideoOptions( $file, $target, $options, $pass){
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
		// Check maxSize
		if (isset( $options['maxSize'] ) && intval( $options['maxSize'] ) > 0) {
			// Get size transform ( if maxSize is > file, file size is used:
			list( $width, $height ) = WebVideoTranscode::getMaxSizeTransform( $file, $options['maxSize'] );			      	
			$cmd.= ' -s ' . intval( $width ) . 'x' . intval( $height );
		} else if ( 
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

	function ffmpegAddAudioOptions( $file, $target, $options, $pass){
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
	function ffmpeg2TheoraEncode( $file, $target, $options){
		global $wgFFmpeg2theoraLocation;
		
		// Get the source:
		$source = $file->getPath();
		
		// Set up the base command
		$cmd = wfEscapeShellArg( $wgFFmpeg2theoraLocation ) . ' ' . wfEscapeShellArg( $source );
		// Add in the encode settings
		foreach( $options as $key => $val ){
			if( isset( self::$foggMap[$key] ) ){
				if( is_array(  self::$foggMap[$key] ) ){
					$cmd.= ' '. implode(' ', WebVideoTranscode::$foggMap[$key] );
				} else if ($val == 'true' || $val === true){
			 		$cmd.= ' '. self::$foggMap[$key];
				} else if ( $val === false){
					//ignore "false" flags
				} else {
					//normal get/set value
					$cmd.= ' '. self::$foggMap[$key] . ' ' . wfEscapeShellArg( $val );
				}
			}
		}		
		
		// Add the output target:
		$cmd.= ' -o ' . wfEscapeShellArg ( $target );
		
		// Don't display shell output
		$cmd.=' 2>&1';
		
		$this->output( "Running cmd: \n\n" .$cmd . "\n" );
		
		wfProfileIn( 'ffmpeg2theora_encode' );
		$shellOutput = wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg2theora_encode' );

		if( $retval ){
			return $shellOutput;
		}
		return true;
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
