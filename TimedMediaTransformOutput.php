<?php 

class TimedMediaTransformOutput extends MediaTransformOutput {
	static $serial = 0;
	
	// Video file sources object lazy init in getSources()
	var $sources = null;
	var $textTracks = null;
	
	function __construct( $conf ){
		$options = array( 'file', 'sources', 'thumbUrl', 'width', 'height', 'length', 'offset', 'isVideo', 'path' );		
		foreach ( $options as $key ) {
			if( isset( $conf[ $key ]) ){
				$this->$key = $conf[$key];
			} else {
				$this->$key = false;
			}
		}
	}
	
	function getPosterUrl(){
		global $wgStylePath;
		if ( $this->isVideo && $this->thumbUrl ) {
			return $this->thumbUrl;
		}
		// else return the fileicon for the poster url: 
		return "$wgStylePath/common/images/icons/fileicon-ogg.png";		
	}
	
	function getPlayerHeight(){
		// Check if "video" tag output:
		if ( $this->isVideo ) {
			return intval( $this->height );
		} else {
			// Give sound files a height of 0 
			return 0;
		}
	}
	
	function getTagName(){		
		return ( $this->isVideo )? 'video' : 'audio';
	}
	
	function toHtml( $options = array() ) {
		global $wgVideoTagOut, $wgMinimumVideoPlayerSize, $wgOut;

		wfLoadExtensionMessages( 'TimedMediaHandler' );
		if ( count( func_get_args() ) == 2 ) {
			throw new MWException( __METHOD__ .' called in the old style' );
		}
			
		// Check if the video is too small to play inline ( instead do a pop-up dialog ) 
		if( $this->width <= $wgMinimumVideoPlayerSize && $this->isVideo ){
			return $this->getImagePopUp();		
		} else {
			return $this->getXmlMediaTagOutput();
		}		
	}
	// XXX migrate this to the mediawiki XML class as 'tagSet' helper function
	static function xmlTagSet( $tagName, $tagSet ){
		$s = '';
		if( empty( $tagSet ) ){
			return '';
		}
		foreach( $tagSet as $attr ){
			$s.= Xml::element($tagName, $attr, '');
		}
		return $s;
	}
	
	function getImagePopUp(){
		return Xml::tags( 'div' , array(
			'id' => "ogg_thumbplayer_" . TimedMediaTransformOutput::$serial++,
			'class' => 'PopUpMediaTransform',
			'style' => "width:" . intval( $this->width ) . "px;height:" . 
						intval( $this->getPlayerHeight() ) . "px",
			'data-videopayload' => $this->getXmlMediaTagOutput( $this->getPopupPlayerSize() ),
			),
				Xml::tags( 'img', array(
					'style' => 'width:100%;height:100%;',
					'src' =>  $this->getPosterUrl(),
				),'')
				.
				// For javascript disabled browsers provide a link to the asset:
				Xml::tags( 'a', array(
					'href'=> $this->file->getUrl(),
					'title' => wfMsg( 'timedmedia-play-media' )
				), '<b></b>'. // why is the a child tag escaped unless there is an html string prefix? 
					Xml::tags( 'div', array(
						'class' => 'play-btn-large'
					), '')
				)
		);
	}
	
	/**
	 * Get target popup player size 
	 * If player is smaller than threshold return size based on wgDefaultUserOptions )
	 */
	function getPopupPlayerSize(){
		global $wgDefaultUserOptions, $wgMinimumVideoPlayerSize, $wgImageLimits;
		// Get the max width from the enabled transcode settings: 
		$maxImageSize = WebVideoTranscode::getMaxSizeWebStream();
		return WebVideoTranscode::getMaxSizeTransform( $this->file, $maxImageSize);
	}
	
	/**
	 * Call mediaWiki xml helper class to build media tag output from 
	 * supplied arrays
	 */
	function getXmlMediaTagOutput( $sizeOverride = array() ){
		// Try to get the first source src attribute ( usually this should be the source file )
		$mediaSources = $this->getMediaSources();
		$firstSource = current( reset( $mediaSources ) );
		if( !$firstSource['url'] ){
			// XXX media handlers don't seem to work with exceptions..
			return 'Error missing media source';
		};

		// Build the video tag output:		
		$s = Xml::tags( $this->getTagName(), $this->getMediaAttr( $sizeOverride ),
	
			// The set of media sources: 
			self::xmlTagSet( 'source', $mediaSources ) .
			
			// Timed text: 
			self::xmlTagSet( 'track', $this->getLocalTextSources() ) .		
			
			// Fallback text displayed for browsers without js and without video tag support: 
			/// XXX note we may want to replace this with an image and download link play button
			wfMsg('timedmedia-no-player-js', $firstSource['src'])				
		);
		return $s;
	}

