<?php

namespace MediaWiki\TimedMediaHandler;

use File;
use MediaTransformError;
use MediaWiki\MediaWikiServices;
use PoolCounterWorkViaCallback;
use UnregisteredLocalFile;

class TimedMediaThumbnail {

	/**
	 * @param array $options
	 * @return bool|MediaTransformError
	 */
	public static function get( $options ) {
		if ( !is_dir( dirname( $options['dstPath'] ) ) ) {
			wfMkdirParents( dirname( $options['dstPath'] ), null, __METHOD__ );
		}

		wfDebug( "Creating video thumbnail at " . $options['dstPath'] . "\n" );
		if (
			isset( $options['width'] ) && isset( $options['height'] ) &&
			$options['width'] != $options['file']->getWidth() &&
			$options['height'] != $options['file']->getHeight()
		) {
			return self::resizeThumb( $options );
		}
		return self::tryFfmpegThumb( $options );
	}

	/**
	 * @param array $options
	 * @return bool|MediaTransformError
	 */
	private static function tryFfmpegThumb( $options ) {
		global $wgFFmpegLocation, $wgMaxShellMemory;

		if ( !$wgFFmpegLocation || !is_file( $wgFFmpegLocation ) ) {
			return false;
		}

		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) . ' -nostdin -threads 1 ';

		$file = $options['file'];
		$handler = $file->getHandler();

		$offset = (int)self::getThumbTime( $options );
		/*
		This is a workaround until ffmpegs ogg demuxer properly seeks to keyframes.
		Seek N seconds before offset and seek in decoded stream after that.
		 -ss before input seeks without decode
		 -ss after input seeks in decoded stream

		 N depends on framerate of input, keyframe interval defaults
		 to 64 for most encoders, seeking a bit before that
		 */

		$framerate = $handler->getFramerate( $file );
		if ( $framerate > 0 ) {
			$seekoffset = 1 + (int)( 64 / $framerate );
		} else {
			$seekoffset = 3;
		}

		if ( $offset > $seekoffset ) {
			$cmd .= ' -ss ' . (float)( $offset - $seekoffset );
			$offset = $seekoffset;
		}

		// try to get temporary local url to file
		$backend = $file->getRepo()->getBackend();

		$src = $backend->getFileHttpUrl( [
			'src' => $file->getPath()
		] );
		if ( $src === null ) {
			$src = $file->getLocalRefPath();
		}

		$cmd .= ' -y -i ' . wfEscapeShellArg( $src );
		$cmd .= ' -ss ' . $offset . ' ';

		// Deinterlace MPEG-2 if necessary
		if ( $handler->isInterlaced( $file ) ) {
			// Send one frame only
			$cmd .= ' -vf yadif=mode=0';
		}

		// Set the output size if set in options:
		if ( isset( $options['width'] ) && isset( $options['height'] ) ) {
			$cmd .= ' -s ' . (int)$options['width'] . 'x' . (int)$options['height'];
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
	private static function resizeThumb( $options ) {
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

		$work = new PoolCounterWorkViaCallback( 'TMHTransformFrame',
			'_tmh:frame:' . $poolKey,
			[ 'doWork' => static function () use ( $file, $params ) {
				return $file->transform( $params, File::RENDER_NOW );
			} ] );
		$thumb = $work->execute();

		if ( !$thumb || $thumb->isError() ) {
			return $thumb;
		}
		$src = $thumb->getStoragePath();
		if ( !$src ) {
			return false;
		}
		$localRepo = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();
		$thumbFile = new UnregisteredLocalFile( $file->getTitle(),
			$localRepo, $src, false );
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
			// @phan-suppress-next-line PhanTypeMismatchReturnNullable
			return $scaledThumb;
		}
		return true;
	}

	/**
	 * @param array $options
	 * @return bool|float|int
	 */
	private static function getThumbTime( $options ) {
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
