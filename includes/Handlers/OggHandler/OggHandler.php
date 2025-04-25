<?php

namespace MediaWiki\TimedMediaHandler\Handlers\OggHandler;

use File_Ogg;
use MediaWiki\Context\IContextSource;
use MediaWiki\FileRepo\File\File;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * ogg handler
 */
class OggHandler extends TimedMediaHandler {
	private const METADATA_VERSION = 2;

	/**
	 * @param File $image
	 * @param string $path
	 * @return string
	 */
	public function getMetadata( $image, $path ) {
		$metadata = [ 'version' => self::METADATA_VERSION ];

		try {
			$f = new File_Ogg( $path );
			$streams = [];
			foreach ( $f->listStreams() as $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					'@phan-var \File_Ogg_Media $stream';
					$streams[$streamID] = [
						'serial' => $stream->getSerial(),
						'group' => $stream->getGroup(),
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'size' => $stream->getSize(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					];
				}
			}
			$metadata['streams'] = $streams;
			$metadata['length'] = $f->getLength();
			// Get the offset of the file (in cases where the file is a segment copy)
			$metadata['offset'] = $f->getStartOffset();
		} catch ( OggException $e ) {
			// File not found, invalid stream, etc.
			$metadata['error'] = [
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			];
		}
		return serialize( $metadata );
	}

	/**
	 * Display metadata box on file description page.
	 *
	 * This is pretty basic, it puts data from all the streams together,
	 * and only outputs a couple of the most commonly used ogg "comments",
	 * with comments from all the streams combined
	 *
	 * @param File $file
	 * @param false|IContextSource $context Context to use (optional)
	 * @return array|false
	 */
	public function formatMetadata( $file, $context = false ) {
		$meta = $this->getCommonMetaArray( $file );
		if ( count( $meta ) === 0 ) {
			return false;
		}
		return $this->formatMetadataHelper( $meta, $context );
	}

	/**
	 * Get some basic metadata properties that are common across file types.
	 *
	 * @param File $file
	 * @return array Array of metadata. See MW's FormatMetadata class for format.
	 */
	public function getCommonMetaArray( File $file ) {
		$metadata = $file->getMetadataArray();
		if ( !$metadata || isset( $metadata['error'] ) || !isset( $metadata['streams'] ) ) {
			return [];
		}

		// See http://www.xiph.org/vorbis/doc/v-comment.html
		// http://age.hobba.nl/audio/mirroredpages/ogg-tagging.html
		$metadataMap = [
			'title' => 'ObjectName',
			'artist' => 'Artist',
			'performer' => 'Artist',
			'description' => 'ImageDescription',
			'license' => 'UsageTerms',
			'copyright' => 'Copyright',
			'organization' => 'dc-publisher',
			'date' => 'DateTimeDigitized',
			'location' => 'LocationDest',
			'contact' => 'Contact',
			'encoded_using' => 'Software',
			'encoder' => 'Software',
			// OpenSubtitles.org hash. Identifies source video.
			'source_ohash' => 'OriginalDocumentID',
			'comment' => 'UserComment',
			'language' => 'LanguageCode',
		];

		$props = [];

		foreach ( $metadata['streams'] as $stream ) {
			if ( isset( $stream['vendor'] ) ) {
				if ( !isset( $props['Software'] ) ) {
					$props['Software'] = [];
				}
				$props['Software'][] = trim( $stream['vendor'] );
			}
			if ( !isset( $stream['comments'] ) ) {
				continue;
			}
			foreach ( $stream['comments'] as $name => $rawValue ) {
				// $value will be an array if the file has
				// a multiple tags with the same name. Otherwise it
				// is a string.
				foreach ( (array)$rawValue as $value ) {
					$trimmedValue = trim( $value );
					if ( $trimmedValue === '' ) {
						continue;
					}
					$lowerName = strtolower( $name );
					if ( isset( $metadataMap[$lowerName] ) ) {
						$convertedName = $metadataMap[$lowerName];
						if ( !isset( $props[$convertedName] ) ) {
							$props[$convertedName] = [];
						}
						$props[$convertedName][] = $trimmedValue;
					}
				}
			}

		}
		// properties might be duplicated across streams
		foreach ( $props as &$type ) {
			$type = array_unique( $type );
			$type = array_values( $type );
		}

		return $props;
	}

