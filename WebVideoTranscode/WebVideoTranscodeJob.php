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
 * 1) non-free media transcode ( dealys the media file being inserted, adds note to talk page once ready)
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
		
		$source = $file->getFullPath();
		if( !is_file($source ) ){
			$this->output( 'File not found: ' . $this->title );
			return false;
		}
		$transcodeKey = $this->params['transcodeKey'];
		
		// Build the destination target
		$destinationFile = WebVideoTranscode::getTargetEncodePath( $file, $transcodeKey );
		
		if( ! isset(  WebVideoTranscode::$derivativeSettings[ $transcodeKey ] )){
			$this->output("Transcode key $transcodeKey not found, skipping");
			return false;
		}
		$options = WebVideoTranscode::$derivativeSettings[ $transcodeKey ];
				
		$this->output( "Encoding to codec: " . $options['codec'] );
		// Check the codec see which encode method to call;
		if( $options['codec'] == 'theora' ){
			$status = $this->ffmpeg2TheoraEncode( $file, $destinationFile, $options );
		} else if( $options['codec'] == 'vp8' ){			
			// Check for twopass:
			if( isset( $options['twopass'] ) ){
				// ffmpeg requires manual two pass
				$status = $this->ffmpegEncode( $file, $destinationFile, $options, 1 );
				if( $status ){
					$status = $this->ffmpegEncode( $file, $destinationFile, $options, 2 );
				}
				// remove any log files
				$this->removeFffmpgeLogFiles( dirname( $destinationFile) );
				
			} else {
				$status = $this->ffmpegEncode( $file, $destinationFile, $options );
			}
		} else {
			wfDebug( 'Error unknown codec:' . $options['codec'] );
			$status = false;
		}
		
		// If status is oky move the file to its final destination. ( timedMediaHandler will look for it there ) 
		// XXX would be nice to clear the cache for the pages where the title in use
		if( $status ){
			wfSuppressWarnings();
			$status = rename($destinationFile, WebVideoTranscode::getDerivativeFilePath( $file, $transcodeKey) );
			wfRestoreWarnings();
		}
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
		$source = $file->getFullPath();
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
		wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg_encode' );

		if( $retval ){
			return false;
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
		
		// Check for aspect ratio		
		if ( isset( $options['aspect'] ) ) {
			$aspectRatio = $options['aspect'];
		} else {
			$aspectRatio = $file->getWidth() . ':' . $file->getHeight();
		}
		$dar = explode(':', $aspectRatio);
		$dar = intval( $dar[0] ) / intval( $dar[1] );
		
		// Check maxSize
		if (isset( $options['maxSize'] ) && intval( $options['maxSize'] ) > 0) {
			// Check if source is smaller than maxSize
			if( !WebVideoTranscode::isTargetLargerThanFile( $options['maxSize'], $file ) ){
				$sourceWidth = $file->getWidth();
				$sourceHeight = $file->getHeight();
				if ($sourceWidth > $options['maxSize'] ) {
					$width = intval( $options['maxSize'] );
					$height = intval( $width / $dar);				
				} else {
					$height = intval( $options['maxSize'] );
					$width = intval( $height * $dar);
		      	}		      	
				$cmd.= ' -s ' . intval( $width ) . 'x' . intval( $height );
			}
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
		global $wgffmpeg2theoraLocation;
		
		// Get the source:
		$source = $file->getFullPath();
		
		// Set up the base command
		$cmd = wfEscapeShellArg( $wgffmpeg2theoraLocation ) . ' ' . wfEscapeShellArg( $source );
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
		wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg2theora_encode' );

		if( $retval ){
			return false;
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