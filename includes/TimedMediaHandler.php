<?php

namespace MediaWiki\TimedMediaHandler;

use MediaHandler;
use MediaTransformError;
use MediaTransformOutput;
use MediaWiki\Context\RequestContext;
use MediaWiki\FileRepo\File\File;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\Parser;
use TransformParameterError;

class TimedMediaHandler extends MediaHandler {

	/**
	 * Get an image size array like that returned by getimagesize(), or false if it
	 * can't be determined.
	 * @param File $file
	 * @param string $path
	 * @param string|false $metadata
	 * @return array|false
	 */
	public function getImageSize( $file, $path, $metadata = false ) {
		/* override by handler */
		return false;
	}

	/**
	 * Get the list of supported wikitext embed params
	 * @return array
	 */
	public function getParamMap() {
		return [
			'img_width' => 'width',
			'timedmedia_thumbtime' => 'thumbtime',
			'timedmedia_starttime' => 'start',
			'timedmedia_endtime' => 'end',
			'timedmedia_disablecontrols' => 'disablecontrols',
			'timedmedia_loop' => 'loop',
			'timedmedia_muted' => 'muted',
		];
	}

	/**
	 * Validate a embed file parameters
	 *
	 * @param string $name Name of the param
	 * @param mixed $value Value to validated
	 * @return bool
	 */
	public function validateParam( $name, $value ) {
		if ( $name === 'thumbtime' || $name === 'start' || $name === 'end' ) {
			if ( self::parseTimeString( $value ) === false ) {
				return false;
			}
		} elseif ( $name === 'disablecontrols' ) {
			$values = explode( ',', $value );
			foreach ( $values as $v ) {
				if ( !in_array( $v, [ 'options', 'timedText', 'fullscreen' ] ) ) {
					return false;
				}
			}
		} elseif ( $name === 'width' || $name === 'height' ) {
			return $value > 0;
		}
		return true;
	}

	/**
	 * TODO we should really have "$file" available here to validate the param string
	 * @param array $params
	 * @return string
	 */
	public function makeParamString( $params ) {
		// Add the width param string ( same as images {width}px )
		$paramString = ( isset( $params['width'] ) ) ? $params['width'] . 'px' : '';
		$paramString .= ( $paramString !== '' ) ? '-' : '';

		// Get the raw thumbTime from thumbtime or start param
		if ( isset( $params['thumbtime'] ) ) {
			$thumbTime = $params['thumbtime'];
		} elseif ( isset( $params['start'] ) ) {
			$thumbTime = $params['start'];
		} else {
			$thumbTime = false;
		}

		if ( $thumbTime !== false ) {
			$time = self::parseTimeString( $thumbTime );
			if ( $time !== false ) {
				return $paramString . 'seek=' . $time;
			}
		}

		if ( !$paramString ) {
			$paramString = 'mid';
		}
		return $paramString;
	}

	/**
	 * Used by thumb.php to find url parameters
	 *
	 * @param string $str
	 * @return array|false Array of thumbnail parameters, or false if string cannot be parsed
	 */
	public function parseParamString( $str ) {
		$params = [];
		if ( preg_match( '/^(mid|(\d*)px-)*(seek=([\d.]+))*$/', $str, $matches ) ) {
			$size = $thumbtime = null;
			if ( isset( $matches[2] ) ) {
				$size = $matches[2];
			}
			if ( isset( $matches[4] ) ) {
				$thumbtime = $matches[4];
			}

			if ( $size !== null && $size !== '' ) {
				$params['width'] = (int)$size;
			}
			if ( $thumbtime !== null ) {
				$params['thumbtime'] = (float)$thumbtime;
			}
			// valid thumbnail URL
			return $params;
		}
		// invalid parameter string
		return false;
	}

	/**
	 * @param File $image
	 * @param array &$params
	 * @return bool
	 */
	public function normaliseParams( $image, &$params ) {
		$timeParam = [ 'thumbtime', 'start', 'end' ];
		// Parse time values if endtime or thumbtime can't be more than length -1
		foreach ( $timeParam as $pn ) {
			if ( isset( $params[$pn] ) && $params[$pn] !== false ) {
				$length = $this->getLength( $image );
				$time = self::parseTimeString( $params[$pn] );
				if ( $time === false ) {
					return false;
				}

				if ( $time > $length - 1 ) {
					$params[$pn] = $length - 1;
				} elseif ( $time <= 0 ) {
					$params[$pn] = 0;
				}
			}
		}

		if ( $this->isAudio( $image ) ) {
			// Assume a default for audio files
			$size = [
				'width' => 220,
				'height' => 23,
			];
		} else {
			$size = [
				'width' => $image->getWidth(),
				'height' => $image->getHeight(),
			];
		}
		// Make sure we don't try and up-scale the asset:
		if ( !$this->isAudio( $image ) && isset( $params['width'] )
			&& (int)$params['width'] > $size['width']
		) {
			$params['width'] = $size['width'];
		}

		if ( isset( $params['height'] ) && $params['height'] !== -1 ) {
			if ( $params['width'] * $size['height'] > $params['height'] * $size['width'] ) {
				$params['width'] = self::fitBoxWidth( $size['width'], $size['height'], $params['height'] );
			}
		}
		if ( isset( $params['width'] ) ) {
			$params['height'] = File::scaleHeight( $size['width'], $size['height'], $params['width'] );
		}

		// Make sure start time is not > than end time
		if (
			isset( $params['start'] ) && isset( $params['end'] ) &&
			$params['start'] !== false &&
			$params['end'] !== false &&
			( self::parseTimeString( $params['start'] ) > self::parseTimeString( $params['end'] ) )
		) {
			return false;
		}

		foreach ( [ 'loop', 'muted' ] as $flag ) {
			$params[ $flag ] = isset( $params[ $flag ] );
		}
		return true;
	}

