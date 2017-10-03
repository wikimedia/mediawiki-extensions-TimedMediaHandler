<?php
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
			$comments = $id3['matroska']['comments'];
			$id3['matroska'] = [
				'comments' => $comments
			];
		}
		return $id3;
	}

	/**
	 * Get the "media size"
	 * @param File $file
	 * @param string $path
	 * @param bool|string|array $metadata
	 * @return array|bool
	 */
	function getImageSize( $file, $path, $metadata = false ) {
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
		if ( isset( $metadata['video']['display_x'] )
				&&
			isset( $metadata['video']['display_y'] )
		) {
			$size = [
				$metadata['video']['display_x'],
				$metadata['video']['display_y']
			];
		} elseif ( isset( $metadata['video']['resolution_x'] )
			&& isset( $metadata['video']['resolution_y'] )
		) {
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
				$size[0] = intval( $size[1] * $metadata['video']['display_aspect_ratio'] );
			} else { // for tall images (i.e. 9:16) take width as base
				$size[1] = intval( $size[0] / $metadata['video']['display_aspect_ratio'] );
			}
		}
		return $size;
	}

	/**
	 * @param File $file
	 * @return string
	 */
	function getMetadataType( $file ) {
		return 'webm';
	}

	/**
	 * @param File $file
	 * @return String
	 */
	function getWebType( $file ) {
		$baseType = ( $file->getWidth() == 0 && $file->getHeight() == 0 ) ? 'audio' : 'video';

		$streams = $this->getStreamTypes( $file );
		if ( !$streams ) {
			return $baseType . '/webm';
		}

		$codecs = strtolower( implode( ', ', $streams ) );

		return $baseType . '/webm; codecs="' . $codecs . '"';
	}

	/**
	 * @param File $file
	 * @return array|bool
	 */
	function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		// id3 gives 'V_VP8' for what we call VP8
		if ( isset( $metadata['video'] ) && $metadata['video']['dataformat'] == 'vp8' ) {
			$streamTypes[] = 'VP8';
		} elseif ( isset( $metadata['video'] ) &&
			( $metadata['video']['dataformat'] === 'vp9'
			|| $metadata['video']['dataformat'] === 'V_VP9'
		) ) {
			// Currently getID3 calls it V_VP9. That will probably change to vp9
			// once getID3 actually gets support for the codec.
			$streamTypes[] = 'VP9';
		}
		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'vorbis' ) {
			$streamTypes[] = 'Vorbis';
		} elseif ( isset( $metadata['audio'] ) &&
			( $metadata['audio']['dataformat'] == 'opus'
			|| $metadata['audio']['dataformat'] == 'A_OPUS'
		) ) {
			// Currently getID3 calls it A_OPUS. That will probably change to 'opus'
			// once getID3 actually gets support for the codec.
			$streamTypes[] = 'Opus';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return String
	 */
	function getShortDesc( $file ) {
		global $wgLang;

		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-webm-short-video', implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) )->text();
	}

	/**
	 * @param File $file
	 * @return String
	 */
	function getLongDesc( $file ) {
		global $wgLang;
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage(
			'timedmedia-webm-long-video',
			implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ),
			$wgLang->formatBitrate( $this->getBitRate( $file ) )
			)->numParams(
				$file->getWidth(),
				$file->getHeight()
			)->text();
	}

	/**
	 * Display metadata box on file description page.
	 *
	 * Very basic, cribbed from OggHandlerTMH fow now.
	 * Only shows the top-level writing/demuxing app comment.
	 *
	 * @param File $file
	 * @param bool|IContextSource $context Context to use (optional)
	 * @return array|bool
	 */
	public function formatMetadata( $file, $context = false ) {
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

		if ( isset( $metadata['matroska'] ) && isset( $metadata['matroska']['comments'] ) ) {
			$comments = $metadata['matroska']['comments'];
			// Map comments from getid3's matroska handler to output format
			// Localization of labels by FormatMetadata...
			$map = [
				'muxingapp' => 'Software',
				'writingapp' => 'Software',
			];
			foreach ( $map as $commentTag => $propTag ) {
				if ( isset( $comments[$commentTag] ) ) {
					if ( !isset( $props[$propTag] ) ) {
						$props[$propTag] = [];
					}
					$props[$propTag][] = $comments[$commentTag];
				}
			}
		}

		return $this->formatMetadataHelper( $props, $context );
	}

}
