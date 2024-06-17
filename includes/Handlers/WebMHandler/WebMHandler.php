<?php

namespace MediaWiki\TimedMediaHandler\Handlers\WebMHandler;

use File;
use MediaWiki\Context\IContextSource;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * WebM handler
 */
class WebMHandler extends ID3Handler {

	/**
	 * @param string $path
	 * @return array
	 */
	protected function getID3( $path ) {
		$id3 = parent::getID3( $path );
		// Unset some parts of id3 that are too detailed and matroska specific:
		// @todo include the basic file codec and other metadata too?
		if ( isset( $id3['matroska'] ) ) {
			if ( isset( $id3['matroska']['comments'] ) ) {
				$comments = $id3['matroska']['comments'];
				$id3['matroska'] = [
					'comments' => $comments
				];
			} else {
				unset( $id3['matroska'] );
			}
		}
		return $id3;
	}

	/**
	 * Get the "media size"
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

		if ( isset( $metadata['error'] ) ) {
			return false;
		}

		$size = [ false, false ];
		// display_x/display_y is only set if DisplayUnit
		// is pixels, otherwise display_aspect_ratio is set
		if ( isset( $metadata['video']['display_x'] ) && isset( $metadata['video']['display_y'] ) ) {
			$size = [
				$metadata['video']['display_x'],
				$metadata['video']['display_y']
			];
		} elseif ( isset( $metadata['video']['resolution_x'] ) && isset( $metadata['video']['resolution_y'] ) ) {
			$size = [
				$metadata['video']['resolution_x'],
				$metadata['video']['resolution_y']
			];
			if ( isset( $metadata['video']['crop_top'] ) ) {
				$size[1] -= $metadata['video']['crop_top'];
			}
			if ( isset( $metadata['video']['crop_bottom'] ) ) {
				$size[1] -= $metadata['video']['crop_bottom'];
			}
			if ( isset( $metadata['video']['crop_left'] ) ) {
				$size[0] -= $metadata['video']['crop_left'];
			}
			if ( isset( $metadata['video']['crop_right'] ) ) {
				$size[0] -= $metadata['video']['crop_right'];
			}
		}
		if ( $size[0] && $size[1] && isset( $metadata['video']['display_aspect_ratio'] ) ) {
			// for wide images (i.e. 16:9) take native height as base
			if ( $metadata['video']['display_aspect_ratio'] >= 1 ) {
				$size[0] = (int)( $size[1] * $metadata['video']['display_aspect_ratio'] );
			} else {
				// for tall images (i.e. 9:16) take width as base
				$size[1] = (int)( $size[0] / $metadata['video']['display_aspect_ratio'] );
			}
		}
		return $size;
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getMetadataType( $file ) {
		return 'webm';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		$baseType = ( !$file->getWidth() && !$file->getHeight() ) ? 'audio' : 'video';

		$streams = $this->getStreamTypes( $file );
		if ( !$streams ) {
			return $baseType . '/webm';
		}

		$codecs = strtolower( implode( ', ', $streams ) );

		return $baseType . '/webm; codecs="' . $codecs . '"';
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		$videoFormat = $metadata[ 'video' ][ 'dataformat' ] ?? false;
		if ( $videoFormat === 'vp8' ) {
			// id3 gives 'V_VP8' for what we call VP8
			$streamTypes[] = 'VP8';
		} elseif ( $videoFormat === 'vp9'
			|| $videoFormat === 'V_VP9'
		) {
			// Currently getID3 calls it V_VP9. That will probably change to vp9
			// once getID3 actually gets support for the codec.
			$streamTypes[] = 'VP9';
		} elseif ( $videoFormat === 'V_AV1' ) {
			$streamTypes[] = 'AV1';
		}

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'vorbis' ) {
			$streamTypes[] = 'Vorbis';
		} elseif ( $audioFormat === 'opus'
			|| $audioFormat === 'A_OPUS'
		) {
			// Currently getID3 calls it A_OPUS. That will probably change to 'opus'
			// once getID3 actually gets support for the codec.
			$streamTypes[] = 'Opus';
		}

		return $streamTypes;
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
		return wfMessage(
			'timedmedia-webm-short-video',
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
			return parent::getLongDesc( $file );
		}
		return wfMessage(
			'timedmedia-webm-long-video',
			implode( '/', $streamTypes )
			)->timeperiodParams(
				$this->getLength( $file )
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
	 * Display metadata box on file description page.
	 *
	 * @param File $file
	 * @param false|IContextSource $context Context to use (optional)
	 * @return array|false
	 */
	public function formatMetadata( $file, $context = false ) {
		$meta = $this->getCommonMetaArray( $file );
		if ( !$meta ) {
			return false;
		}

		return $this->formatMetadataHelper( $meta, $context );
	}

