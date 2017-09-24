<?php

class TimedMediaTransformOutput extends MediaTransformOutput {
	protected static $serial = 0;

	// Video file sources object lazy init in getSources()
	// TODO these vars should probably be private
	public $sources = null;
	public $textTracks = null;
	public $hashTime = null;
	public $textHandler = null; // lazy init in getTextHandler
	public $disablecontrols = null;

	public $start;
	public $end;
	public $fillwindow;
	protected $playerClass;

	// The prefix for player ids
	const PLAYER_ID_PREFIX = 'mwe_player_';

	function __construct( $conf ) {
		$options = [ 'file', 'dstPath', 'sources', 'thumbUrl', 'start', 'end',
			'width', 'height', 'length', 'offset', 'isVideo', 'path', 'fillwindow',
			'sources', 'disablecontrols', 'playerClass' ];
		foreach ( $options as $key ) {
			if ( isset( $conf[ $key ] ) ) {
				$this->$key = $conf[$key];
			} else {
				$this->$key = false;
			}
		}
	}

	/**
	 * @return TextHandler
	 */
	function getTextHandler() {
		if ( !$this->textHandler ) {
			// Init an associated textHandler
			$this->textHandler = new TextHandler( $this->file );
		}
		return $this->textHandler;
	}

	/**
	 * Get the media transform thumbnail
	 * @param bool|array $sizeOverride
	 * @return string
	 */
	function getUrl( $sizeOverride = false ) {
		global $wgVersion, $wgResourceBasePath, $wgStylePath;
		// Needs to be 1.24c because version_compare() works in confusing ways
		if ( version_compare( $wgVersion, '1.24c', '>=' ) ) {
			$url = "$wgResourceBasePath/resources/assets/file-type-icons/fileicon-ogg.png";
		} else {
			$url = "$wgStylePath/common/images/icons/fileicon-ogg.png";
		}

		if ( $this->isVideo ) {
			if ( $this->thumbUrl ) {
				$url = $this->thumbUrl;
			}

			// Update the $posterUrl to $sizeOverride ( if not an old file )
			if ( !$this->file->isOld() && $sizeOverride &&
				$sizeOverride[0] && intval( $sizeOverride[0] ) != intval( $this->width ) ) {
				$apiUrl = $this->getPoster( $sizeOverride[0] );
				if ( $apiUrl ) {
					$url = $apiUrl;
				}
			}
		}
		return $url;
	}

	/**
	 * TODO get the local path
	 * @return mixed
	 */
	function getPath() {
		return $this->dstPath;
	}