	/**
	 * Get the "media size"
	 *
	 * @param File $file
	 * @param string $path
	 * @param false|string|array $metadata
	 * @return array|false
	 */
	public function getImageSize( $file, $path, $metadata = false ) {
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}

		if ( is_string( $metadata ) ) {
			$metadata = $this->unpackMetadata( $metadata );
		}

		if ( isset( $metadata['error'] ) || !isset( $metadata['streams'] ) ) {
			return false;
		}
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$mediaVideoTypes = $config->get( 'MediaVideoTypes' );
		foreach ( $metadata['streams'] as $stream ) {
			if ( in_array( $stream['type'], $mediaVideoTypes, true ) ) {
				$pictureWidth = $stream['header']['PICW'];
				$parNumerator = $stream['header']['PARN'];
				$parDenominator = $stream['header']['PARD'];
				if ( $parNumerator && $parDenominator ) {
					// Compensate for non-square pixel aspect ratios
					$pictureWidth = $pictureWidth * $parNumerator / $parDenominator;
				}
				return [
					(int)$pictureWidth,
					(int)$stream['header']['PICH']
				];
			}
		}
		return [ false, false ];
	}

	/**
	 * @param string|array $metadata
	 * @param bool $unserialize
	 * @return false|mixed
	 */
	public function unpackMetadata( $metadata, $unserialize = true ) {
		if ( $unserialize ) {
			// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			$metadata = @unserialize( $metadata );
		}

		if ( isset( $metadata['version'] ) && $metadata['version'] === self::METADATA_VERSION ) {
			return $metadata;
		}

		return false;
	}

	/**
	 * @param File $image
	 * @return string
	 */
	public function getMetadataType( $image ) {
		return 'ogg';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		$baseType = $this->isAudio( $file ) ? 'audio' : 'video';
		$baseType .= '/ogg';
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return $baseType;
		}
		$codecs = strtolower( implode( ", ", $streamTypes ) );
		return $baseType . '; codecs="' . $codecs . '"';
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $file->getMetadataArray();
		foreach ( $metadata['streams'] ?? [] as $stream ) {
			$streamTypes[] = $stream['type'];
		}
		return array_unique( $streamTypes );
	}

	/**
	 * @param File $file
	 * @return float
	 */
	public function getOffset( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['offset'] ?? 0.0 );
	}

	/**
	 * @param File $file
	 * @return float
	 */
	public function getLength( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['length'] ?? 0.0 );
	}

	/**
	 * Get useful response headers for GET/HEAD requests for a file with the given metadata
	 * @param array $metadata Contains this handler's unserialized getMetadata() for a file
	 * @return array
	 * @since 1.30
	 */
	public function getContentHeaders( $metadata ) {
		$result = [];

		if ( $metadata && !isset( $metadata['error'] ) && isset( $metadata['length'] ) ) {
			$result = [ 'X-Content-Duration' => (float)$metadata['length'] ];
		}

		return $result;
	}

	private function findStream( File $file, array $types ): ?array {
		$metadata = $file->getMetadataArray();
		foreach ( $metadata['streams'] ?? [] as $stream ) {
			if ( in_array( $stream['type'] ?? [], $types ) ) {
				return $stream;
			}
		}
		return null;
	}

	private function findVideoStream( File $file ): ?array {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$mediaVideoTypes = $config->get( 'MediaVideoTypes' );
		return $this->findStream( $file, $mediaVideoTypes );
	}

	private function findAudioStream( File $file ): ?array {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$mediaAudioTypes = $config->get( 'MediaAudioTypes' );
		return $this->findStream( $file, $mediaAudioTypes );
	}

	/**
	 * @param File $file
	 * @return float
	 */
	public function getFramerate( $file ) {
		$stream = $this->findVideoStream( $file );
		if ( $stream ) {
			return $stream['header']['FRN'] / $stream['header']['FRD'];
		}
		return 0.0;
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasVideo( $file ) {
		$stream = $this->findVideoStream( $file );
		return $stream !== null;
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasAudio( $file ) {
		$stream = $this->findAudioStream( $file );
		return $stream !== null;
	}

	/**
	 * @param File $file
	 * @return int
	 */
	public function getAudioChannels( $file ) {
		$stream = $this->findAudioStream( $file );
		$header = $stream['header'] ?? null;
		if ( isset( $header['vorbis_version'] ) ) {
			return (int)$header['audio_channels'];
		} elseif ( isset( $header['opus_version'] ) ) {
			return (int)$header['nb_channels'];
		} else {
			return 0;
		}
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$mediaVideoTypes = $config->get( 'MediaVideoTypes' );
		$mediaAudioTypes = $config->get( 'MediaAudioTypes' );
		if ( array_intersect( $streamTypes, $mediaVideoTypes ) ) {
			// Count multiplexed audio/video as video for short descriptions
			$msg = 'timedmedia-ogg-short-video';
		} elseif ( array_intersect( $streamTypes, $mediaAudioTypes ) ) {
			$msg = 'timedmedia-ogg-short-audio';
		} else {
			$msg = 'timedmedia-ogg-short-general';
		}
		return wfMessage(
			$msg,
			implode( '/', $streamTypes )
			)->timeperiodParams(
				$this->getLength( $file )
			)->text();
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			$unpacked = $this->unpackMetadata( $file->getMetadata() );
			if ( isset( $unpacked['error']['message'] ) ) {
				return wfMessage( 'timedmedia-ogg-long-error', $unpacked['error']['message'] )
					->sizeParams( $file->getSize() )
					->text();
			}
			return wfMessage( 'timedmedia-ogg-long-no-streams' )
				->sizeParams( $file->getSize() )
				->text();
		}
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$mediaVideoTypes = $config->get( 'MediaVideoTypes' );
		$mediaAudioTypes = $config->get( 'MediaAudioTypes' );
		if ( array_intersect( $streamTypes, $mediaVideoTypes ) ) {
			if ( array_intersect( $streamTypes, $mediaAudioTypes ) ) {
				$msg = 'timedmedia-ogg-long-multiplexed';
			} else {
				$msg = 'timedmedia-ogg-long-video';
			}
		} elseif ( array_intersect( $streamTypes, $mediaAudioTypes ) ) {
			$msg = 'timedmedia-ogg-long-audio';
		} else {
			$msg = 'timedmedia-ogg-long-general';
		}
		$size = 0;
		$unpacked = $this->unpackMetadata( $file->getMetadata() );
		if ( !$unpacked || isset( $unpacked['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			foreach ( $unpacked['streams'] as $stream ) {
				if ( isset( $stream['size'] ) ) {
					$size += $stream['size'];
				}
			}
		}
		return wfMessage(
			$msg,
			implode( '/', $streamTypes )
			)->timeperiodParams(
				$length
			)->bitrateParams(
				$this->getBitRate( $file )
			)->numParams(
				$file->getWidth(),
				$file->getHeight()
			)->sizeParams(
				$file->getSize()
			)->text();
	}

	/**
	 * @param File $file
	 * @return float|int
	 */
	public function getBitRate( $file ) {
		$size = 0;
		$unpacked = $this->unpackMetadata( $file->getMetadata() );
		if ( !$unpacked || isset( $unpacked['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			if ( isset( $unpacked['streams'] ) ) {
				foreach ( $unpacked['streams'] as $stream ) {
					if ( isset( $stream['size'] ) ) {
						$size += $stream['size'];
					}
				}
			}
		}
		return $length ? $size / $length * 8 : 0;
	}
}
