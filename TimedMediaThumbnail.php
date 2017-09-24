<?php
class TimedMediaThumbnail {

	/**
	 * @param array $options
	 * @return bool|MediaTransformError
	 */
	static function get( $options ) {
		if ( !is_dir( dirname( $options['dstPath'] ) ) ) {
			wfMkdirParents( dirname( $options['dstPath'] ), null, __METHOD__ );
		}

		wfDebug( "Creating video thumbnail at " .  $options['dstPath']  . "\n" );
		if (
			isset( $options['width'] ) && isset( $options['height'] ) &&
			$options['width'] != $options['file']->getWidth() &&
			$options['height'] != $options['file']->getHeight()
		) {
			return self::resizeThumb( $options );
		}
		// try OggThumb, and fallback to ffmpeg
		$result = self::tryOggThumb( $options );
		if ( $result === false ) {
			return self::tryFfmpegThumb( $options );
		}
		return $result;
	}

	/**
	 * Run oggThumb to generate a still image from a video file, using a frame
	 * close to the given number of seconds from the start.
	 *
	 * @param array $options
	 * @return bool|MediaTransformError
	 *
	 */
	static function tryOggThumb( $options ) {
		global $wgOggThumbLocation;

		// Check that the file is 'ogg' format
		if ( $options['file']->getHandler()->getMetadataType( $options['file'] ) != 'ogg' ) {
			return false;
		}

		// Check for $wgOggThumbLocation
		if ( !$wgOggThumbLocation || !is_file( $wgOggThumbLocation ) ) {
			return false;
		}

		$time = self::getThumbTime( $options );
		$dstPath = $options['dstPath'];
		$videoPath = $options['file']->getLocalRefPath();

		$cmd = wfEscapeShellArg( $wgOggThumbLocation )
			. ' -t ' . floatval( $time );
		// Set the output size if set in options:
		if ( isset( $options['width'] ) && isset( $options['height'] ) ) {
			$cmd .= ' -s ' . intval( $options['width'] ) . 'x' . intval( $options['height'] );
		}
		$cmd .= ' -n ' . wfEscapeShellArg( $dstPath ) .
			' ' . wfEscapeShellArg( $videoPath ) . ' 2>&1';
		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );

