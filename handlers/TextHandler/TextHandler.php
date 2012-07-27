<?php
/**
 * Timed Text handling for mediaWiki
 *
 * Timed text support is presently fairly limited. Unlike Ogg and WebM handlers,
 * timed text does not extend the TimedMediaHandler class.
 *
 * TODO On "new" timedtext language save purge all pages where file exists
 */
class TextHandler {

	var $remoteNs = null;//lazy init remote Namespace number

	/**
	 * @var File
	 */
	protected $file;

	function __construct( $file ){
		$this->file = $file;
	}

	/**
	 * Get the timed text tracks elements as an associative array
	 * @return array|mixed
	 */
	function getTracks(){
		if( $this->file->isLocal() || $this->file->repo instanceof ForeignDBViaLBRepo ){
			return $this->getLocalTextSources();
		}else {
			return $this->getRemoteTextSources();
		}
	}

	/**
	 * @return bool|int|null
	 */
	function getTimedTextNamespace(){
		if( $this->file->isLocal() || $this->file->repo instanceof ForeignDBViaLBRepo ){
			return NS_TIMEDTEXT;
		} else {
			if( $this->remoteNs !== null ){
				return $this->remoteNs;
			}
			// Get the namespace data from the image api repo:
			// fetchImageQuery query caches results
			$data = $this->file->getRepo()->fetchImageQuery( array(
				'meta' =>'siteinfo',
				'siprop' => 'namespaces'
			));

			if( isset( $data['query'] ) && isset( $data['query']['namespaces'] ) ){

				foreach( $data['query']['namespaces'] as $ns ){
					if( $ns['*'] == 'TimedText' ){
						$this->remoteNs = $ns['id'];
						return $this->remoteNs;
					}
				}
			}

			// Look in the main namespace ?
			// ( probably should instead throw an error )
			return false;
		}
	}

	/**
	 * @return array|bool
	 */
	function getTextPagesQuery(){
		$ns = $this->getTimedTextNamespace();
		if( $ns === false ){
			wfDebug("Repo: " . $this->file->repo->getName() . " does not have a TimedText namesapce \n");
			// No timed text namespace, don't try to look up timed text tracks
			return false;
		}
		return array(
			'action' => 'query',
			'list' => 'allpages',
			'apnamespace' => $ns,
			'aplimit' => 300,
			'apprefix' => $this->file->getTitle()->getDBKey()
		);
	}

	/**
	 * @return array|mixed
	 */
	function getRemoteTextSources(){
		global $wgMemc;
		// Use descriptionCacheExpiry as our expire for timed text tracks info
		if ( $this->file->getRepo()->descriptionCacheExpiry > 0 ) {
			wfDebug("Attempting to get text tracks from cache...");
			$key = $this->file->getRepo()->getLocalCacheKey( 'RemoteTextTracks', 'url', $this->file->getName() );
			$obj = $wgMemc->get($key);
			if ($obj) {
				wfDebug("success!\n");
				return $obj;
			}
			wfDebug("miss\n");
		}
		wfDebug("Get text tracks from remote api \n");
		$query = $this->getTextPagesQuery();

		// Error in getting timed text namespace return empty array;
		if( $query === false ){
			return array();
		}
		$data = $this->file->getRepo()->fetchImageQuery( $query );
		if ( $data && $this->file->repo->descriptionCacheExpiry > 0 ) {
			$wgMemc->set( $key, $data, $this->file->repo->descriptionCacheExpiry );
		}
		return $this->getTextTracksFromData( $data );
	}

	/**
	 * @return array
	 */
	function getLocalTextSources(){
		// Init $this->textTracks
		$params = new FauxRequest( $this->getTextPagesQuery() );
		$api = new ApiMain( $params );
		$api->execute();
		$data = $api->getResultData();
		// Get the list of language Names
		return $this->getTextTracksFromData( $data );
	}

	/**
	 * @param $data
	 * @return array
	 */
	function getTextTracksFromData( $data ){
		global $wgForeignFileRepos;

		$textTracks = array();
		$providerName = $this->file->repo->getName();
		// For a while commons repo in the mediaWiki manual was called "shared"
		// ( we need commons to be named "commons" so that the javascript api provider names match up )
		//disabled for now, this breaks embedding commons on wikipedia
		/*
		if( $providerName == 'shared' || $providerName == 'wikimediacommons' ){
			// We could alternatively check $this->file->repo->mApiBase
			foreach( $wgForeignFileRepos as $repo ){
				if( $repo['name'] ==  $this->file->repo->getName()
						&&
					parse_url( $repo['apibase'] , PHP_URL_HOST ) == 'commons.wikimedia.org'
				){
					$providerName = 'commons';
				}
			}
		}
		*/
		// Provider name should be the same as the interwiki map
		// @@todo more testing with this:
		$interWikiPrefix = ( $providerName == 'local' ) ? '' : $providerName . ':';

		$langNames = Language::getLanguageNames();
		if( $data['query'] && $data['query']['allpages'] ){
			foreach( $data['query']['allpages'] as $na => $page ){
				$subTitle = Title::newFromText( $interWikiPrefix . $page['title'] ) ;
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
				$namespacePrefix = ( $subTitle->getNsText() )? $subTitle->getNsText() . ':' : '';
				$textTracks[] = array(
					'kind' => 'subtitles',
					'data-mwtitle' => $namespacePrefix . $subTitle->getDBkey(),
					'data-mwprovider' => $providerName,
					'type' => 'text/x-srt',
					// TODO Should eventually add special entry point and output proper WebVTT format:
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
		return $textTracks;
	}
}
