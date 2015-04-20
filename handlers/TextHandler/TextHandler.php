<?php
/**
 * Timed Text handling for mediaWiki
 *
 * Timed text support is presently fairly limited. Unlike Ogg and WebM handlers,
 * timed text does not extend the TimedMediaHandler class.
 *
 * TODO On "new" timedtext language save purge all pages where file exists
 */


/**
 * Subclass ApiMain but query other db
 */
class ForeignApiQueryAllPages extends ApiQueryAllPages {
	public function __construct( $mDb, $query, $moduleName ) {
		global $wgTimedTextForeignNamespaces;

		$this->foreignDb = $mDb;

		$wikiID = $this->foreignDb->getWikiID();
		if ( isset( $wgTimedTextForeignNamespaces[ $wikiID ] ) ) {
			$this->foreignNs = $wgTimedTextForeignNamespaces[ $wikiID ];
		} else {
			$this->foreignNs = NS_TIMEDTEXT;
		}
		parent::__construct( $query, $moduleName, 'ap' );
	}

	protected function getDB() {
		return $this->foreignDb;
	}

	protected function parseMultiValue( $valueName, $value, $allowMultiple, $allowedValues ) {
		// foreignnNs might not be defined localy,
		// catch the undefined error here
		if ( $valueName == 'apnamespace'
			&& $value == $this->foreignNs
			&& $allowMultiple == false
		) {
			return $this->foreignNs;
		}
		return parent::parseMultiValue( $valueName, $value, $allowMultiple, $allowedValues );
	}

