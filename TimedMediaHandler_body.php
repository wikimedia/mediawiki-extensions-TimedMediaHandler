<?php

class TimedMediaHandler extends MediaHandler {
	static $magicDone = false;

	/**
	 * @return bool
	 */
	function isEnabled() {
		return true;
	}

	/**
	 * Get an image size array like that returned by getimagesize(), or false if it
	 * can't be determined.
	 * @param $file File
	 * @param $path string
	 * @param $metadata bool
	 * @return array|bool
	 */
	function getImageSize( $file, $path, $metadata = false ) {
		/* override by handler */
		return false;
	}

	/**
	 * Get the list of supported wikitext embed params
	 * @return array
	 */
	function getParamMap() {
		return array(
			'img_width' => 'width',
			'timedmedia_thumbtime' => 'thumbtime',
			'timedmedia_starttime' => 'start',
			'timedmedia_endtime' => 'end',
			'timedmedia_disablecontrols' => 'disablecontrols',
		);
	}

	/**
	 * Validate a embed file parameters
	 *
	 * @param $name {String} Name of the param
	 * @param $value {Mixed} Value to validated
	 * @return bool
	 */
	function validateParam( $name, $value ) {
		if ( $name == 'thumbtime' || $name == 'start' || $name == 'end' ) {
			if ( $this->parseTimeString( $value ) === false ) {
				return false;
			}
		} elseif ( $name == 'disablecontrols' ) {
			$values = explode( ',', $value);
			foreach($values as $v) {
				if ( !in_array( $v, array( 'options', 'timedText', 'fullscreen' ) ) ) {
					return false;
				}
			}
		} elseif( $name === 'width' || $name === 'height' ) {
			return $value > 0;
		}
		return true;
	}

	/**
	 * TODO we should really have "$file" available here to validate the param string
	 * @param $params array
	 * @return string
	 */
	function makeParamString( $params ) {
		// Add the width param string ( same as images {width}px )
		$paramString ='';
		$paramString.= ( isset( $params['width'] ) )?  $params['width'] . 'px' : '';
		$paramString.= ( $paramString != '' )? '-' : '';

		// Get the raw thumbTime from thumbtime or start param
		if ( isset ( $params['thumbtime'] ) ) {
			$thumbTime = $params['thumbtime'];
		} elseif ( isset ( $params['start'] ) ) {
			$thumbTime = $params['start'];
		} else {
			$thumbTime = false;
		}

		if ( $thumbTime !== false ) {
			$time = $this->parseTimeString( $thumbTime );
			if ( $time !== false ) {
				return $paramString. 'seek=' . $time;
			}
		}

		if ( !$paramString ) {
			$paramString = 'mid';
		}
		return $paramString ;
	}

	/**
	 * Used by thumb.php to find url parameters
	 *
	 * @param $str string
	 * @return array|bool Array of thumbnail parameters, or false if string cannot be parsed
	 */
	function parseParamString( $str ) {
		$params = array();
		if ( preg_match( '/^(mid|(\d*)px-)*(seek=([\d.]+))*$/', $str, $matches ) ) {
			$size = $thumbtime = null;
			if ( isset( $matches[2] ) ) {
				$size = $matches[2];
			}
			if ( isset( $matches[4] ) ) {
				$thumbtime = $matches[4];
			}

			if ( !is_null( $size ) && $size !== '' ) {
				$params['width'] = (int) $size;
			}
			if ( !is_null( $thumbtime ) ) {
				$params['thumbtime'] = (float) $thumbtime;
			}
			return $params; // valid thumbnail URL
		} else {
			// invalid parameter string
			return false;
		}
	}

