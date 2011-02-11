<?php 
class TimedMediaThumbnail {
	function get($file, $dstPath, $params, $width, $height){
		global $wgFFmpegLocation, $wgOggThumbLocation;

		$length = $this->getLength( $file );
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
		wfMkdirParents( dirname( $dstPath ) );

		wfDebug( "Creating video thumbnail at $dstPath\n" );

		// First check for oggThumb
		if( $wgOggThumbLocation && is_file( $wgOggThumbLocation ) ){
			$cmd = wfEscapeShellArg( $wgOggThumbLocation ) .
				' -t '. intval( $thumbtime ) . ' ' .
				' -n ' . wfEscapeShellArg( $dstPath ) . ' ' .
				' ' . wfEscapeShellArg( $file->getPath() ) . ' 2>&1';
			$returnText = wfShellExec( $cmd, $retval );
			//check if it was successful or if we should try ffmpeg:
			if ( !$this->removeBadFile( $dstPath, $retval ) ) {
				return true;
			}
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

		if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
			#re-attempt encode command on frame time 1 and with mapping (special case for chopped oggs)
			$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -map 0:1 '.
			' -ss 1 ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';
			$retval = 0;
			$returnText = wfShellExec( $cmd, $retval );
		}

		if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
			#No mapping, time zero. A last ditch attempt.
			$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -ss 0 ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';

			$retval = 0;
			$returnText = wfShellExec( $cmd, $retval );
			// If still bad return error:
			if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
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
		}
		//if we did not return an error return true to continue media thum display
		return true;
	}