	/**
	 * Get file metadata as an array
	 *
	 * Reuse MediaWiki's support for image metadata so
	 * translate WebM metadata keywords to equivalent exif values
	 * @see http://wiki.webmproject.org/webm-metadata/global-metadata
	 *
	 * @param File $file
	 * @return array|false
	 */
	public function getCommonMetaArray( File $file ) {
		$metadata = $file->getMetadata();

		if ( is_string( $metadata ) ) {
			$metadata = $this->unpackMetadata( $metadata );
		}

		if ( isset( $metadata['error'] ) ) {
			return false;
		}

		if ( !$metadata ) {
			return false;
		}

		$props = [];

		if ( isset( $metadata['matroska']['comments'] ) ) {
			$comments = $metadata['matroska']['comments'];
			// Map comments from getid3's matroska handler to output format
			// Localization of labels by FormatMetadata...
			// Based on http://wiki.webmproject.org/webm-metadata/global-metadata
			// and https://matroska.org/technical/tagging.html
			// matched against descriptions in language/i18n/exif/qqq.json
			$map = [
				'muxingapp' => 'Software',
				'writingapp' => 'Software',
				'title' => 'ObjectName',
				'summary' => 'ImageDescription',
				'synopsis' => 'ImageDescription',
				'artist' => 'Artist',
				'publisher' => 'dc-publisher',
				'genre' => 'dc-type',
				'content_type' => 'dc-type',
				'keywords' => 'Keywords',
				'law_rating' => 'ContentWarning',
				'date_released' => 'DateTimeReleased',
				'date_recorded' => 'DateTimeOriginal',
				'date_encoded' => 'DateTimeDigitized',
				'date_digitized' => 'DateTimeDigitized',
				'date_tagged' => 'DateTimeMetadata',
				'date_purchased' => 'dc-date',
				'date_written' => 'dc-date',
				'comment' => 'UserComment',
				'rating' => 'Rating',
				'encoder' => 'Software',
				'copyright' => 'Copyright',
				'production_copyright' => 'Copyright',
				'license' => 'UsageTerms',
				'terms_of_use' => 'UsageTerms',
				'catalog_number' => 'Identifier',
				'url' => 'Identifier',
				'isrc' => 'Identifier',
				'isbn' => 'Identifier',
				'barcode' => 'Identifier',
				'lccn' => 'Identifier',
				'imdb' => 'Identifier',
				'tmdb' => 'Identifier',
				'tvdb' => 'Identifier',
				'tvdb2' => 'Identifier',
				// Not official but seen in files
				'language' => 'LanguageCode',
				'webstatement' => 'WebStatement',
				'licenseurl' => 'LicenseUrl'
			];
			foreach ( $map as $commentTag => $propTag ) {
				if ( isset( $comments[$commentTag] ) ) {
					if ( !isset( $props[$propTag] ) ) {
						$props[$propTag] = [];
					}
					$props[$propTag] = array_merge( (array)$comments[$commentTag], $props[$propTag] );
				}
			}
			foreach ( $props as &$tag ) {
				// We put multiple similar tags
				// under same name, which sometimes
				// results in duplication.
				$tag = array_unique( $tag );
			}
		}

		return $props;
	}

}
