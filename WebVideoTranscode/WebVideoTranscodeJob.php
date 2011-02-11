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
	// Run the transcode request
	public function run() {
		// Get the file object
		$file = wfLocalFile( $this->title );		
		$transcodeKey = $this->params['transcodeKey'];
		
		// Build the destination target
		$destinationFile = WebVideoTranscode::getTargetEncodePath( $file, $transcodeKey );
		
		$options = WebVideoTranscode::$derivativeSettings[ $transcodeKey ];		
	
		// Check the codec see which encode method to call;
		if( $options['codec'] == 'theora' ){
			$status = $this->ffmpeg2TheoraEncode( $file, $destinationFile, $options );
		} else if( $options['codec'] == 'vp8' ){			
			// Check for twopass:
			if( isset( $options['twopass'] ) ){
				$status = $this->ffmpegEncode( $file, $destinationFile, $options, 1 );
				if( $status ){
					$status = $this->ffmpegEncode( $file, $destinationFile, $options, 2 );
				}	
			} else {
				$this->ffmpegEncode( $file, $destinationFile, $options );
			}
			
		} else {
			wfDebug( 'Error unknown codec:' . $options['codec'] );
			$status = false;
		}
		
		return $status;
	}
	
	/** Utility helper for ffmpeg and ffmpeg2theora mapping **/
	
	function ffmpegEncode( $file, $target, $options, $pass=0 ){
		global $wgFFmpegLocation;	
		// Get the source
		$source = $file->getFullPath();
		
		// Set up the base command
		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . ' ' . wfEscapeShellArg( $source );
		
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
	    
	    
	    if ( $pass == 1 || $options['noaudio'] ) {
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
			$cmd.= $target;
		}
		
		print "Running cmd: \n\n" .$cmd . "\n\n" ;
		
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
		$cmd.="-y -skip_threshold 0 -rc_buf_aggressivity 0 -bufsize 6000k -rc_init_occupancy 4000 -threads 4";
		
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
			$cmd.= " -vp " . wfEscapeShellArg( $options['videoBitrate'] );
		}		
		// Set the codec:
		$cmd.= " -vcodec libvpx";
		
		die( $file->getWidth() + ':' + $file->getHeight() );
		
		// Check for aspect ratio		
		if ($options['aspect']) {
			$aspectRatio = $options['aspect'];
		} else {
			$aspectRatio = $file->getWidth() + ':' + $file->getHeight();
		}
		
		$dar = $aspectRatio.split(':');
		$dar = intval( $aspectRatio[0] ) /  intval( $aspectRatio[1] );
		
		// Check maxSize
		if (isset( $options['maxSize'] ) && intval( $options['maxSize'] ) > 0) {
			$sourceWidth = $file->getWidth();
			$sourceHeight =$file->getHeight();
			if ($sourceWidth > $options['maxSize'] ) {
				$width = intval( $options['maxSize'] );
				$height = intval( $width / $dar);				
			} else {
				$height = intval( $options['maxSize'] );
				$width = intval( $height * $dar);
	      	}
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
		global $wgffmpeg2theoraLocation;
		
		// Get the source:
		$source = $file->getFullPath();
		
		// Set up the base command
		$cmd = wfEscapeShellArg( $wgffmpeg2theoraLocation ) . ' ' . wfEscapeShellArg( $source );
		
		// Add in the encode settings
		foreach( $options as $key => $val){
			if( isset( WebVideoTranscode::$foggMap[$key] ) ){
				if( is_array(  WebVideoTranscode::$foggMap[$key] ) ){
					$cmd.= ' '. implode(' ', WebVideoTranscode::$foggMap[$key] );
				}else if($val == 'true' || $val === true){
			 		$cmd.= ' '. WebVideoTranscode::$foggMap[$key];
				}else if( $val === false){
					//ignore "false" flags
				}else{
					//normal get/set value
					$cmd.= ' '. WebVideoTranscode::$foggMap[$key] . ' ' . wfEscapeShellArg( $val );
				}
			}
		}
		die( "run\n\n" . $cmd. "\n");
		// Add the output target:
		$cmd.= ' -o ' . wfEscapeShellArg ( $target );
		print "Running cmd: \n\n" .$cmd . "\n\n" ;
		
		wfProfileIn( 'ffmpeg2theora_encode' );
		wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg2theora_encode' );

		if( $retval ){
			return false;
		}
		return true;
	}
}