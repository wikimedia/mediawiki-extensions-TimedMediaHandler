<?php 
class TimedMediaThumbnail {
	
	function get( $file, $dstPath, $params, $width, $height){
		global $wgFFmpegLocation, $wgOggThumbLocation;
		$thumbtime = self::getThumbTime($file, $params );
		
		wfMkdirParents( dirname( $dstPath ) );

		wfDebug( "Creating video thumbnail at $dstPath\n" );
		
		$width = ( isset( $params['width'] ) 
					&& 
				 $params['width'] > 0 
				 	&& 
				 $params['width'] < $file->getWidth() 
		) ? $params['width'] : $file->getWidth();
		
		$height = ( isset( $params['height'] ) 
					&&
				  $params['height'] > 0 
					&& 
		 		  $params['height'] < $file->getHeight() 
		 ) ? $params['height'] : $file->getHeight();
		
		// If ogg try OggThumb: 
		if( self::tryOggThumb($file, $dstPath, $width, $height, $thumbtime ) ){
			return true;
		}
		// Else try and return the ffmpeg thumbnail attempt:
		return self::tryFfmpegThumb($file, $dstPath, $width, $height, $thumbtime );
	}
	/**
	 * Try to render a thumbnail using oggThumb:
	 *
	 * @param $file {Object} File object
	 * @param $dstPath {string} Destination path for the rendered thumbnail
	 * @param $dstPath {array} Thumb rendering parameters ( like size and time )
	 */
	function tryOggThumb($file, $dstPath, $width, $height, $thumbtime ){
		global $wgOggThumbLocation;
		
		// Check for ogg format file and $wgOggThumbLocation 
		if( !$file->getHandler()->getMetadataType() == 'ogg' 
			|| !$wgOggThumbLocation 
			|| !is_file( $wgOggThumbLocation ) 
		){
			return false;
		}
		
		$cmd = wfEscapeShellArg( $wgOggThumbLocation ) .
			' -t '. intval( $thumbtime ) . ' ' .
			' -n ' . wfEscapeShellArg( $dstPath ) . ' ' .
			' ' . wfEscapeShellArg( $file->getPath() ) . ' 2>&1';
		$returnText = wfShellExec( $cmd, $retval );
		
		// Check if it was successful
		if ( !$file->getHandler()->removeBadFile( $dstPath, $retval ) ) {
			return true;
		}
		return false;
	}
	
	function tryFfmpegThumb($file, $dstPath, $width, $height, $thumbtime ){
		global $wgFFmpegLocation;
		if( !$wgFFmpegLocation || !is_file( $wgFFmpegLocation ) ){
			return false;
		}
	
		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -ss ' . intval( $thumbtime ) . ' ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			# MJPEG, that's the same as JPEG except it's supported by the windows build of ffmpeg
			# No audio, one frame
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';

		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );
		
		// Check if it was successful
		if ( !$file->getHandler()->removeBadFile( $dstPath, $retval ) ) {
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
		return new MediaTransformError( 'thumbnail_error', $width, $height, implode( "\n", $lines ) );
	}

	function getThumbTime( $file, $params ){
		
		$length = $file->getLength();
		$thumbtime = false;
		if ( isset( $params['thumbtime'] ) ) {
			$thumbtime = TimedMediaHandler::parseTimeString( $params['thumbtime'], $length );
		}
		if ( $thumbtime === false ) {
			// If start time param isset use that for the thumb:
			if( isset( $params['start'] ) ){
				$thumbtime = TimedMediaHandler::parseTimeString( $params['start'], $length );
			}else{
				# Seek to midpoint by default, it tends to be more interesting than the start
				$thumbtime = $length / 2;
			}
		}
		return $thumbtime;
	}
}