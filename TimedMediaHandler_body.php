<?php

// TODO: Fix core printable stylesheet. Descendant selectors suck.

class TimedMediaHandler extends MediaHandler {

	static $magicDone = false;

	function isEnabled() {
		return true;
	}
	
	function getImageSize( $file, $path, $metadata = false ) {
		/* override by handler */	
	}
	
	/**
	 * Get the list of supported wikitext embed params
	 */
	function getParamMap() {
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		return array(
			'img_width' => 'width',
			'timedmedia_thumbtime' => 'thumbtime',
			'timedmedia_starttime'	=> 'start',
			'timedmedia_endtime'	=> 'end',
		);
	}
	/**
	 * Validate a embed file parameters
	 * 
	 * @param $name {String} Name of the param
	 * @param $value {Mixed} Value to validated 
	 */
	function validateParam( $name, $value ) {
		if ( $name == 'thumbtime' || $name == 'start' || $name == 'end' ) {
			if ( $this->parseTimeString( $value ) === false ) {
				return false;
			}
		}
		return true;
	}
	
	function makeParamString( $params ) {
		if ( isset( $params['thumbtime'] ) ) {
			$time = $this->parseTimeString( $params['thumbtime'] );
			if ( $time !== false ) {
				return 'seek=' . $time;
			}
		}
		return 'mid';
	}

	function parseParamString( $str ) {
		$m = false;
		if ( preg_match( '/^seek=(\d+)$/', $str, $m ) ) {
			return array( 'thumbtime' => $m[0] );
		}
		return array();
	}

	function normaliseParams( $image, &$params ) {
		$timeParam = array('thumbtime', 'start', 'end');
		// Parse time values if endtime or thumbtime can't be more than length -1
		foreach($timeParam as $pn){
			if ( isset( $params[$pn] ) ) {
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
		// Make sure start time is not > than end time
		if(isset($params['start']) && isset($params['end']) ){
			if($params['start'] > $params['end'])
				return false;
		}

		return true;
	}
	function parserTransformHook( $parser, $file ) {
		if ( isset( $parser->mOutput->hasTimedMediaTransform ) ) {
			return;
		}
		$parser->mOutput->hasTimedMediaTransform = true;
		$parser->mOutput->addOutputHook( 'TimedMediaHandler' );
	}
	/**
	 * Output hook only adds the PopUpMediaTransform 
	 * 
	 * The core embedPlayer module is part of a "loaderScript" so it does not need to 
	 * be registered here. 
	 */
	static function outputHook( $outputPage, $parserOutput, $data ) {
		// Add the PopUpMediaTransform code 
		$outputPage->addModules( 'PopUpMediaTransform' );
		$outputPage->addModuleStyles( 'PopUpMediaTransform' );
	}
	/**
	 * Utility functions
	 */
	public static function parseTimeString( $seekString, $length = false ) {
		$parts = explode( ':', $seekString );
		$time = 0;
		for ( $i = 0; $i < count( $parts ); $i++ ) {
			if ( !is_numeric( $parts[$i] ) ) {
				return false;
			}
			$time += intval( $parts[$i] ) * pow( 60, count( $parts ) - $i - 1 );
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

	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}
	function getThumbType( $ext, $mime, $params = null ) {
		return array( 'jpg', 'image/jpeg' );
	}
	// checks if a given file is an audio file
	function isAudio( $file ){
		return ( $file->getWidth() == 0 && $file->getHeight() == 0 );
	}
	function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		global $wgFFmpegLocation, $wgEnabledDerivatives;
	
		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();
	
		$baseConfig = array(
			'file' => $file,
			'length' => $this->getLength( $file ),
			'offset' => $this->getOffset( $file ),
			'width' => $params['width'],
			'height' =>  $srcWidth == 0 ? $srcHeight : $params['width']* $srcHeight / $srcWidth,
			'isVideo' => !$this->isAudio( $file ),
			'thumbtime' => ( isset( $params['thumbtime'] ) )? $params['thumbtime'] : false,
			'start' => ( isset( $params['start'] ) )? $params['start'] : false,
			'end' => ( isset( $params['end'] ) )? $params['end'] : false
		);
		
		// No thumbs for audio
		if( $baseConfig['isVideo'] === false ){			
			return new TimedMediaTransformOutput( $baseConfig );
		}

		// Setup pointer to thumb arguments
		$baseConfig['thumbUrl'] = $dstUrl;
		$baseConfig['dstPath'] = $dstPath;
		
		// Check if transform is deferred:
		if ( $flags & self::TRANSFORM_LATER ) {
			return new TimedMediaTransformOutput($baseConfig);
		}

		// Generate thumb:
		$thumbStatus = TimedMediaThumbnail::get( $baseConfig );
		if( $thumbStatus !== true ){
			return $thumbStatus;
		}
	
		return new TimedMediaTransformOutput( $baseConfig );
	}

	function canRender( $file ) { return true; }
	function mustRender( $file ) { return true; }

	// Get a stream offset time
	function getOffset( $file ){
		return 0;
	}

	function getDimensionsString( $file ) {
		global $wgLang;

		if ( $file->getWidth() ) {
			return wfMsg( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ),
				$wgLang->formatNum( $file->getWidth() ),
				$wgLang->formatNum( $file->getHeight() ) );
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}
}