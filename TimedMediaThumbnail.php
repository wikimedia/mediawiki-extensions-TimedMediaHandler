<?php 
class TimedMediaThumbnail {
	
	static function get( $options ){
		global $wgFFmpegLocation, $wgOggThumbLocation;
		$thumbtime = self::getThumbTime( $options );
		
		// Set up lodal pointer to file
		$file = $options['file'];
		if( !is_dir( dirname( $options['dstPath'] ) ) ){
			wfMkdirParents( dirname( $options['dstPath'] ) );
		}

		wfDebug( "Creating video thumbnail at" .  $options['dstPath']  . "\n" );
		
		// If ogg try OggThumb:
		if( $options['file']->getHandler()->getMetadataType() == 'ogg' ){ 
			if( self::tryOggThumb( $options) ){
				return true;
			}
		}
		// Else try ffmpeg and return result:
		return self::tryFfmpegThumb( $options );
	}
	/**
	 * Try to render a thumbnail using oggThumb:
	 *
	 * @param $file {Object} File object
	 * @param $dstPath {string} Destination path for the rendered thumbnail
	 * @param $dstPath {array} Thumb rendering parameters ( like size and time )
	 */
	static function tryOggThumb( $options ){
		global $wgOggThumbLocation;
		
		// Check for ogg format file and $wgOggThumbLocation 
		if( !$wgOggThumbLocation 
			|| !is_file( $wgOggThumbLocation ) 
		){
			return false;
		}
		
		$cmd = wfEscapeShellArg( $wgOggThumbLocation ) .
			' -t '. intval( $options['thumbtime'] ) . ' ' .
			' -n ' . wfEscapeShellArg( $options['dstPath'] ) . ' ' .
			' ' . wfEscapeShellArg( $options['file']->getPath() ) . ' 2>&1';
		$returnText = wfShellExec( $cmd, $retval );
		
		// Check if it was successful
		if ( !$options['file']->getHandler()->removeBadFile( $options['dstPath'], $retval ) ) {
			return true;
		}
		return false;
	}
	
	static function tryFfmpegThumb( $options ){
		global $wgFFmpegLocation;
		if( !$wgFFmpegLocation || !is_file( $wgFFmpegLocation ) ){
			return false;
		}
		
		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -i ' . wfEscapeShellArg( $options['file']->getPath() ) .
			' -ss ' . intval( $options['thumbtime'] ) .
			# MJPEG, that's the same as JPEG except it's supported by the windows build of ffmpeg
			# No audio, one frame
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $options['dstPath'] ) . ' 2>&1';

		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );
		// Check if it was successful
		if ( !$options['file']->getHandler()->removeBadFile( $options['dstPath'], $retval ) ) {
			return true;
		}
		// Filter nonsense
		$lines = explode( "\n", str_replace( "\r\n", "\n", $returnText ) );
		if ( substr( $lines[0], 0, 6 ) == 'FFmpeg' ) {
			for ( $i = 1; $i < count( $lines ); $i++ ) {
				if ( substr( $lines[$i], 0, 2 ) != '  ' ) {
					break;
				}
			}
			$lines = array_slice( $lines, $i );
		}
		// Return error box
		return new MediaTransformError( 'thumbnail_error', $options['width'], $options['height'], implode( "\n", $lines ) );
	}

	static function getThumbTime( $options ){		
		$length = $options['file']->getLength();
		$thumbtime = false;
		if ( $options['thumbtime'] ) {
		 	return TimedMediaHandler::parseTimeString( $options['thumbtime'], $length );
		}
		// If start time param isset use that for the thumb:
		if( $options['start'] ) {
			return TimedMediaHandler::parseTimeString( $options['start'], $length );
		}else{
			// Seek to midpoint by default, it tends to be more interesting than the start
			return $length / 2;
		}		
	}
}