<?php
/**
 * Timed Text handling for mediaWiki
 *
 * Timed text support is presently fairly limited. Unlike Ogg and WebM handlers,
 * timed text does not extend the TimedMediaHandler class.
 *
 * TODO On "new" timedtext language save purge all pages where file exists
 */

use Wikimedia\Rdbms\ResultWrapper;

class TextHandler {
	// lazy init remote Namespace number
	public $remoteNs = null;
	public $remoteNsName = null;

	/**
	 * @var File
	 */
	protected $file;

	function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Get the timed text tracks elements as an associative array
	 * @return array|mixed
	 */
	function getTracks() {
		if ( $this->file->isLocal() ) {
			return $this->getLocalTextSources();
		} elseif ( $this->file->getRepo() instanceof ForeignDBViaLBRepo ) {
			return $this->getForeignDBTextSources();
		} else {
			return $this->getRemoteTextSources();
		}
	}

	/**
	 * @return bool|int|null
	 */
	function getTimedTextNamespace() {
		global $wgEnableLocalTimedText;
		if ( $this->file->isLocal() ) {
			if ( $wgEnableLocalTimedText ) {
				return NS_TIMEDTEXT;
			} else {
				return false;
			}
		} elseif ( $this->file->repo instanceof ForeignDBViaLBRepo ) {
			global $wgTimedTextForeignNamespaces;
			$wikiID = $this->file->getRepo()->getSlaveDB()->getWikiID();
			if ( isset( $wgTimedTextForeignNamespaces[ $wikiID ] ) ) {
				return $wgTimedTextForeignNamespaces[ $wikiID ];
			}
			// failed to get namespace via ForeignDBViaLBRepo, return NS_TIMEDTEXT
			if ( $wgEnableLocalTimedText ) {
				return NS_TIMEDTEXT;
			} else {
				return false;
			}
		} else {
			if ( $this->remoteNs !== null ) {
				return $this->remoteNs;
			}
			// Get the namespace data from the image api repo:
			// fetchImageQuery query caches results
			$data = $this->file->getRepo()->fetchImageQuery( [
				'meta' => 'siteinfo',
				'siprop' => 'namespaces'
			] );

			if ( isset( $data['query'] ) && isset( $data['query']['namespaces'] ) ) {
				// get the ~last~ timed text namespace defined
				foreach ( $data['query']['namespaces'] as $ns ) {
					if ( isset( $ns['canonical'] ) && $ns['canonical'] === 'TimedText' ) {
						$this->remoteNs = $ns['id'];
						$this->remoteNsName = $ns['*'];
						wfDebug( "Discovered remoteNs: $this->remoteNs and name: $this->remoteNsName \n" );
						break;
					}
				}
			}
			// Return the remote Ns
			return $this->remoteNs;
		}
	}

	/**
	 * Retrieve a list of TimedText pages in the database that start with
	 * the name of the file associated with this handler.
	 *
	 * If the file is on a foreign repo, will query the ForeignDb
	 *
	 * @return ResultWrapper|bool
	 */
	function getTextPages() {
		$ns = $this->getTimedTextNamespace();
		if ( $ns === false ) {
			wfDebug( 'Repo: ' . $this->file->repo->getName() . " does not have a TimedText namespace \n" );
			// No timed text namespace, don't try to look up timed text tracks
			return false;
		}
		$dbr = $this->file->getRepo()->getSlaveDB();
		$prefix = $this->file->getTitle()->getDBkey();
		return $dbr->select(
			'page',
			[ 'page_namespace', 'page_title' ],
			[
				'page_namespace' => $ns,
				'page_title ' . $dbr->buildLike( $prefix, $dbr->anyString() )
			],
			__METHOD__,
			[
				'LIMIT' => 300,
				'ORDER BY' => 'page_title'
			]
		);
	}

	/**
	 * Build the api query to find TimedText pages belonging to a remote file
	 * @return array|bool
	 */
	function getRemoteTextPagesQuery() {
		$ns = $this->getTimedTextNamespace();
		if ( $ns === false ) {
			wfDebug( 'Repo: ' . $this->file->repo->getName() . " does not have a TimedText namespace \n" );
			// No timed text namespace, don't try to look up timed text tracks
			return false;
		}
		return [
			'action' => 'query',
			'titles' => $this->file->getTitle()->getPrefixedDBkey(),
			'prop' => 'videoinfo',
			'viprop' => 'timedtext',
			'formatversion' => '2',
		];
	}

	/**
	 * Retrieve the text sources belonging to a remote file
	 * @return array|mixed
	 */
	function getRemoteTextSources() {
		global $wgMemc;
		// Use descriptionCacheExpiry as our expire for timed text tracks info
		if ( $this->file->getRepo()->descriptionCacheExpiry > 0 ) {
			wfDebug( "Attempting to get text tracks from cache..." );
			$key = $this->file->getRepo()->getLocalCacheKey(
				'RemoteTextTracks', 'url', $this->file->getName()
			);
			$obj = $wgMemc->get( $key );
			if ( $obj ) {
				wfDebug( "success!\n" );
				return $obj;
			}
			wfDebug( "miss\n" );
		}
		wfDebug( "Get text tracks from remote api \n" );
		$query = $this->getRemoteTextPagesQuery();

		// Error in getting timed text namespace return empty array;
		if ( $query === false ) {
			return [];
		}
		$data = $this->file->getRepo()->fetchImageQuery( $query );
		$textTracks = $this->getTextTracksFromData( $data );
		if ( $data && $this->file->repo->descriptionCacheExpiry > 0 ) {
			$wgMemc->set( $key, $textTracks, $this->file->repo->descriptionCacheExpiry );
		}
		return $textTracks;
	}