	/**
	 * @return int
	 */
	function getPlayerHeight() {
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return intval( $this->height );
		} else {
			// Give sound files a height of 23px
			return 23;
		}
	}

	/**
	 * @return int
	 */
	function getPlayerWidth() {
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return intval( $this->width );
		} else {
			// Give sound files a width of 300px ( if unsized )
			if ( $this->width == 0 ) {
				return 300;
			}
			// else give the target size down to 35 px wide
			return ( $this->width < 35 ) ? 35 : intval( $this->width );
		}
	}

	/**
	 * @return string
	 */
	function getTagName() {
		return ( $this->isVideo ) ? 'video' : 'audio';
	}

	/**
	 * @param array $options
	 * @return string
	 * @throws Exception
	 */
	function toHtml( $options = [] ) {
		if ( count( func_get_args() ) == 2 ) {
			throw new Exception( __METHOD__ .' called in the old style' );
		}

		$oldHeight = $this->height;
		$oldWidth = $this->width;
		if ( isset( $options['override-height'] ) ) {
			$this->height = $options['override-height'];
		}
		if ( isset( $options['override-width'] ) ) {
			$this->width = $options['override-width'];
		}

		if ( $this->useImagePopUp() && TimedMediaHandlerHooks::activePlayerMode() === 'mwembed' ) {
			$res = $this->getImagePopUp();
		} else {
			$res = $this->getHtmlMediaTagOutput();
		}
		$this->width = $oldWidth;
		$this->height = $oldHeight;
		return $res;
	}

	/**
	 * Helper to determine if to use pop up dialog for videos
	 *
	 * @return boolean
	 */
	private function useImagePopUp() {
		global  $wgMinimumVideoPlayerSize;
		// Check if the video is too small to play inline ( instead do a pop-up dialog )
		// If we're filling the window (e.g. during an iframe embed) one probably doesn't want the pop up.
		// Also the pop up is broken in that case.
		return $this->isVideo
			&& !$this->fillwindow
			&& $this->getPlayerWidth() < $wgMinimumVideoPlayerSize
			// Do not do pop-up if its going to be the same size as inline player anyways
			&& $this->getPlayerWidth() < $this->getPopupPlayerWidth();
	}

	/**
	 * XXX migrate this to the mediawiki Html class as 'tagSet' helper function
	 * @param string $tagName
	 * @param array $tagSet
	 * @return string
	 */
	static function htmlTagSet( $tagName, $tagSet ) {
		if ( empty( $tagSet ) ) {
			return '';
		}
		$s = '';
		foreach ( $tagSet as $attr ) {
			$s .= Html::element( $tagName, $attr );
		}
		return $s;
	}

	/**
	 * @return string
	 */
	function getImagePopUp() {
		// pop up videos set the autoplay attribute to true:
		$autoPlay = true;
		$id = self::$serial;
		self::$serial++;

		return Xml::tags( 'div', [
				'id' => self::PLAYER_ID_PREFIX . $id,
				'class' => 'PopUpMediaTransform',
				'style' => "width:" . $this->getPlayerWidth() . "px;",
				'videopayload' => $this->getHtmlMediaTagOutput( $this->getPopupPlayerSize(), $autoPlay ),
				],
			Xml::tags( 'img', [
				'alt' => $this->file->getTitle(),
				'style' => "width:" . $this->getPlayerWidth() . "px;height:" .
							$this->getPlayerHeight() . "px",
				'src' => $this->getUrl(),
			], '' )
			.
			// For javascript disabled browsers provide a link to the asset:
			Xml::tags( 'a', [
					'href' => $this->file->getUrl(),
					'title' => wfMessage( 'timedmedia-play-media' )->escaped(),
					'target' => 'new'
				],
				Xml::tags( 'span', [
						'class' => 'play-btn-large'
					],
					// Have some sort of text for lynx & screen readers.
					Html::element(
						'span',
						[ 'class' => 'mw-tmh-playtext' ],
						wfMessage( 'timedmedia-play-media' )->text()
					)
				)
			)
		);
	}

	/**
	 * Get target popup player size
	 * @return int
	 */
	function getPopupPlayerSize() {
		// Get the max width from the enabled transcode settings:
		$maxImageSize = WebVideoTranscode::getMaxSizeWebStream();
		return WebVideoTranscode::getMaxSizeTransform( $this->file, $maxImageSize );
	}

	/**
	 * Helper function to get pop up width
	 *
	 * Silly function because array index operations aren't allowed
	 * on function calls before php 5.4
	 */
	private function getPopupPlayerWidth() {
		list( $popUpWidth ) = $this->getPopupPlayerSize();
		return $popUpWidth;
	}

	/**
	 * Sort media by bandwidth, but with things not wide enough at end
	 *
	 * The list should be in preferred source order, so we want the file
	 * with the lowest bitrate (to save bandwidth) first, but we also want
	 * appropriate resolution files before the 160p transcodes.
	 */
	private function sortMediaByBandwidth( $a, $b ) {
		$width = $this->getPlayerWidth();
		$maxWidth = $this->getPopupPlayerWidth();
		if ( $this->useImagePopUp() || $width > $maxWidth ) {
			// If its a pop-up player than we should use the pop up player size
			// if its a normal player, but has a bigger width than the pop-up
			// player, then we use the pop-up players width as the target width
			// as that is equivalent to the max transcode size. Otherwise this
			// will suggest the original file as the best source, which seems like
			// a potentially bad idea, as it could be anything size wise.
			$width = $maxWidth;
		}

		if ( $a['width'] < $width && $b['width'] >= $width ) {
			// $a is not wide enough but $b is
			// so we consider $a > $b as we want $b before $a
			return 1;
		}
		if ( $a['width'] >= $width && $b['width'] < $width ) {
			// $b not wide enough, so $a must be preferred.
			return -1;
		}
		if ( $a['width'] < $width && $b['width'] < $width && $a['width'] != $b['width'] ) {
			// both are too small. Go with the one closer to the target width
			return ( $a['width'] < $b['width'] ) ? -1 : 1;
		}
		// Both are big enough, or both equally too small. Go with the one
		// that has a lower bit-rate (as it will be faster to download).
		if ( isset( $a['bandwidth'] ) && isset( $b['bandwidth'] ) ) {
			return ( $a['bandwidth'] < $b['bandwidth'] ) ? -1 : 1;
		}

		// We have no firm basis for a comparison, so consider them equal.
		return 0;
	}

	/**
	 * Call mediaWiki xml helper class to build media tag output from
	 * supplied arrays.
	 *
	 * This function is also called by the Score extension, in which case
	 * there is no connection to a file object.
	 *
	 * @param array $sizeOverride
	 * @param bool $autoPlay sets the autoplay attribute
	 * @return string
	 */
	function getHtmlMediaTagOutput( $sizeOverride = [], $autoPlay = false ) {
		// Try to get the first source src attribute ( usually this should be the source file )
		$mediaSources = $this->getMediaSources();
		reset( $mediaSources ); // do not rely on auto-resetting of arrays under HHVM
		$firstSource = current( $mediaSources );

		if ( !$firstSource['src'] ) {
			// XXX media handlers don't seem to work with exceptions..
			return 'Error missing media source';
		};

		// Sort sources by bandwidth least to greatest ( so default selection on resource constrained
		// browsers ( without js? ) go with minimal source.
		usort( $mediaSources, [ $this, 'sortMediaByBandwidth' ] );

		// We prefix some source attributes with data- to pass along to the javascript player
		$prefixedSourceAttr = [
			'width',
			'height',
			'title',
			'shorttitle',
			'bandwidth',
			'framerate',
			'disablecontrols',
			'transcodekey',
			'label',
			'res',
		];
		foreach ( $mediaSources as &$source ) {
			foreach ( $source as $attr => $val ) {
				if ( in_array( $attr, $prefixedSourceAttr ) ) {
					$source[ 'data-' . $attr ] = $val;
					unset( $source[ $attr ] );
				}
			}
		}
		$mediaTracks = $this->file ? $this->getTextHandler()->getTracks() : [];
		foreach ( $mediaTracks as &$track ) {
			foreach ( $track as $attr => $val ) {
				if ( $attr === 'title' || $attr === 'provider' ) {
					$track[ 'data-mw' . $attr ] = $val;
					unset( $track[ $attr ] );
				} elseif ( $attr === 'dir' ) {
					$track[ 'data-' . $attr ] = $val;
					unset( $track[ $attr ] );
				}
			}
		}

		$width = $sizeOverride ? $sizeOverride[0] : $this->getPlayerWidth();
		if ( $this->fillwindow ) {
			$width = '100%';
		} else {
			$width .= 'px';
		}

		// Build the video tag output:
		$s = Html::rawElement( $this->getTagName(), $this->getMediaAttr( $sizeOverride, $autoPlay ),
			// The set of media sources:
			self::htmlTagSet( 'source', $mediaSources ) .

			// Timed text:
			self::htmlTagSet( 'track', $mediaTracks )
		);

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'videojs' ) {
			return $s;
		} // else mwEmbed player

		// Build the video tag output:
		return Xml::tags( 'div', [
			'class' => 'mediaContainer',
			'style' => 'width:'. $width
		], $s );
	}

	/**
	 * Get poster.
	 * @param int $width width of poster. Should not equal $this->width.
	 * @throws Exception If $width is same as $this->width.
	 * @return String|bool url for poster or false
	 */
	function getPoster( $width ) {
		if ( intval( $width ) === intval( $this->width ) ) {
			// Prevent potential loop
			throw new Exception( "Asked for poster in current size. Potential loop." );
		}
		$params = [ "width" => intval( $width ) ];
		$mto = $this->file->transform( $params );
		if ( $mto ) {
			return $mto->getUrl();
		} else {
			return false;
		}
	}

	/**
	 * Get the media attributes
	 * @param array|bool $sizeOverride Array of width and height
	 * @param bool $autoPlay
	 * @return array
	 */
	function getMediaAttr( $sizeOverride = false, $autoPlay = false ) {
		global $wgVideoPlayerSkin;

		// Normalize values
		$length = floatval( $this->length );
		$offset = floatval( $this->offset );

		$width = $sizeOverride ? $sizeOverride[0] : $this->getPlayerWidth();
		$height = $sizeOverride ? $sizeOverride[1] : $this->getPlayerHeight();

		if ( $this->fillwindow ) {
			$width = '100%';
			$height = '100%';
		} else {
			$width .= 'px';
			$height .= 'px';
		}

		$id = self::$serial;
		self::$serial++;
		$mediaAttr = [
			'id' => self::PLAYER_ID_PREFIX . $id,
			// Get the correct size:
			'poster' => $this->getUrl( $sizeOverride ),

			// Note we set controls to true ( for no-js players ) when mwEmbed rewrites the interface
			// it updates the controls attribute of the embed video
			'controls' => 'true',
			// Since we will reload the item with javascript,
			// tell browser to not load the video before
			'preload' => 'none',
		];

		if ( $autoPlay === true ) {
			$mediaAttr['autoplay'] = 'true';
		}

		if ( !$this->isVideo ) {
			// audio element doesn't have poster attribute
			unset( $mediaAttr[ 'poster' ] );
		}

		if ( TimedMediaHandlerHooks::activePlayerMode() === 'videojs' ) {
			// Note: do not add 'video-js' class before the runtime transform!
			$mediaAttr['class'] = $wgVideoPlayerSkin;
			$mediaAttr['width'] = $this->fillwindow ? '100%' : intval( $width );
			if ( $this->isVideo ) {
				$mediaAttr['height'] = $this->fillwindow ? '100%' : intval( $height );
			} else {
				unset( $mediaAttr['height'] );
			}
			if ( $this->fillwindow ) {
				$mediaAttr[ 'class' ] .= ' vjs-fluid';
				$mediaAttr[ 'data-player' ] = 'fillwindow';
			}
		} else {
			$mediaAttr['style'] = "width:{$width}";

			if ( $this->isVideo ) {
				$mediaAttr['style'] .= ";height:{$height}";
			}

			// MediaWiki uses the kSkin class
			$mediaAttr['class'] = 'kskin';
		}

		// Used by Score extension and to disable specific controls from wikicode
		if ( $this->disablecontrols ) {
			$mediaAttr[ 'data-disablecontrols' ] = $this->disablecontrols;
		}

		// Additional class-name provided by Transform caller
		if ( $this->playerClass ) {
			$mediaAttr[ 'class' ] .= ' ' . $this->playerClass;
		}

		if ( $this->file ) {
			// Custom data-attributes
			$mediaAttr += [
				'data-durationhint' => $length,
				'data-startoffset' => $offset,
				'data-mwtitle' => $this->file->getTitle()->getDBkey()
			];

			// Add api provider:
			if ( $this->file->isLocal() ) {
				$apiProviderName = 'local';
			} else {
				// Set the api provider name to "wikimediacommons" for shared ( instant commons convention )
				// (provider names should have identified the provider instead of the provider type "shared")
				$apiProviderName = $this->file->getRepoName();
				if ( $apiProviderName == 'shared' ) {
					$apiProviderName = 'wikimediacommons';
				}
			}
			// XXX Note: will probably migrate mwprovider to an escaped api url.
			$mediaAttr[ 'data-mwprovider' ] = $apiProviderName;
		} else {
			if ( $length ) {
				$mediaAttr[ 'data-durationhint' ] = $length;
			}
			if ( $offset ) {
				$mediaAttr[ 'data-startoffset' ] = $offset;
			}
		}

		return $mediaAttr;
	}

	/**
	 * @return null
	 */
	function getMediaSources() {
		if ( !$this->sources ) {
			// Generate transcode jobs ( and get sources that are already transcoded)
			// At a minimum this should return the source video file.
			$this->sources = WebVideoTranscode::getSources( $this->file );
			// Check if we have "start or end" times and append the temporal url fragment hash
			foreach ( $this->sources as &$source ) {
				$source['src'] .= $this->getTemporalUrlHash();
			}
		}
		return $this->sources;
	}

	function getTemporalUrlHash() {
		if ( $this->hashTime ) {
			return $this->hashTime;
		}
		$hash = '';
		if ( $this->start ) {
			$startSec = TimedMediaHandler::parseTimeString( $this->start );
			if ( $startSec !== false ) {
				$hash .= '#t=' . TimedMediaHandler::seconds2npt( $startSec );
			}
		}
		if ( $this->end ) {
			if ( $hash == '' ) {
				$hash .= '#t=0';
			}
			$endSec = TimedMediaHandler::parseTimeString( $this->end );
			if ( $endSec !== false ) {
				$hash .= ',' . TimedMediaHandler::seconds2npt( $endSec );
			}
		}
		$this->hashTime = $hash;
		return $this->hashTime;
	}

	public static function resetSerialForTest() {
		self::$serial = 1;
	}

	/**
	 * @return array
	 */
	public function getAPIData() {
		$vals = [
			'derivatives' => WebVideoTranscode::getSources( $this->file, [ 'fullurl' ] ),
			'timedtext' => $this->getTextHandler()->getTracks(),
		];
		foreach ( $vals['timedtext'] as &$track ) {
			$track['src'] = wfExpandUrl( $track['src'], PROTO_CURRENT );
		}
		unset( $track );
		return $vals;
	}
}