	/**
	 * Parser output hook only adds the required modules
	 *
	 * The core embedPlayer module lazy loaded by the loader modules
	 *
	 * @param Parser $parser
	 * @param ?File $file
	 */
	public function parserTransformHook( $parser, $file ) {
		$parserOutput = $parser->getOutput();
		if ( $parserOutput->getExtensionData( 'mw_ext_TMH_hasTimedMediaTransform' ) ) {
			return;
		}

		$parserOutput->addModuleStyles( [ 'ext.tmh.player.styles' ] );
		$parserOutput->addModules( [ 'ext.tmh.player' ] );

		$parserOutput->setExtensionData( 'mw_ext_TMH_hasTimedMediaTransform', true );
	}

	/**
	 * Utility functions
	 * @param string $timeString
	 * @param false|int $length
	 * @return false|int
	 */
	public static function parseTimeString( $timeString, $length = false ) {
		$parts = explode( ':', $timeString );
		$time = 0;
		$partsCount = count( $parts );
		// Check for extra :s
		if ( $partsCount > 3 ) {
			return false;
		}
		foreach ( $parts as $i => $iValue ) {
			if ( !is_numeric( $iValue ) ) {
				return false;
			}
			$time += (float)$iValue * pow( 60, $partsCount - $i - 1 );
		}

		if ( $time < 0 ) {
			wfDebug( __METHOD__ . ": specified negative time, using zero\n" );
			return 0;
		}
		// We don't need more than millisecond precisions
		// And for duration (length) seconds precision is ok
		$time = $length ? ceil( $time ) : round( $time, 3 );
		if ( $length !== false && $time > $length - 1 ) {
			wfDebug( __METHOD__ .
				": specified near-end or past-the-end time {$time}s, using end minus 1s\n" );
			$time = $length - 1;
		}
		return $time;
	}

	/**
	 * Converts seconds to Normal play time (NPT) time format:
	 * consist of hh:mm:ss.ms
	 * also see: http://www.ietf.org/rfc/rfc2326.txt section 3.6
	 *
	 * @param int $time Seconds to be converted to npt time format
	 * @return false|string
	 */
	public static function seconds2npt( $time ) {
		if ( !is_numeric( $time ) ) {
			wfDebug( __METHOD__ . ": trying to get npt time on NaN: " . $time );
			return false;
		}
		if ( $time < 0 ) {
			wfDebug( __METHOD__ . ": trying to time on negative value: " . $time );
			return false;
		}
		$hours = floor( $time / 3600 );
		$min = floor( $time / 60 ) % 60;
		$sec = floor( $time ) % 60;
		$ms = floor( $time * 1000 ) % 1000;
		$ms = ( $ms != 0 ) ? sprintf( '.%03d', $ms ) : '';

		return sprintf( '%02d:%02d:%02d%s', $hours, $min, $sec, $ms );
	}

	/**
	 * @param string $metadata
	 * @return false|mixed
	 */
	public function unpackMetadata( $metadata ) {
		// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) ) {
			return $unser;
		}