	/**
	 * Retrieve the text sources belonging to a foreign db accessible file
	 * @return array
	 */
	function getForeignDBTextSources() {
		$data = $this->getTextPages();
		if ( $data !== false ) {
			return $this->getTextTracksFromRows( $data );
		}
		return [];
	}

	/**
	 * Retrieve the text sources belonging to a local file
	 * @return array
	 */
	function getLocalTextSources() {
		global $wgEnableLocalTimedText;
		if ( $wgEnableLocalTimedText ) {
			$data = $this->getTextPages();
			if ( $data !== false ) {
				return $this->getTextTracksFromRows( $data );
			}
		}
		return [];
	}

	/**
	 * Build an array of track information using a Database result
	 * Handles both local and foreign Db results
	 *
	 * @param ResultWrapper $data Database result with page titles
	 * @return array
	 */
	function getTextTracksFromRows( ResultWrapper $data ) {
		$textTracks = [];
		$providerName = $this->file->repo->getName();
		// commons is called shared in production. normalize it to wikimediacommons
		if ( $providerName === 'shared' ) {
			$providerName = 'wikimediacommons';
		}
		// Provider name should be the same as the interwiki map

		if ( !$this->file->isLocal() ) {
			$namespaceName = $this->getForeignNamespaceName();
		}
		$langNames = Language::fetchLanguageNames( null, 'mw' );

		foreach ( $data as $row ) {
			// Note, the namespace ID of this title might be 'unknown'
			// to our configuration if this is called in ForeignDb situations
			if ( $this->file->isLocal() ) {
				$subTitle = Title::newFromRow( $row );
			} else {
				$subTitle = new ForeignTitle( $row->page_namespace, $namespaceName, $row->page_title );
			}
			$titleParts = explode( '.', $row->page_title );
			if ( count( $titleParts ) >= 3 ) {
				$timedTextExtension = array_pop( $titleParts );
				$languageKey = array_pop( $titleParts );
				$contentType = $this->getContentType( $timedTextExtension );
			} else {
				continue;
			}
			// If there is no valid language continue:
			if ( !isset( $langNames[ $languageKey ] ) ) {
				continue;
			}

			$textTracks[] = [
				// @todo Should eventually add special entry point and output proper WebVTT format:
				// http://www.whatwg.org/specs/web-apps/current-work/webvtt.html
				'src' => $this->getFullURL( $subTitle, $contentType ),
				'kind' => 'subtitles',
				'type' => $contentType,
				'title' => $this->getPrefixedDBkey( $subTitle ),
				'provider' => $providerName,
				'srclang' => $languageKey,
				'dir' => Language::factory( $languageKey )->getDir(),
				'label' => wfMessage( 'timedmedia-subtitle-language',
					$langNames[ $languageKey ],
					$languageKey )->text()
			];
		}
		return $textTracks;
	}

	/**
	 * Build an array of track information using an API result
	 * @param mixed $data JSON decoded result from a query API request
	 * @return array
	 */
	function getTextTracksFromData( $data ) {
		$textTracks = [];
		if ( $data !== null && $data['query'] && $data['query']['pages'] ) {
			foreach ( $data['query']['pages'] as $page ) {
				if ( isset( $page['videoinfo'] ) && $page['videoinfo'] ) {
					foreach ( $page['videoinfo'] as $info ) {
						if ( $info['timedtext'] ) {
							foreach ( $info['timedtext'] as $track ) {
								// Add validation ?
								$textTracks[] = $track;
							}
						}
					}
				}
			}
		}
		return $textTracks;
	}

	function getContentType( $timedTextExtension ) {
		if ( $timedTextExtension === 'srt' ) {
			return 'text/x-srt';
		} elseif ( $timedTextExtension === 'vtt' ) {
			return 'text/vtt';
		}
		return '';
	}

	function getForeignNamespaceName() {
		global $wgEnableLocalTimedText;
		if ( $this->remoteNs !== null ) {
			return $this->remoteNsName;
		}
		/* Else, we use the canonical namespace, since we can't look up the actual one */
		if ( $wgEnableLocalTimedText ) {
			return strtr( MWNamespace::getCanonicalName( NS_TIMEDTEXT ), ' ', '_' );
		} else {
			// Assume the default name if no local TimedText.
			return 'TimedText';
		}
	}

	/**
	 * Retrieve a namespace prefixed and underscored title
	 * @param Title|ForeignTitle $pageTitle
	 * @return string
	 */
	function getPrefixedDBkey( $pageTitle ) {
		if ( $pageTitle instanceof Title ) {
			return $pageTitle->getPrefixedDBkey();
		} elseif ( $pageTitle instanceof ForeignTitle ) {
			return $pageTitle->getFullText();
		}
		return null;
	}

	/**
	 * Retrieve a url to the raw subtitle file
	 * Only use for local and foreignDb requests
	 *
	 * @param Title|ForeignTitle $pageTitle
	 * @param string $contentType
	 * @return string
	 */
	function getFullURL( $pageTitle, $contentType ) {
		if ( $pageTitle instanceof Title ) {
			return $pageTitle->getFullURL( [
				'action' => 'raw',
				'ctype' => $contentType
			] );
		} elseif ( $pageTitle instanceof ForeignTitle ) {
			$query = 'title=' . wfUrlencode( $pageTitle->getFullText() ) . '&';
			$query .= wfArrayToCgi( [
				'action' => 'raw',
				'ctype' => $contentType
			] );
			// Note: This will return false if scriptDirUrl is not set for repo.
			return $this->file->repo->makeUrl( $query );
		}
		return null;
	}
}