	/**
	 * @param $image File
	 * @param $params array
	 * @return bool
	 */
	function normaliseParams( $image, &$params ) {
		$timeParam = array( 'thumbtime', 'start', 'end' );
		// Parse time values if endtime or thumbtime can't be more than length -1
		foreach($timeParam as $pn){
			if ( isset( $params[$pn] ) && $params[$pn] !== false ) {
				$length = $this->getLength( $image );
				$time = $this->parseTimeString( $params[$pn] );
				if ( $time === false ) {
					return false;
				} elseif ( $time > $length - 1 ) {
					$params[$pn] = $length - 1;
				} elseif ( $time <= 0 ) {
					$params[$pn] = 0;
				}
			}
		}

		if ( $this->isAudio( $image ) ) {
			// Assume a default for audio files
			$size = array(
				'width' => 220,
				'height' => 23,
			);
		} else {
			$size = array(
				'width' => $image->getWidth(),
				'height' => $image->getHeight(),
			);
		}
		// Make sure we don't try and up-scale the asset:
		if( isset( $params['width'] ) && (int)$params['width'] > $size['width'] ){
			$params['width'] = $size['width'];
		}

		if ( isset( $params['height'] ) && $params['height'] != -1 ) {
			if( $params['width'] * $size['height'] > $params['height'] * $size['width'] ) {
				$params['width'] = self::fitBoxWidth( $size['width'], $size['height'], $params['height'] );
			}
		}
		if ( isset( $params['width'] ) ) {
			$params['height'] = File::scaleHeight( $size['width'], $size['height'], $params['width'] );
		}

		// Make sure start time is not > than end time
		if( isset($params['start'])
			&& isset($params['end'] )
			&& $params['start'] !== false
			&& $params['end'] !== false
		) {
			if ( $this->parseTimeString( $params['start'] ) > $this->parseTimeString( $params['end'] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $parser Parser
	 * @param $file File
	 */
	function parserTransformHook( $parser, $file ) {
		$parserOutput = $parser->getOutput();
		if ( isset( $parserOutput->hasTimedMediaTransform ) ) {
			return ;
		}
		$parserOutput->hasTimedMediaTransform = true;
		$parserOutput->addOutputHook( 'TimedMediaHandler' );
	}

	/**
	 * Parser output hook only adds the PopUpMediaTransform
	 *
	 * The core embedPlayer module is part of a "loaderScript" so it does not need to
	 * be registered here.
	 *
	 * TODO move core loader to on-page script as to not include it on all pages.
	 *
	 * @param $outputPage OutputPage
	 * @param $parserOutput
	 * @param $data
	 */
	static function outputHook( $outputPage, $parserOutput, $data ) {
		// Add the PopUpMediaTransform code
		$outputPage->addModuleScripts( 'mw.PopUpMediaTransform' );
		$outputPage->addModuleStyles( 'mw.PopUpMediaTransform' );
		$outputPage->addModules( 'mw.TMHGalleryHook.js' );
	}

	/**
	 * Utility functions
	 * @param $timeString
	 * @param $length
	 * @return bool|int
	 */
	public static function parseTimeString( $timeString, $length = false ) {
		$parts = explode( ':', $timeString );
		$time = 0;
		// Check for extra :s
		if( count( $parts ) > 3 ){
			return false;
		}
		for ( $i = 0; $i < count( $parts ); $i++ ) {
			if ( !is_numeric( $parts[$i] ) ) {
				return false;
			}
			$time += floatval( $parts[$i] ) * pow( 60, count( $parts ) - $i - 1 );
		}

		if ( $time < 0 ) {
			wfDebug( __METHOD__.": specified negative time, using zero\n" );
			$time = 0;
		} elseif ( $length !== false && $time > $length - 1 ) {
			wfDebug( __METHOD__.": specified near-end or past-the-end time {$time}s, using end minus 1s\n" );
			$time = $length - 1;
		}
		return $time;
	}

	/**
	 * @static
	 * @param $timePassed
	 * @return string
	 */
	public static function getTimePassedMsg( $timePassed ){
		$t = array();
		$t['days'] = floor($timePassed/60/60/24);
		$t['hours'] = floor($timePassed/60/60)%24;
		$t['minutes'] = floor($timePassed/60)%60;
		$t['seconds'] = $timePassed%60;

		foreach( $t as $k => $v ){
			if($v == 0 ){
				unset( $t[$k] );
			} else {
				// Give grep a chance to find the usages:
				// timedmedia-days, timedmedia-hours, timedmedia-minutes,timedmedia-seconds
				$t[$k] = wfMessage( 'timedmedia-' . $k, $v )->text();
			}
		}
		if( count( $t ) == 0 ){
			$t = array( wfMessage( 'timedmedia-seconds', 0 )->text() ) ;
		}

		global $wgLang;
		return $wgLang->commaList( $t );
	}

	/**
	 * Converts seconds to Normal play time (NPT) time format:
	 * consist of hh:mm:ss.ms
	 * also see: http://www.ietf.org/rfc/rfc2326.txt section 3.6
	 *
	 * @param $time Number Seconds to be converted to npt time format
	 * @return bool|string
	 */
	public static function seconds2npt( $time ){
		if ( !is_numeric( $time ) ) {
			wfDebug( __METHOD__.": trying to get npt time on NaN:" + $time);
			return false;
		}
		if( $time < 0 ){
			wfDebug( __METHOD__.": trying to time on negative value:" + $time);
			return false;
		}
		$hours = floor( $time / 3600 );
		$min = floor( ( $time / 60 ) % 60 );
		$sec = ($time % 60 );
		$ms = ( $time - round( $time, 3) != 0 ) ? '.' .( $time - round( $time, 3) ) : '';

		return "{$hours}:{$min}:{$sec}{$ms}";
	}

 	/**
	 * @param $metadata
	 * @return bool|mixed
	 */
	function unpackMetadata( $metadata ) {
		wfSuppressWarnings();
		$unser = unserialize( $metadata );
		wfRestoreWarnings();
		if ( isset( $unser['version'] ) ) {
			return $unser;
		} else {
			return false;
		}
	}

	/**
	 * @param $image
	 * @param $metadata
	 * @return bool
	 */
	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	/**
	 * @param $ext
	 * @param $mime
	 * @param null $params
	 * @return array
	 */
	function getThumbType( $ext, $mime, $params = null ) {
		return array( 'jpg', 'image/jpeg' );
	}

	/**
	 * checks if a given file is an audio file
	 * @param $file File
	 * @return bool
	 */
	function isAudio( $file ){
		return ( $file->getWidth() == 0 && $file->getHeight() == 0 );
	}

	/**
	 * @param $file File
	 * @param $dstPath String
	 * @param $dstUrl String
	 * @param $params array
	 * @param $flags int
	 * @return bool|MediaTransformError|MediaTransformOutput|TimedMediaTransformOutput
	 */
	function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		# Important or height handling is wrong.
		if ( !$this->normaliseParams( $file, $params ) ) {
			return new TransformParameterError( $params );
		}

		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();

		// Audio should not be transformed by size, give it a default width and height
		if( $this->isAudio( $file ) ){
			$srcWidth = 220;
			$srcHeight = 23;
		}

		$params['width'] = isset( $params['width'] ) ? $params['width'] : $srcWidth;

		// if height overtakes width use height as max:
		$targetWidth = $params['width'];
		$targetHeight = $srcWidth == 0 ? $srcHeight : round( $params['width'] * $srcHeight / $srcWidth );
		if( isset( $params['height'] ) && $targetHeight > $params['height'] ){
			$targetHeight = $params['height'];
			$targetWidth = round( $params['height'] * $srcWidth / $srcHeight );
		}
		$options = array(
			'file' => $file,
			'length' => $this->getLength( $file ),
			'offset' => $this->getOffset( $file ),
			'width' => $targetWidth,
			'height' =>  $targetHeight,
			'isVideo' => !$this->isAudio( $file ),
			'thumbtime' => isset( $params['thumbtime'] ) ? $params['thumbtime'] : intval( $file->getLength() / 2 ),
			'start' => isset( $params['start'] ) ? $params['start'] : false,
			'end' => isset( $params['end'] ) ? $params['end'] : false,
			'fillwindow' => isset( $params['fillwindow'] ) ? $params['fillwindow'] : false,
			'disablecontrols' => isset ( $params['disablecontrols'] ) ? $params['disablecontrols'] : false
		);

		// No thumbs for audio
		if( !$options['isVideo'] ){
			return new TimedMediaTransformOutput( $options );
		}

		// Setup pointer to thumb arguments
		$options[ 'thumbUrl' ] = $dstUrl;
		$options[ 'dstPath' ] = $dstPath;
		$options[ 'path' ] = $dstPath;

		// Check if transform is deferred:
		if ( $flags & self::TRANSFORM_LATER ) {
			return new TimedMediaTransformOutput($options);
		}

		// Generate thumb:
		$thumbStatus = TimedMediaThumbnail::get( $options );
		if( $thumbStatus !== true ){
			return $thumbStatus;
		}

		return new TimedMediaTransformOutput( $options );
	}

	/**
	 * @param $file
	 * @return bool
	 */
	function canRender( $file ) { return true; }

	/**
	 * @param $file
	 * @return bool
	 */
	function mustRender( $file ) { return true; }

	/**
	 * Get a stream offset time
	 * @param $file
	 * @return int
	 */
	function getOffset( $file ){
		return 0;
	}

	/**
	 * Get length of a file
	 * @param $file
	 * @return int
	 */
	function getLength( $file ){
		return $file->getLength();
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getDimensionsString( $file ) {
		global $wgLang;

		if ( $file->getWidth() ) {
			return wfMessage( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ) )
				->numParams( $file->getWidth(), $file->getHeight() )->text();
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}

	public function filterThumbnailPurgeList( &$files, $options ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;

		$transcodeSet = array_merge($wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet);

		//dont remove derivatives on normal purge
		foreach(array_slice($files, 1) as $key => $file) {
			foreach( $transcodeSet as $transcodeKey ) {
				if ( preg_match('/' . preg_quote($transcodeKey) . '$/', $file) ) {
					unset($files[$key]);
					break;
				}
			}
		}
	}
}
