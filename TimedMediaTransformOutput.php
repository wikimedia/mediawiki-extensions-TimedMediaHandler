<?php

class TimedMediaTransformOutput extends MediaTransformOutput {
	static $serial = 0;

	// Video file sources object lazy init in getSources()
	var $sources = null;
	var $textTracks = null;
	var $hashTime = null;
	var $textHandler = null; // lazy init in getTextHandler
	var $disablecontrols = null;

	var $start, $end, $fillwindow;

	// The prefix for player ids
	const PLAYER_ID_PREFIX = 'mwe_player_';

	function __construct( $conf ){
		$options = array( 'file', 'dstPath', 'sources', 'thumbUrl', 'start', 'end',
			'width', 'height', 'length', 'offset', 'isVideo', 'path', 'fillwindow',
			'sources', 'disablecontrols' );
		foreach ( $options as $key ) {
			if( isset( $conf[ $key ]) ){
				$this->$key = $conf[$key];
			} else {
				$this->$key = false;
			}
		}
	}

	/**
	 * @return TextHandler
	 */
	function getTextHandler(){
		if( !$this->textHandler ){
			// Init an associated textHandler
			$this->textHandler = new TextHandler( $this->file );
		}
		return $this->textHandler;
	}

	/**
	 * Get the media transform thumbnail
	 * @return string
	 */
	function getUrl( $sizeOverride = false ){
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
			if( !$this->file->isOld() && $sizeOverride &&
				$sizeOverride[0] && intval( $sizeOverride[0] ) != intval( $this->width ) ){
				$apiUrl = $this->getPoster( $sizeOverride[0] );
				if( $apiUrl ){
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
	function getPath(){
		return $this->dstPath;
	}

	/**
	 * @return int
	 */
	function getPlayerHeight(){
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
	function getPlayerWidth(){
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return intval( $this->width );
		} else {
			// Give sound files a width of 300px ( if unsized )
			if( $this->width == 0 ){
				return 300;
			}
			// else give the target size down to 35 px wide
			return ( $this->width < 35 ) ? 35 : intval( $this->width ) ;
		}
	}

	/**
	 * @return string
	 */
	function getTagName(){
		return ( $this->isVideo ) ? 'video' : 'audio';
	}

	/**
	 * @param $options array
	 * @return string
	 * @throws Exception
	 */
	function toHtml( $options = array() ) {
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

		if ( $this->useImagePopUp() ) {
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
	 * @param $tagName
	 * @param $tagSet
	 * @return string
	 */
	static function htmlTagSet( $tagName, $tagSet ){
		if( empty( $tagSet ) ){
			return '';
		}
		$s = '';
		foreach( $tagSet as $attr ){
			$s .= Html::element( $tagName, $attr);
		}
		return $s;
	}

	/**
	 * @return string
	 */
	function getImagePopUp(){
		// pop up videos set the autoplay attribute to true:
		$autoPlay = true;
		return Xml::tags( 'div' , array(
				'id' => self::PLAYER_ID_PREFIX . TimedMediaTransformOutput::$serial++,
				'class' => 'PopUpMediaTransform',
				'style' => "width:" . $this->getPlayerWidth() . "px;",
				'videopayload' => $this->getHtmlMediaTagOutput( $this->getPopupPlayerSize(), $autoPlay ),
				),
			Xml::tags( 'img', array(
				'alt' => $this->file->getTitle(),
				'style' => "width:" . $this->getPlayerWidth() . "px;height:" .
							$this->getPlayerHeight() . "px",
				'src' =>  $this->getUrl(),
			),'')
			.
			// For javascript disabled browsers provide a link to the asset:
			Xml::tags( 'a', array(
					'href'=> $this->file->getUrl(),
					'title' => wfMessage( 'timedmedia-play-media' )->escaped(),
					'target' => 'new'
				),
				Xml::tags( 'span', array(
						'class' => 'play-btn-large'
					),
					// Have some sort of text for lynx & screen readers.
					Html::element(
						'span',
						array( 'class' => 'mw-tmh-playtext' ),
						wfMessage( 'timedmedia-play-media' )->text()
					)
				)
			)
		);
	}

	/**
	 * Get target popup player size
	 */
	function getPopupPlayerSize(){
		// Get the max width from the enabled transcode settings:
		$maxImageSize = WebVideoTranscode::getMaxSizeWebStream();
		return WebVideoTranscode::getMaxSizeTransform( $this->file, $maxImageSize);
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
	 * supplied arrays
	 * @param $sizeOverride array
	 * @param $autoPlay boolean sets the autoplay attribute
	 * @return string
	 */
	function getHtmlMediaTagOutput( $sizeOverride = array(), $autoPlay = false ){
		// Try to get the first source src attribute ( usually this should be the source file )
		$mediaSources = $this->getMediaSources();
		reset( $mediaSources ); // do not rely on auto-resetting of arrays under HHVM
		$firstSource = current( $mediaSources );

		if( !$firstSource['src'] ){
			// XXX media handlers don't seem to work with exceptions..
			return 'Error missing media source';
		};

		// Sort sources by bandwidth least to greatest ( so default selection on resource constrained
		// browsers ( without js? ) go with minimal source.
		usort( $mediaSources, array( $this, 'sortMediaByBandwidth' ) );

		// We prefix some source attributes with data- to pass along to the javascript player
		$prefixedSourceAttr = Array( 'width', 'height', 'title', 'shorttitle', 'bandwidth', 'framerate', 'disablecontrols' );
		foreach( $mediaSources as &$source ){
			foreach( $source as $attr => $val ){
				if( in_array( $attr, $prefixedSourceAttr ) ){
					$source[ 'data-' . $attr ] = $val;
					unset( $source[ $attr ] );
				}
			}
		}

		$width = $sizeOverride ? $sizeOverride[0] : $this->getPlayerWidth();
		if( $this->fillwindow ){
			$width = '100%';
		} else {
			$width .= 'px';
		}
		// Build the video tag output:
		$s = Xml::tags( 'div' , array(
				'class' => 'mediaContainer',
				'style' => 'position:relative;display:block;width:'. $width
			),
			Html::rawElement( $this->getTagName(), $this->getMediaAttr( $sizeOverride, $autoPlay ),
				// The set of media sources:
				self::htmlTagSet( 'source', $mediaSources ) .

				// Timed text:
				self::htmlTagSet( 'track',
					$this->file ? $this->getTextHandler()->getTracks() : null ) .

				// Fallback text displayed for browsers without js and without video tag support:
				/// XXX note we may want to replace this with an image and download link play button
				wfMessage( 'timedmedia-no-player-js', $firstSource['src'] )->text()
			)
		);
		return $s;
	}

	/**
	 * Get poster.
	 * @param $width Integer width of poster. Should not equal $this->width.
	 * @throws Exception If $width is same as $this->width.
	 * @return String|bool url for poster or false
	 */
	function getPoster ( $width ) {
		if ( intval( $width ) === intval( $this->width ) ) {
			// Prevent potential loop
			throw new Exception( "Asked for poster in current size. Potential loop." );
		}
		$params = array( "width" => intval( $width ) );
		$mto = $this->file->transform( $params );
		if ( $mto ) {
			return $mto->getUrl();
		} else {
			return false;
		}
	}

	/**
	 * Get the media attributes
	 * @param $sizeOverride Array|bool of width and height
	 * @return array
	 */
	function getMediaAttr( $sizeOverride = false, $autoPlay = false ){
		global $wgVideoPlayerSkin ;
		// Normalize values
		$length = floatval( $this->length  );
		$offset = floatval( $this->offset );

		$width = $sizeOverride ? $sizeOverride[0] : $this->getPlayerWidth();
		$height = $sizeOverride ? $sizeOverride[1]: $this->getPlayerHeight();

		// The poster url:
		$posterUrl = $this->getUrl( $sizeOverride );

		if( $this->fillwindow ){
			$width = '100%';
			$height = '100%';
		} else{
			$width .= 'px';
			$height .= 'px';
		}

		$mediaAttr = array(
			'id' => self::PLAYER_ID_PREFIX . TimedMediaTransformOutput::$serial++,
			'style' => "width:{$width}",
			// Get the correct size:
			'poster' => $posterUrl,

			// Note we set controls to true ( for no-js players ) when mwEmbed rewrites the interface
			// it updates the controls attribute of the embed video
			'controls'=> 'true',
			// Since we will reload the item with javascript,
			// tell browser to not load the video before
			'preload'=>'none',
		);

		if ( $this->isVideo ) {
			$mediaAttr['style'] .= ";height:{$height}";
		}

		if( $autoPlay === true ){
			$mediaAttr['autoplay'] = 'true';
		}

		// MediaWiki uses the kSkin class
		$mediaAttr['class'] = 'kskin';

		if ( $this->file ) {
			// Custom data-attributes
			$mediaAttr += array(
				'data-durationhint' => $length,
				'data-startoffset' => $offset,
				'data-mwtitle' => $this->file->getTitle()->getDBkey()
			);

			// Add api provider:
			if( $this->file->isLocal() ){
				$apiProviderName = 'local';
			} else {
				// Set the api provider name to "wikimediacommons" for shared ( instant commons convention )
				// (provider names should have identified the provider instead of the provider type "shared")
				$apiProviderName = $this->file->getRepoName();
				if( $apiProviderName == 'shared' ) {
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
		if ( $this->disablecontrols ) {
			$mediaAttr[ 'data-disablecontrols' ] = $this->disablecontrols;
		}
		return $mediaAttr;
	}

	/**
	 * @return null
	 */
	function getMediaSources(){
		if( !$this->sources ){
			// Generate transcode jobs ( and get sources that are already transcoded)
			// At a minimum this should return the source video file.
			$this->sources = WebVideoTranscode::getSources( $this->file );
			// Check if we have "start or end" times and append the temporal url fragment hash
			foreach( $this->sources as &$source ){
				$source['src'].= $this->getTemporalUrlHash();
			}
		}
		return $this->sources;
	}

	function getTemporalUrlHash(){
		if( $this->hashTime ){
			return $this->hashTime;
		}
		$hash ='';
		if( $this->start ){
			$startSec = TimedMediaHandler::parseTimeString( $this->start );
			if( $startSec !== false ){
				$hash.= '#t=' . TimedMediaHandler::seconds2npt( $startSec );
			}
		}
		if( $this->end ){
			if( $hash == '' ){
				$hash .= '#t=0';
			}
			$endSec = TimedMediaHandler::parseTimeString( $this->end );
			if( $endSec !== false ){
				$hash.= ',' . TimedMediaHandler::seconds2npt( $endSec );
			}
		}
		$this->hashTime = $hash;
		return $this->hashTime;
	}
}
