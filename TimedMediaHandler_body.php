<?php

// TODO: Fix core printable stylesheet. Descendant selectors suck.

class TimedMediaHandler extends MediaHandler {
	const OGG_METADATA_VERSION = 2;

	static $magicDone = false;

	function isEnabled() {
		return true;
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
	/**
	 * Get the "media size" 
	 *
	 */	 
	function getImageSize( $file, $path, $metadata = false ) {
		global $wgMediaVideoTypes;
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );
		if ( isset( $metadata['error'] ) || !isset( $metadata['streams'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			if ( in_array( $stream['type'], $wgMediaVideoTypes ) ) {
				return array(
					$stream['header']['PICW'],
					$stream['header']['PICH']
				);
			}
		}
		return array( false, false );
	}

	function getMetadata( $image, $path ) {
		// Get the $image type: 
		print_r( $image );
		die();
		
		$metadata = array( 'version' => self::OGG_METADATA_VERSION );

		if ( !class_exists( 'File_Ogg' ) ) {
			require( 'File/Ogg.php' );
		}
		try {
			$f = new File_Ogg( $path );
			$streams = array();
			foreach ( $f->listStreams() as $streamType => $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					$streams[$streamID] = array(
						'serial' => $stream->getSerial(),
						'group' => $stream->getGroup(),
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'size' => $stream->getSize(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					);
				}
			}
			$metadata['streams'] = $streams;
			$metadata['length'] = $f->getLength();
			// Get the offset of the file (in cases where the file is a segment copy)
			$metadata['offset'] = $f->getStartOffset();
		} catch ( PEAR_Exception $e ) {
			// File not found, invalid stream, etc.
			$metadata['error'] = array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}
		return serialize( $metadata );
	}

	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::OGG_METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}

	function getMetadataType( $image ) {
		return 'ogg';
	}

	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	function getThumbType( $ext, $mime, $params = null ) {
		return array( 'jpg', 'image/jpeg' );
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
			'isVideo' => ( $srcHeight != 0 && $srcWidth != 0 )
		);
		// No thumbs for audio
		if( $baseConfig['isVideo'] === false ){			
			return new TimedMediaTransformOutput( $baseConfig );
		}

		// Setup pointer to thumb url: 
		$baseConfig['thumbUrl'] = $dstUrl;
		
		// Check if transform is deferred:
		if ( $flags & self::TRANSFORM_LATER ) {
			return new TimedMediaTransformOutput($baseConfig);
		}

		// Generate thumb:
		$thumbStatus = TimedMediaThumbnail::gennerateThumb( $file, $dstPath, $params, $width, $height );
		if( $thumbStatus !== true ){
			return $thumbStatus;
		}
	
		return new TimedMediaTransformOutput( $baseConfig );
	}
		
	function canRender( $file ) { return true; }
	function mustRender( $file ) { return true; }

	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['length'];
		}
	}
	function getOffset( $file ){
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) || !isset( $metadata['offset']) ) {
			return 0;
		} else {
			return $metadata['offset'];
		}
	}

	function getStreamTypes( $file ) {
		$streamTypes = '';
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			$streamTypes[$stream['type']] = true;
		}
		return array_keys( $streamTypes );
	}

	function getShortDesc( $file ) {
		global $wgLang, $wgMediaAudioTypes, $wgMediaVideoTypes;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		if ( array_intersect( $streamTypes, $wgMediaVideoTypes ) ) {
			// Count multiplexed audio/video as video for short descriptions
			$msg = 'timedmedia-short-video';
		} elseif ( array_intersect( $streamTypes, $wgMediaAudioTypes ) ) {
			$msg = 'timedmedia-short-audio';
		} else {
			$msg = 'timedmedia-short-general';
		}
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) );
	}

	function getLongDesc( $file ) {
		global $wgLang, $wgMediaVideoTypes, $wgMediaAudioTypes;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			$unpacked = $this->unpackMetadata( $file->getMetadata() );
			return wfMsg( 'timedmedia-long-error', $unpacked['error']['message'] );
		}
		if ( array_intersect( $streamTypes,$wgMediaVideoTypes  ) ) {
			if ( array_intersect( $streamTypes, $wgMediaAudioTypes ) ) {
				$msg = 'timedmedia-long-multiplexed';
			} else {
				$msg = 'timedmedia-long-video';
			}
		} elseif ( array_intersect( $streamTypes, $wgMediaAudioTypes ) ) {
			$msg = 'timedmedia-long-audio';
		} else {
			$msg = 'timedmedia-long-general';
		}
		$size = 0;
		$unpacked = $this->unpackMetadata( $file->getMetadata() );
		if ( !$unpacked || isset( $metadata['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			foreach ( $unpacked['streams'] as $stream ) {
				if( isset( $stream['size'] ) )
					$size += $stream['size'];
			}
		}
		$bitrate = $length == 0 ? 0 : $size / $length * 8;
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $length ),
			$wgLang->formatBitrate( $bitrate ),
			$wgLang->formatNum( $file->getWidth() ),
			$wgLang->formatNum( $file->getHeight() )
	   	);
	}

	function getDimensionsString( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		if ( $file->getWidth() ) {
			return wfMsg( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ),
				$wgLang->formatNum( $file->getWidth() ),
				$wgLang->formatNum( $file->getHeight() ) );
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}

	static function getMyScriptPath() {
		global $wgScriptPath;
		return "$wgScriptPath/extensions/TimedMediaHandler";
	}

	function setHeaders( $out ) {
		
	}

	function parserTransformHook( $parser, $file ) {
		if ( isset( $parser->mOutput->hasOggTransform ) ) {
			return;
		}
		$parser->mOutput->hasOggTransform = true;
		$parser->mOutput->addOutputHook( 'TimedMediaHandler' );
	}

	static function outputHook( $outputPage, $parserOutput, $data ) {
		$instance = MediaHandler::getHandler( 'application/ogg' );
		if ( $instance ) {
			$instance->setHeaders( $outputPage );
		}
	}	
}