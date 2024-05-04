<?php

namespace MediaWiki\TimedMediaHandler\Handlers\WebMHandler;

use MediaWiki\Context\IContextSource;
use MediaWiki\FileRepo\File\File;
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
	 * @inheritDoc
	 */
	protected function getSizeFromMetadata( $metadata ) {
		// Just return the size of the first video stream
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
	 * Returns the metadata type for the given file.
	 *
	 * @param File $file The file object.
	 * @return string 'webm' (indicating the file type is WebM).
	 */
	public function getMetadataType( $file ) {
		return 'webm';
	}

	/**
	 * Returns the MIME type for the file, including codecs information.
	 *
	 * @param File $file The file object.
	 * @return string The MIME type including the codecs.
	 */
	public function getWebType( $file ) {
		// Determine the base type (audio or video) based on file dimensions
		$baseType = ( !$file->getWidth() && !$file->getHeight() ) ? 'audio' : 'video';

		// Get the stream types (codecs) from the file metadata
		$streams = $this->getStreamTypes( $file );
		if ( !$streams ) {
			// Return the base type with WebM format if no streams are found
			return $baseType . '/webm';
		}

		// Process codecs: Keep AV1 in original case, convert others to lowercase
		$processedStreams = array_map( function ( $codec ) use ( $file ) {
			if ( $codec === 'AV1' ) {
				return $this->getAV1CodecString( $file );
			}
			return strtolower( $codec );
		}, $streams );

		// Combine processed streams into a single codec string
		$codecs = implode( ', ', $processedStreams );
		return $baseType . '/webm; codecs="' . $codecs . '"';
	}

	/**
	 * Generates an AV1 codec string from metadata using a simplified constraints table
	 * derived from the AV1 specification. This method attempts to determine the AV1 level
	 * by comparing the video's resolution and frame rate against predefined maximums.
	 *
	 * IMPORTANT: This implementation uses a reduced set of AV1 levels (02, 03, 04, 05, 06),
	 * not all intermediate levels found in the specification. The constraints are simplified
	 * and may not fully reflect the official AV1 specification.
	 *
	 * NOTE: For full compliance with the AV1 spec, you would need to consider additional
	 * parameters such as decode rate, header rate, tiles, and bitrate-based tier assignment.
	 * This code focuses on resolution and display rate constraints only.
	 *
	 * The AV1 codec string format is: "av01.P.LT.BB"
	 *  - P   = Profile (0,1,2). Default: '0' (Main) unless profile info is provided.
	 *  - LT  = Level and Tier combined. For example, level 2.0 with Main tier → '02M'.
	 *  - BB  = Bit depth (08,10,12). Default: '08' unless metadata specifies otherwise.
	 *
	 * @param File $file The file for which we want the codecstring
	 * @return string|false The generated AV1 codec string in the format "av01.P.LT.BB".
	 */
	private function getAV1CodecString( File $file ) {
		$metadata = $file->getMetadataArray();
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		$video = isset( $metadata['video'] ) && is_array( $metadata['video'] ) ? $metadata['video'] : [];

		// Default values
		// Assume Main profile if not specified
		$profile = '0';
		// Default bit depth: 8-bit
		$bitDepth = '08';
		// Default tier: Main (unless specified otherwise)
		$tier = 'M';

		// Determine bit depth from metadata, if available
		// @fixme GetID3 will return a `bits_per_sample` track entry
		// for a `BitDepth` item however ffmpeg does not appear to emit them
		// so we should not assume they will be present. It may be necessary
		// to do additional checks, preferably by fixing upstream to decode
		// the AV1 headers.
		if ( isset( $video['bits_per_sample'] ) ) {
			$depth = (int)$video['bits_per_sample'];
			if ( $depth === 8 ) {
				// use default '08'
			} elseif ( $depth === 10 ) {
				$bitDepth = '10';
			} elseif ( $depth === 12 ) {
				$bitDepth = '12';
				$profile = '2';
			} else {
				// If depth is outside expected range, the file isn't playable.
				return false;
			}
		}

		// Extract resolution and frame rate
		$width = isset( $video['resolution_x'] ) ? (int)$video['resolution_x'] : 0;
		$height = isset( $video['resolution_y'] ) ? (int)$video['resolution_y'] : 0;
		// Default 30 fps if unknown
		$frameRate = isset( $video['frame_rate'] ) ? (float)$video['frame_rate'] : 30.0;

		// Check if a tier is specified
		// Currently, we assume Main tier if not specified. If 'high' is detected, switch to High tier.
		if ( isset( $video['tier'] ) && is_string( $video['tier'] ) ) {
			$t = strtolower( $video['tier'] );
			if ( $t === 'high' ) {
				$tier = 'H';
			}
		}

		// If resolution is not available, we cannot reliably determine a level.
		// Default to level 05 in that case (arbitrary choice).
		if ( $width <= 0 || $height <= 0 ) {
			$codecString = sprintf( "av01.%s.%s%s.%s", $profile, '05', $tier, $bitDepth );
			return $codecString;
		}

		// Calculate picture size and display rate
		$pictureSize = $width * $height;
		$displayRate = (int)( $pictureSize * $frameRate );

		/**
		 * Updated AV1 Level Constraints Table based on AV1 specification.
		 * This table maps `seq_level_idx` values (0-19) to their corresponding
		 * AV1 levels, maximum resolutions, and frame rate constraints.
		 *
		 * Key:
		 *  - `seq_level_idx` (string): AV1 sequence level index (e.g., '00' for Level 2.0).
		 *
		 * Value (array):
		 *  - [0]: Maximum picture size (in pixels).
		 *  - [1]: Maximum horizontal resolution (width).
		 *  - [2]: Maximum vertical resolution (height).
		 *  - [3]: Maximum display rate (in pixels per second).
		 *
		 * Notes:
		 *  - Some levels (e.g., 2.2, 2.3, 3.2, 3.3, etc.) are not defined in the AV1 specification.
		 *  - Levels 5.3 and 6.3 are identical to Levels 5.2 and 6.2, respectively.
		 *  - Adjust these values based on updates to the AV1 specification or specific requirements.
		 */
		$av1Levels = [
			// seq_level_idx = 0, Level 2.0: Maximum resolution ~426x240@30fps
			'00' => [ 147456, 2048, 1152, 4423680 ],

			// seq_level_idx = 1, Level 2.1: Maximum resolution ~640x360@30fps
			'01' => [ 278784, 2816, 1584, 8363520 ],

			// seq_level_idx = 2, Level 2.2: Not defined in the AV1 specification
			// seq_level_idx = 3, Level 2.3: Not defined in the AV1 specification

			// seq_level_idx = 4, Level 3.0: Maximum resolution ~854x480@30fps
			'04' => [ 665856, 4352, 2448, 19975680 ],

			// seq_level_idx = 5, Level 3.1: Maximum resolution ~1280x720@30fps
			'05' => [ 1065024, 5504, 3096, 31950720 ],

			// seq_level_idx = 6, Level 3.2: Not defined in the AV1 specification
			// seq_level_idx = 7, Level 3.3: Not defined in the AV1 specification

			// seq_level_idx = 8, Level 4.0: Maximum resolution ~1920x1080@30fps
			'08' => [ 2359296, 6144, 3456, 70778880 ],

			// seq_level_idx = 9, Level 4.1: Maximum resolution ~1920x1080@60fps
			'09' => [ 2359296, 6144, 3456, 141557760 ],

			// seq_level_idx = 10, Level 4.2: Not defined in the AV1 specification
			// seq_level_idx = 11, Level 4.3: Not defined in the AV1 specification

			// seq_level_idx = 12, Level 5.0: Maximum resolution ~3840x2160@30fps
			'12' => [ 8912896, 8192, 4352, 267386880 ],

			// seq_level_idx = 13, Level 5.1: Maximum resolution ~3840x2160@60fps
			'13' => [ 8912896, 8192, 4352, 534773760 ],

			// seq_level_idx = 14, Level 5.2: Maximum resolution ~3840x2160@120fps
			'14' => [ 8912896, 8192, 4352, 1069547520 ],

			// seq_level_idx = 15, Level 5.3: Same as Level 5.2 (not explicitly differentiated)
			'15' => [ 8912896, 8192, 4352, 1069547520 ],

			// seq_level_idx = 16, Level 6.0: Maximum resolution ~7680x4320@30fps
			'16' => [ 35651584, 16384, 8704, 1069547520 ],

			// seq_level_idx = 17, Level 6.1: Maximum resolution ~7680x4320@60fps
			'17' => [ 35651584, 16384, 8704, 2139095040 ],

			// seq_level_idx = 18, Level 6.2: Maximum resolution ~7680x4320@120fps
			'18' => [ 35651584, 16384, 8704, 4278190080 ],

			// seq_level_idx = 19, Level 6.3: Same as Level 6.2 (not explicitly differentiated)
			'19' => [ 35651584, 16384, 8704, 4278190080 ],
		];

		// Determine the minimal level that supports the given parameters
		$chosenLevel = null;
		foreach ( $av1Levels as $lvl => $constraints ) {
			[ $maxPicSize, $maxHSize, $maxVSize, $maxDisplayRate ] = $constraints;

			// Check if the video fits within these constraints
			if ( $pictureSize <= $maxPicSize &&
				$width <= $maxHSize &&
				$height <= $maxVSize &&
				$displayRate <= $maxDisplayRate
			) {
				$chosenLevel = $lvl;
				break;
			}
		}

		// If no suitable level is found, default to the highest level (08)
		// This implies the content exceeds all known constraints we have defined.
		if ( $chosenLevel === null ) {
			$chosenLevel = '08';
		}

		// @fixme check chroma subsampling
		// 4:4:4 must be at least '1' - High profile
		// 4:2:2 or 12-bit must be at least '2' - Professional profile

		// Construct the codec string
		// Format: av01.<profile>.<level><tier>.<bitDepth>
		//
		// @fixme check the color primaries, transfer characteristics,
		//        matrix coefficients, range glad
		// Full format: av01.P.LLT.DD[.M.CCC.cp.tc.mc.F]
		// Use the full format if they're not same as defaults
		$codecString = sprintf( "av01.%s.%s%s.%s", $profile, $chosenLevel, $tier, $bitDepth );

		return $codecString;
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $file->getMetadataArray();

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
	 * @return string HTML
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-webm-short-video' )
			->params( implode( '/', $streamTypes ) )
			->timeperiodParams( $this->getLength( $file ) )
			->escaped();
	}

	/**
	 * @param File $file
	 * @return string HTML
	 */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-webm-long-video' )
			->params( implode( '/', $streamTypes ) )
			->timeperiodParams( $this->getLength( $file ) )
			->bitrateParams( $this->getBitRate( $file ) )
			->numParams( $file->getWidth(), $file->getHeight() )
			->sizeParams( $file->getSize() )
			->escaped();
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
		$metadata = $file->getMetadataArray();
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
