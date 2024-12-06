<?php

namespace MediaWiki\TimedMediaHandler;

use LogicException;
use MediaTransformOutput;
use MediaWiki\Html\Html;
use MediaWiki\MainConfigNames;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class TimedMediaTransformOutput extends MediaTransformOutput {
	/** @var int */
	protected static $serial = 0;

	// Video file sources object lazy init in getSources()
	// TODO these vars should probably be private
	/** @var array[]|false|null */
	public $sources;

	/** @var string|false|null */
	public $hashTime;

	public ?TextHandler $textHandler = null;

	/** @var string|false|null */
	public $disablecontrols;

	/** @var mixed */
	public $dstPath;

	/** @var string|false */
	public $thumbUrl;

	/** @var string|false */
	public $start;

	/** @var string|false */
	public $end;

	/** @var float|false */
	public $length;

	/** @var float|false */
	public $offset;

	/** @var bool */
	public $isVideo;

	/** @var bool */
	public $fillwindow;

	/** @var string|false */
	protected $playerClass;

	/** @var bool */
	protected $inline;

	/** @var bool */
	protected $muted;

	/** @var bool */
	protected $loop;

	// The prefix for player ids
	private const PLAYER_ID_PREFIX = 'mwe_player_';

	/**
	 * @param array $conf
	 */
	public function __construct( $conf ) {
		$this->file = $conf['file'] ?? false;
		$this->dstPath = $conf['dstPath'] ?? false;
		$this->sources = $conf['sources'] ?? false;
		$this->thumbUrl = $conf['thumbUrl'] ?? false;
		$this->start = $conf['start'] ?? false;
		$this->end = $conf['end'] ?? false;
		$this->width = $conf['width'] ?? 0;
		$this->height = $conf['height'] ?? 0;
		$this->length = $conf['length'] ?? false;
		$this->offset = $conf['offset'] ?? false;
		$this->isVideo = $conf['isVideo'] ?? false;
		$this->path = $conf['path'] ?? false;
		$this->fillwindow = $conf['fillwindow'] ?? false;
		$this->disablecontrols = $conf['disablecontrols'] ?? false;
		$this->playerClass = $conf['playerClass'] ?? false;
		$this->inline = $conf['inline'] ?? false;
		$this->muted = $conf['muted'] ?? false;
		$this->loop = $conf['loop'] ?? false;
	}

	private function getTextHandler(): TextHandler {
		if ( !$this->textHandler ) {
			// Init an associated textHandler
			$this->textHandler = new TextHandler( $this->file, [ TimedTextPage::VTT_SUBTITLE_FORMAT ] );
		}
		return $this->textHandler;
	}

	/**
	 * Get the media transform thumbnail
	 * @param false|array $sizeOverride
	 */
	public function getUrl( $sizeOverride = false ): string {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$resourceBasePath = $config->get( MainConfigNames::ResourceBasePath );
		$url = "$resourceBasePath/resources/assets/file-type-icons/fileicon-ogg.png";

		if ( $this->isVideo ) {
			if ( $this->thumbUrl ) {
				$url = $this->thumbUrl;
			}

			// Update the $posterUrl to $sizeOverride ( if not an old file )
			if ( !$this->file->isOld() && $sizeOverride &&
				$sizeOverride[0] && (int)$sizeOverride[0] !== (int)$this->width ) {
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
	public function getPath() {
		return $this->dstPath;
	}

	public function getPlayerHeight(): int {
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return (int)$this->height;
		}
		// Give sound files a height of 23px
		return 23;
	}

	public function getPlayerWidth(): int {
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return (int)$this->width;
		}

		// Give sound files a width of 300px ( if unsized )
		if ( !$this->width ) {
			return 300;
		}
		// else give the target size, but at least 35px
		return max( 35, (int)$this->width );
	}

	public function getTagName(): string {
		return ( $this->isVideo ) ? 'video' : 'audio';
	}

	/**
	 * @inheritDoc
	 */
	public function toHtml( $options = [] ): string {
		$classes = $options['img-class'] ?? '';

		$oldHeight = $this->height;
		$oldWidth = $this->width;
		if ( isset( $options['override-height'] ) ) {
			$this->height = $options['override-height'];
		}
		if ( isset( $options['override-width'] ) ) {
			$this->width = $options['override-width'];
		}

		$mediaAttr = $this->getMediaAttr( false, false, $classes );

		// XXX: This might be redundant with data-mwtitle
		$services = MediaWikiServices::getInstance();
		$enableLegacyMediaDOM = $services->getMainConfig()->get( MainConfigNames::ParserEnableLegacyMediaDOM );
		if ( !$enableLegacyMediaDOM && isset( $options['magnify-resource'] ) ) {
			$mediaAttr['resource'] = $options['magnify-resource'];
		}

		$res = $this->getHtmlMediaTagOutput( $mediaAttr );
		$this->width = $oldWidth;
		$this->height = $oldHeight;
		return $this->linkWrap( [], $res );
	}

	/**
	 * Helper to determine if to use pop up dialog for videos
	 */
	private function useImagePopUp(): bool {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		// Check if the video is too small to play inline ( instead do a pop-up dialog )
		// If we're filling the window (e.g. during an iframe embed) one probably doesn't want the pop-up.
		// Also, the pop-up is broken in that case.
		return $this->isVideo
			&& !$this->fillwindow
			&& $this->getPlayerWidth() < $config->get( 'MinimumVideoPlayerSize' )
			// Do not do pop-up if it's going to be the same size as inline player anyways
			&& $this->getPlayerWidth() < $this->getPopupPlayerWidth();
	}

	/**
	 * XXX migrate this to the mediawiki Html class as 'tagSet' helper function
	 */
	private static function htmlTagSet( string $tagName, array $tagSet ): string {
		if ( !$tagSet ) {
			return '';
		}
		$s = '';
		foreach ( $tagSet as $attr ) {
			$s .= Html::element( $tagName, $attr );
		}
		return $s;
	}

	/**
	 * Get target popup player size
	 * @return int[]
	 */
	private function getPopupPlayerSize(): array {
		// Get the max width from the enabled transcode settings:
		$maxImageSize = WebVideoTranscode::getMaxSizeWebStream();
		return WebVideoTranscode::getMaxSizeTransform( $this->file, (string)$maxImageSize );
	}

	/**
	 * Helper function to get pop up width
	 *
	 * Silly function because array index operations aren't allowed
	 * on function calls before php 5.4
	 */
	private function getPopupPlayerWidth(): int {
		[ $popUpWidth ] = $this->getPopupPlayerSize();
		return $popUpWidth;
	}

	/**
	 * Sort media by bandwidth, but with things not wide enough at end
	 *
	 * The list should be in preferred source order, so we want the file
	 * with the lowest bitrate (to save bandwidth) first, but we also want
	 * appropriate resolution files before the 160p transcodes.
	 */
	private function sortMediaByBandwidth( array $a, array $b ): int {
		$width = $this->getPlayerWidth();
		$maxWidth = $this->getPopupPlayerWidth();
		if ( $this->useImagePopUp() || $width > $maxWidth ) {
			// If it's a pop-up player than we should use the pop-up player size.
			// If it's a normal player, but has a bigger width than the pop-up
			// player, then we use the pop-up players width as the target width
			// as that is equivalent to the max transcode size. Otherwise, this
			// will suggest the original file as the best source, which seems like
			// a potentially bad idea, as it could be anything size wise.
			$width = $maxWidth;
		}

		if ( $a['width'] < $width && $b['width'] >= $width ) {
			// $a is not wide enough but $b is, so we
			// consider $a > $b as we want $b before $a
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
	 * @param array $mediaAttr The result of calling getMediaAttr()
	 * @return string HTML
	 */
	private function getHtmlMediaTagOutput( array $mediaAttr ): string {
		// Try to get the first source src attribute ( usually this should be the source file )
		$mediaSources = $this->getMediaSources();
		// do not rely on auto-resetting of arrays under HHVM
		reset( $mediaSources );
		$firstSource = current( $mediaSources );

		if ( $firstSource === false || !$firstSource['src'] ) {
			// XXX media handlers don't seem to work with exceptions..
			return 'Error missing media source';
		}

		// Sort sources by bandwidth least to greatest (so that the default selection on resource
		// constrained browsers (without js?) go with minimal source.)
		usort( $mediaSources, [ $this, 'sortMediaByBandwidth' ] );

		// We prefix some source attributes with data- to pass along to the javascript player
		$prefixedSourceAttr = [
			'width',
			'height',
			'transcodekey',
		];
		$removeSourceAttr = [
			'bandwidth',
			'framerate',
			'disablecontrols',
			'title',
			'shorttitle',
			'label',
			'res',
		];
		foreach ( $mediaSources as &$source ) {
			foreach ( $source as $attr => $val ) {
				if ( in_array( $attr, $removeSourceAttr, true ) ) {
					unset( $source[ $attr ] );
				}
				if ( in_array( $attr, $prefixedSourceAttr, true ) ) {
					$source[ 'data-' . $attr ] = $val;
					unset( $source[ $attr ] );
				}
			}
		}
		unset( $source );
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
		unset( $track );

		// Build the video tag output:
		return Html::rawElement( $this->getTagName(), $mediaAttr,
			// The set of media sources:
			self::htmlTagSet( 'source', $mediaSources ) .

			// Timed text:
			self::htmlTagSet( 'track', $mediaTracks )
		);
	}

	/**
	 * Get poster.
	 * @param int $width width of poster. Should not equal $this->width.
	 * @return string|false url for poster or false
	 */
	private function getPoster( int $width ) {
		if ( $width === (int)$this->width ) {
			// Prevent potential loop
			throw new LogicException( "Asked for poster in current size. Potential loop." );
		}
		$params = [ "width" => $width ];
		$mto = $this->file->transform( $params );
		if ( $mto ) {
			return $mto->getUrl();
		}

		return false;
	}

	/**
	 * Get the media attributes
	 * @param array|false $sizeOverride Array of width and height
	 * @param bool $autoPlay
	 * @param string $classes
	 * @return array
	 */
	private function getMediaAttr(
		$sizeOverride = false, bool $autoPlay = false, string $classes = ''
	): array {
		// Make sure we have pure floats values and round them up to whole seconds
		$length = ceil( (float)$this->length );

		$width = $sizeOverride ? $sizeOverride[0] : $this->getPlayerWidth();
		$height = $sizeOverride ? $sizeOverride[1] : $this->getPlayerHeight();

		$id = self::$serial;
		self::$serial++;
		$mediaAttr = [
			'id' => self::PLAYER_ID_PREFIX . $id,
			// Get the correct size:
			'poster' => $this->getUrl( $sizeOverride ),

			// Note we set controls to true ( for no-js players )
			// When ext.tmh.player.element.js runs it replaces the native player controls
			'controls' => 'true',

			// Since we will reload the item with javascript,
			// tell browser to not load the video before
			'preload' => 'none',
		];

		if ( $autoPlay ) {
			$mediaAttr['autoplay'] = 'true';
		}

		if ( !$this->isVideo ) {
			// audio element doesn't have poster attribute
			unset( $mediaAttr[ 'poster' ] );
		}

		if ( $this->muted ) {
			$mediaAttr['muted'] = 'true';
		}

		if ( $this->loop ) {
			$mediaAttr['loop'] = 'true';
		}

		// Secure selector for JavaScript
		$mediaAttr['data-mw-tmh'] = '';

		// Note: do not add 'video-js' class before the runtime transform!
		$mediaAttr['class'] = '';
		$mediaAttr['width'] = (int)$width;
		if ( $this->isVideo ) {
			$mediaAttr['height'] = (int)$height;
		} else {
			$mediaAttr['style'] = "width:{$width}px;";
			unset( $mediaAttr['height'] );
		}
		if ( $this->fillwindow ) {
			$mediaAttr[ 'data-player' ] = 'fillwindow';
		}
		if ( $this->inline ) {
			$mediaAttr['class'] .= ' mw-tmh-inline';
			$mediaAttr['playsinline'] = '';
			$mediaAttr['preload'] = 'auto';
		}

		// Used by Score extension and to disable specific controls from wikicode
		if ( $this->disablecontrols ) {
			$mediaAttr[ 'data-disablecontrols' ] = $this->disablecontrols;
		}

		// Additional class-name provided by Transform caller
		if ( $this->playerClass ) {
			$mediaAttr[ 'class' ] .= ' ' . $this->playerClass;
		}

		if ( $classes !== '' ) {
			$mediaAttr[ 'class' ] .= ' ' . $classes;
		}

		if ( $length ) {
			$mediaAttr[ 'data-durationhint' ] = $length;
		}

		if ( $this->file ) {
			// Add api provider:
			if ( $this->file->isLocal() ) {
				$apiProviderName = 'local';
			} else {
				// Set the api provider name to "wikimediacommons" for shared ( instant commons convention )
				// (provider names should have identified the provider instead of the provider type "shared")
				$apiProviderName = $this->file->getRepoName();
				if ( $apiProviderName === 'shared' ) {
					$apiProviderName = 'wikimediacommons';
				}
			}
			// Custom data-attributes
			$mediaAttr += [
				'data-mwtitle' => $this->file->getTitle()->getDBkey(),
				// XXX Note: will probably migrate mwprovider to an escaped api url.
				'data-mwprovider' => $apiProviderName,
			];
		}

		return $mediaAttr;
	}

	private function getMediaSources(): array {
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

	private function getTemporalUrlHash(): string {
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
			if ( $hash === '' ) {
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

	public static function resetSerialForTest(): void {
		self::$serial = 1;
	}

	/**
	 * @param array|null $options An optional array of strings to tweak
	 *   the values returned.  Currently valid keys are `"fullurl"`, which
	 *   calls `wfExpandUrl(..., PROTO_CURRENT)` on all URLs returned, and
	 *   `"withhash"`, which ensures that returned URLs have the temporal
	 *   url hash appended (as `getMediaSources()` does).
	 */
	public function getAPIData( ?array $options = null ): array {
		$options ??= [ 'fullurl' ];

		$timedtext = $this->getTextHandler()->getTracks();
		if ( in_array( 'fullurl', $options, true ) ) {
			foreach ( $timedtext as &$track ) {
				$track['src'] = wfExpandUrl( $track['src'], PROTO_CURRENT );
			}
			unset( $track );
		}

		$derivatives = WebVideoTranscode::getSources( $this->file, $options );
		if ( in_array( 'withhash', $options, true ) ) {
			// Check if we have "start or end" times and append the temporal url fragment hash
			foreach ( $derivatives as &$source ) {
				$source['src'] .= $this->getTemporalUrlHash();
			}
			unset( $source );
		}

		return [
			'derivatives' => $derivatives,
			'timedtext' => $timedtext,
		];
	}
}