		if ( $options['file']->getHandler()->removeBadFile( $dstPath, $retval ) || $retval ) {
			// oggThumb spams both stderr and stdout with useless progress
			// messages, and then often forgets to output anything when
			// something actually does go wrong. So interpreting its output is
			// a challenge.
			$lines = explode( "\n", str_replace( "\r\n", "\n", $returnText ) );
			if ( count( $lines ) > 0
				&& preg_match( '/invalid option -- \'n\'$/', $lines[0] )
			) {
				$returnText = wfMessage( 'timedmedia-oggThumb-version', '0.9' )->inContentLanguage()->text();
			} else {
				$returnText = wfMessage( 'timedmedia-oggThumb-failed' )->inContentLanguage()->text();
			}
			return new MediaTransformError( 'thumbnail_error',
				$options['width'], $options['height'], $returnText );
		}
		return true;
	}

	/**
	 * @param array $options
	 * @return bool|MediaTransformError
	 */
	static function tryFfmpegThumb( $options ) {
		global $wgFFmpegLocation, $wgMaxShellMemory;

		if ( !$wgFFmpegLocation || !is_file( $wgFFmpegLocation ) ) {
			return false;
		}

		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . ' -threads 1 ';

		$offset = intval( self::getThumbTime( $options ) );
		/*
		This is a workaround until ffmpegs ogg demuxer properly seeks to keyframes.
		Seek N seconds before offset and seek in decoded stream after that.
		 -ss before input seeks without decode
		 -ss after input seeks in decoded stream

		 N depends on framerate of input, keyframe interval defaults
		 to 64 for most encoders, seeking a bit before that
		 */

		$framerate = $options['file']->getHandler()->getFramerate( $options['file'] );
		if ( $framerate > 0 ) {
			$seekoffset = 1 + intval( 64 / $framerate );
		} else {
			$seekoffset = 3;
		}

		if ( $offset > $seekoffset ) {
			$cmd .= ' -ss ' . floatval( $offset - $seekoffset );
			$offset = $seekoffset;
		}

		// try to get temorary local url to file
		$backend = $options['file']->getRepo()->getBackend();
		// getFileHttpUrl was only added in mw 1.21, dont fail if it does not exist
		if ( method_exists( $backend, 'getFileHttpUrl' ) ) {
			$src = $backend->getFileHttpUrl( [
				'src' => $options['file']->getPath()
			] );
		} else {
			$src = null;
		}
		if ( $src == null ) {
			$src = $options['file']->getLocalRefPath();
		}

		$cmd .= ' -y -i ' . wfEscapeShellArg( $src );
		$cmd .= ' -ss ' . $offset . ' ';

		// Set the output size if set in options:
		if ( isset( $options['width'] ) && isset( $options['height'] ) ) {
			$cmd .= ' -s '. intval( $options['width'] ) . 'x' . intval( $options['height'] );
		}

		// MJPEG, that's the same as JPEG except it's supported by the windows build of ffmpeg
		// No audio, one frame
		$cmd .= ' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $options['dstPath'] ) . ' 2>&1';

		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );
		// Check if it was successful
		if ( !$options['file']->getHandler()->removeBadFile( $options['dstPath'], $retval ) ) {
			return true;
		}
		$returnText = $cmd . "\nwgMaxShellMemory: $wgMaxShellMemory\n" . $returnText;
		// Return error box
		return new MediaTransformError(
			'thumbnail_error', $options['width'], $options['height'], $returnText
		);
	}

	/**
	 * @param array $options
	 * @return bool|MediaTransformError
	 */
	static function resizeThumb( $options ) {
		$file = $options['file'];
		$params = [];
		foreach ( [ 'start', 'thumbtime' ] as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$params[ $key ] = $options[ $key ];
			}
		}
		$params["width"] = $file->getWidth();
		$params["height"] = $file->getHeight();

		$poolKey = $file->getRepo()->getSharedCacheKey( 'file', md5( $file->getName() ) );
		$posOptions = array_flip( [ 'start', 'thumbtime' ] );
		$poolKey = wfAppendQuery( $poolKey, array_intersect_key( $options, $posOptions ) );

		if ( class_exists( 'PoolCounterWorkViaCallback' ) ) {
			$work = new PoolCounterWorkViaCallback( 'TMHTransformFrame',
				'_tmh:frame:' . $poolKey,
				[ 'doWork' => function () use ( $file, $params ) {
					return $file->transform( $params, File::RENDER_NOW );
				} ] );
			$thumb = $work->execute();
		} else {
			$thumb = $file->transform( $params, File::RENDER_NOW );
		}

		if ( !$thumb || $thumb->isError() ) {
			return $thumb;
		}
		$src = $thumb->getStoragePath();
		if ( !$src ) {
			return false;
		}
		$thumbFile = new UnregisteredLocalFile( $file->getTitle(),
			RepoGroup::singleton()->getLocalRepo(), $src, false );
		$thumbParams = [
			"width" => $options['width'],
			"height" => $options['height']
		];
		$handler = $thumbFile->getHandler();
		if ( !$handler ) {
			return false;
		}
		$scaledThumb = $handler->doTransform(
			$thumbFile,
			$options['dstPath'],
			$options['dstPath'],
			$thumbParams
		);

		if ( !$scaledThumb || $scaledThumb->isError() ) {
			return $scaledThumb;
		}
		return true;
	}

	/**
	 * @param array $options
	 * @return bool|float|int
	 */
	static function getThumbTime( $options ) {
		$length = $options['file']->getLength();

		// If start time param isset use that for the thumb:
		if ( isset( $options['start'] ) ) {
			$thumbtime = TimedMediaHandler::parseTimeString( $options['start'], $length );
			if ( $thumbtime !== false ) {
				return $thumbtime;
			}
		}
		// else use thumbtime
		if ( isset( $options['thumbtime'] ) ) {
			$thumbtime = TimedMediaHandler::parseTimeString( $options['thumbtime'], $length );
			if ( $thumbtime !== false ) {
				return $thumbtime;
			}
		}
		// Seek to midpoint by default, it tends to be more interesting than the start
		return $length / 2;
	}
}