	/**
	 * An alternative to titleToKey() that doesn't trim trailing spaces
	 *
	 *
	 * @FIXME: I'M A BIG HACK
	 *
	 * @param string $titlePart Title part with spaces
	 * @return string Title part with underscores
	 */
	public function titlePartToKey( $titlePart, $defaultNamespace = NS_MAIN ) {
		return substr( $this->titleToKey( $titlePart . 'x' ), 0, -1 );
	}
}

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
		if( $this->file->isLocal() ){
			return $this->getLocalTextSources();
		} elseif ( $this->file->getRepo() instanceof ForeignDBViaLBRepo ){
			return $this->getForeignDBTextSources();
		} else {
			return $this->getRemoteTextSources();
		}
	}

	/**
	 * @return bool|int|null
	 */
	function getTimedTextNamespace(){
		global $wgEnableLocalTimedText;
		if( $this->file->isLocal() ) {
			if ( $wgEnableLocalTimedText ) {
				return NS_TIMEDTEXT;
			} else {
				return false;
			}
		} elseif( $this->file->repo instanceof ForeignDBViaLBRepo ){
			global $wgTimedTextForeignNamespaces;
			$wikiID = $this->file->getRepo()->getSlaveDB()->getWikiID();
			if ( isset( $wgTimedTextForeignNamespaces[ $wikiID ] ) ) {
				return $wgTimedTextForeignNamespaces[ $wikiID ];
			}
			// failed to get namespace via ForeignDBViaLBRepo, return NS_TIMEDTEXT
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
				// get the ~last~ timed text namespace defined
				foreach( $data['query']['namespaces'] as $ns ){
					if( $ns['*'] == 'TimedText' ){
						$this->remoteNs = $ns['id'];
					}
				}
			}
			// Return the remote Ns
			return $this->remoteNs;
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
			'apprefix' => $this->file->getTitle()->getDBkey()
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
		$textTracks = $this->getTextTracksFromData( $data );
		if ( $data && $this->file->repo->descriptionCacheExpiry > 0 ) {
			$wgMemc->set( $key, $textTracks, $this->file->repo->descriptionCacheExpiry );
		}
		return $textTracks;
	}

	/**
	 * @return array
	 */
	function getLocalTextSources(){
		global $wgEnableLocalTimedText;
		if ( $wgEnableLocalTimedText ) {
			// Init $this->textTracks
			$params = new FauxRequest( $this->getTextPagesQuery() );
			$api = new ApiMain( $params );
			$api->execute();
			if ( defined( 'ApiResult::META_CONTENT' ) ) {
				$data = $api->getResult()->getResultData( null, array( 'Strip' => 'all' ) );
			} else {
				$data = $api->getResultData();
			}
			wfDebug(print_r($data, true));
			// Get the list of language Names
			return $this->getTextTracksFromData( $data );
		} else {
			return array();
		}
	}

	/**
	 * @return array|mixed
	 */
	function getForeignDBTextSources(){
		// Init $this->textTracks
		$params = new FauxRequest( $this->getTextPagesQuery() );
		$api = new ApiMain( $params );
		$api->profileIn();
		$query = new ApiQuery( $api, 'foo', 'bar' );
		$query->profileIn();
		$module = new ForeignApiQueryAllPages( $this->file->getRepo()->getSlaveDB(), $query, 'allpages' );
		$module->profileIn();
		$module->execute();
		$module->profileOut();
		$query->profileOut();
		$api->profileOut();

		if ( defined( 'ApiResult::META_CONTENT' ) ) {
			$data = $module->getResult()->getResultData( null, array( 'Strip' => 'all' ) );
		} else {
			$data = $module->getResultData();
		}
		// Get the list of language Names
		return $this->getTextTracksFromData( $data );
	}

	/**
	 * @param $data
	 * @return array
	 */
	function getTextTracksFromData( $data ){
		$textTracks = array();
		$providerName = $this->file->repo->getName();
		// commons is called shared in production. normalize it to wikimediacommons
		if( $providerName == 'shared' ){
			$providerName = 'wikimediacommons';
		}
		// Provider name should be the same as the interwiki map
		// @@todo more testing with this:

		$langNames = Language::fetchLanguageNames( null, 'mw' );
		if( $data['query'] && $data['query']['allpages'] ){
			foreach( $data['query']['allpages'] as $page ){
				$subTitle = Title::newFromText( $page['title'] ) ;
				$tileParts = explode( '.', $page['title'] );
				if( count( $tileParts) >= 3 ){
					/*$subtitle_extension = */ array_pop( $tileParts );
					$languageKey = array_pop( $tileParts );
				} else {
					continue;
				}
				// If there is no valid language continue:
				if( !isset( $langNames[ $languageKey ] ) ){
					continue;
				}
				$namespacePrefix = "TimedText:";
				$textTracks[] = array(
					'kind' => 'subtitles',
					'data-mwtitle' => $namespacePrefix . $subTitle->getDBkey(),
					'data-mwprovider' => $providerName,
					'type' => 'text/x-srt',
					// @todo Should eventually add special entry point and output proper WebVTT format:
					// http://www.whatwg.org/specs/web-apps/current-work/webvtt.html
					'src' => $this->getFullURL( $page['title'] ),
					'srclang' =>  $languageKey,
					'data-dir' => Language::factory( $languageKey )->getDir(),
					'label' => wfMessage('timedmedia-subtitle-language',
						$langNames[ $languageKey ],
						$languageKey )->text()
				);
			}
		}
		return $textTracks;
	}

	function getFullURL( $pageTitle ){
		if( $this->file->isLocal() ) {
			$subTitle =  Title::newFromText( $pageTitle ) ;
			return $subTitle->getFullURL( array(
				'action' => 'raw',
				'ctype' => 'text/x-srt'
			));
		//} elseif( $this->file->repo instanceof ForeignDBViaLBRepo ){
		} else {
			$query = 'title=' . wfUrlencode( $pageTitle ) . '&';
			$query .= wfArrayToCgi( array(
				'action' => 'raw',
				'ctype' => 'text/x-srt'
			) );
			// Note: This will return false if scriptDirUrl is not set for repo.
			return $this->file->repo->makeUrl( $query );
		}
	}
}