	function getMediaAttr( $sizeOverride = false ){
		global $wgVideoPlayerSkin ;
		// Normalize values
		$length = floatval( $this->length  );
		$offset = floatval( $this->offset );
		
		$width = ( $sizeOverride )? $sizeOverride[0] : intval( $this->width );
		$height =  ( $sizeOverride )? $sizeOverride[1]: $this->getPlayerHeight();
		
		$mediaAttr = array(			
			'id' => "ogg_player_" . TimedMediaTransformOutput::$serial++,
			'style' => "width:{$width}px;height:{$height}px",
			'poster' => $this->getPosterUrl(),
			'alt' => $this->file->getTitle()->getText(),
		
			// Note we set controls to true ( for no-js players ) when mwEmbed rewrites the interface
			// it updates the controls attribute of the embed video
			'controls'=> 'true',
		);
		
		// Set player skin:
		if( $wgVideoPlayerSkin ){
			$mediaAttr['class'] = htmlspecialchars ( $wgVideoPlayerSkin );
		}
		
		// Custom data-attributes
		$mediaAttr += array(			
			'data-durationhint' => $length,
			'data-startoffset' => $offset,
			'data-mwtitle' => $this->file->getTitle()->getDBKey()
		);
		
		// Add api provider:		
		if( !$this->file->isLocal() ){			
			// Set the api provider name to "commons" for shared ( instant commons convention ) 
			// ( provider names should have identified the provider
			// instead of the provider type "shared" )
			$apiProviderName = ( $this->file->getRepoName() == 'shared' ) ? 'commons':  $this->file->getRepoName();			
		} else {
			$apiProviderName = 'local';
		}
		// XXX Note: will probably migrate mwprovider to an escaped api url.
		$mediaAttr[ 'data-mwprovider' ] = $apiProviderName;
		
		return $mediaAttr;
	}
	
	function getMediaSources(){
		if( !$this->sources ){
			// Generate transcode jobs ( and get sources that area already transcoded)
			// At a minimum this should return the source video file. 
			$this->sources = WebVideoTranscode::getSources( $this->file );	
		}
		return $this->sources;
	}
	
	function getLocalTextSources(){
		global $wgServer, $wgScriptPath;
		
		// Don't do lookup if non-local path: 
		// TODO integrate with repo api and do remote lookup
		if( !$this->file->isLocal() ){
			return array();
		}
		
		// Check local cache: 		
		if( $this->textTracks ){
			return $this->textTracks;
		}
		// Init $this->textTracks
		$this->textTracks = array();
		
		$params = new FauxRequest( array (
			'action' => 'query',
			'list' => 'allpages',
			'apnamespace' => NS_TIMEDTEXT,
			'aplimit' => 200,
			'apprefix' => $this->file->getTitle()->getDBKey()
		));
		$api = new ApiMain( $params );
		$api->execute();
		$data = $api->getResultData();			
		// Get the list of language Names
		$langNames = Language::getLanguageNames();
		
		if( $data['query'] && $data['query']['allpages'] ){
			foreach( $data['query']['allpages'] as $na => $page ){
				$subTitle = Title::newFromText( $page['title'] ) ;
				$tileParts = explode( '.', $page['title'] );
				if( count( $tileParts) >= 3 ){
					$subtitle_extension = array_pop( $tileParts );
					$languageKey = array_pop( $tileParts );
				} else {
					continue;
				}
				// If there is no valid language continue:
				if( !isset( $langNames[ $languageKey ] ) ){
					continue;
				}
				$this->textTracks[] = array(		
					'kind' => 'subtitles',
					'data-mwtitle' => $subTitle->getNsText() . ':' . $subTitle->getDBkey(),
					'type' => 'text/x-srt',
					// TODO Should add a special entry point and output proper WebVTT format:
					// http://www.whatwg.org/specs/web-apps/current-work/webvtt.html
					'src' => $subTitle->getFullURL( array( 
						'action' => 'raw',
						'ctype' => 'text/x-srt'
					)),
					'srclang' =>  $languageKey,
					'label' => wfMsg('timedmedia-subtitle-language', 
						$langNames[ $languageKey ], 
						$languageKey )
				);
			}
		}
		return $this->textTracks;
	}
}