		return false;
	}

	/**
	 * @param File $image
	 * @param string $metadata
	 * @return bool
	 */
	public function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	/**
	 * @param string $ext
	 * @param string $mime
	 * @param null $params
	 * @return array
	 */
	public function getThumbType( $ext, $mime, $params = null ) {
		return [ 'jpg', 'image/jpeg' ];
	}

	/**
	 * checks if a given file is an audio file
	 * @param File $file
	 * @return bool
	 */
	public function isAudio( $file ) {
		return ( !$file->getWidth() && !$file->getHeight() );
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasVideo( $file ) {
		return false;
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasAudio( $file ) {
		return false;
	}

	/**
	 * Audio channel count, or 0 if no audio.
	 * Fractional subwoofer channels are counted as a whole, so
	 * eg "5.1 surround" is 6 channels.
	 *
	 * @param File $file
	 * @return int
	 */
	public function getAudioChannels( $file ) {
		return 0;
	}

	/**
	 * @param File $file
	 * @param string $dstPath
	 * @param string $dstUrl
	 * @param array $params
	 * @param int $flags
	 * @return bool|MediaTransformError|MediaTransformOutput|TimedMediaTransformOutput
	 */
	public function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		# Important or height handling is wrong.
		if ( !$this->normaliseParams( $file, $params ) ) {
			return new TransformParameterError( $params );
		}

		$options = [
			'file' => $file,
			'length' => $this->getLength( $file ),
			'offset' => $this->getOffset( $file ),
			// Default thumbnail width and height for audio files is hardcoded to match the dimensions of
			// the filetype icon, see TimedMediaTransformOutput::getUrl(). Overridden for video below.
			'width' => $params['width'] ?? 120,
			// Height is ignored for audio files anyway, and $params['height'] might be set to 0
			'height' => $params['width'] ?? 120,
			'isVideo' => !$this->isAudio( $file ),
			'thumbtime' => $params['thumbtime'] ?? (int)( $file->getLength() / 2 ),
			'start' => $params['start'] ?? false,
			'end' => $params['end'] ?? false,
			'fillwindow' => $params['fillwindow'] ?? false,
			'disablecontrols' => $params['disablecontrols'] ?? false,
			'loop' => $params['loop'] ?? false,
			'muted' => $params['muted'] ?? false,
			'inline' => $params['inline'] ?? false,
		];

		// Allow start and end query string params on image pages (T203994)
		if ( isset( $params['imagePageParams'] ) ) {
			$requestParams = $params['imagePageParams'];
			if ( !$options['start'] ) {
				$options['start'] = $requestParams[ 'start' ] ?? false;
			}
			if ( !$options['end'] ) {
				$options['end'] = $requestParams[ 'end' ] ?? false;
			}
		}

		// No thumbs for audio
		if ( !$options['isVideo'] ) {
			return new TimedMediaTransformOutput( $options );
		}

		// We're dealing with a video file now, set width and height
		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();

		$params['width'] ??= $srcWidth;

		// if height overtakes width use height as max:
		$targetWidth = $params['width'];
		$targetHeight = $srcWidth ? round( $params['width'] * $srcHeight / $srcWidth ) : $srcHeight;
		if ( isset( $params['height'] ) && $targetHeight > $params['height'] ) {
			$targetHeight = $params['height'];
			$targetWidth = round( $params['height'] * $srcWidth / $srcHeight );
		}

		$options[ 'width' ] = $targetWidth;
		$options[ 'height' ] = $targetHeight;

		// Setup pointer to thumb arguments
		$options[ 'thumbUrl' ] = $dstUrl;
		$options[ 'dstPath' ] = $dstPath;
		$options[ 'path' ] = $dstPath;

		// Check if transform is deferred:
		if ( $flags & self::TRANSFORM_LATER ) {
			return new TimedMediaTransformOutput( $options );
		}

		// Generate thumb:
		$timedMediaThumbnail = MediaWikiServices::getInstance()
			->getService( 'TimedMediaHandler.TimedMediaThumbnail' );
		$thumbStatus = $timedMediaThumbnail->get( $options );
		if ( $thumbStatus !== true ) {
			return $thumbStatus;
		}

		return new TimedMediaTransformOutput( $options );
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function mustRender( $file ) {
		return true;
	}

	/**
	 * Get a stream offset time
	 * @param File $file
	 * @return float
	 */
	public function getOffset( $file ) {
		return 0.0;
	}

	/**
	 * Get length of a file
	 * @param File $file
	 * @return float
	 */
	public function getLength( $file ) {
		return $file->getLength();
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getDimensionsString( $file ) {
		if ( $file->getWidth() ) {
			return wfMessage(
				'video-dims'
				)->timeperiodParams(
					$this->getLength( $file )
				)->numParams(
					$file->getWidth(),
					$file->getHeight()
				)->text();
		}

		$lang = RequestContext::getMain()->getLanguage();
		return $lang->formatTimePeriod( $this->getLength( $file ) );
	}

	/**
	 * Return frame rate, if applicable, or 0 if no valid data.
	 * Subclasses will implement relevant metadata extraction.
	 *
	 * Note that values returned as floating point are not exact for
	 * NTSC/ATSC video with 30000/1001, 60000/1001, or 24000/1001
	 * frame rates!
	 *
	 * Note interlacing should be checked separately if relevant.
	 *
	 * @param File $file
	 * @return float
	 */
	public function getFramerate( $file ) {
		return 0.0;
	}

	/**
	 * Returns true if the file contains an interlaced video track.
	 * @param File $file
	 * @return bool
	 */
	public function isInterlaced( $file ) {
		return false;
	}
}
