<?php
/**
 * Timed Text handling for mediaWiki
 *
 * Timed text support is presently fairly limited. Unlike Ogg and WebM handlers,
 * timed text does not extend the TimedMediaHandler class.
 *
 * TODO On "new" timedtext language save purge all pages where file exists
 */

namespace MediaWiki\TimedMediaHandler\Handlers\TextHandler;

use Exception;
use MediaWiki\FileRepo\File\File;
use MediaWiki\FileRepo\File\ForeignDBFile;
use MediaWiki\FileRepo\IForeignRepoWithDB;
use MediaWiki\FileRepo\IForeignRepoWithMWApi;
use MediaWiki\FileRepo\LocalRepo;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\TimedText\ParseError;
use MediaWiki\TimedMediaHandler\TimedText\SrtReader;
use MediaWiki\TimedMediaHandler\TimedText\SrtWriter;
use MediaWiki\TimedMediaHandler\TimedText\VttWriter;
use MediaWiki\TimedMediaHandler\TimedTextPage;
use MediaWiki\Title\Title;
use RuntimeException;
use Wikimedia\Rdbms\IExpression;
use Wikimedia\Rdbms\IResultWrapper;
use Wikimedia\Rdbms\LikeValue;

class TextHandler {
	/** Lazy init remote Namespace number */
	public ?int $remoteNs = null;
	/** Lazy init remote Namespace name */
	public ?string $remoteNsName = null;

	/**
	 * @param File $file
	 * @param array $formats Array of string keys for subtitle formats
	 */
	public function __construct(
		protected readonly File $file,
		protected readonly array $formats = [ TimedTextPage::VTT_SUBTITLE_FORMAT, TimedTextPage::SRT_SUBTITLE_FORMAT ],
	) {
	}

	/**
	 * Get the timed text tracks elements as an associative array
	 */
	public function getTracks(): array {
		if ( $this->file->isLocal() || $this->file instanceof ForeignDBFile ) {
			$data = $this->getTextPagesFromDb();
			return $data ? $this->getTextTracksFromRows( $data ) : [];
		} elseif ( $this->file->getRepo() instanceof IForeignRepoWithMWApi ) {
			return $this->getRemoteTextSources( $this->file );
		}
		return [];
	}

	public function getTimedTextNamespace(): ?int {
		$repo = $this->file->getRepo();

		if ( $this->file->isLocal() ) {
			return NS_TIMEDTEXT;
		}
		if ( $repo instanceof IForeignRepoWithDB ) {
			$config = MediaWikiServices::getInstance()->getMainConfig();
			$timedTextForeignNamespaces = $config->get( 'TimedTextForeignNamespaces' );
			$wikiID = $repo->getReplicaDB()->getDomainID();
			// if failed to get namespace via IForeignRepoWithDB, return NS_TIMEDTEXT
			return $timedTextForeignNamespaces[$wikiID] ?? NS_TIMEDTEXT;
		}
		if ( $repo instanceof IForeignRepoWithMWApi ) {
			if ( $this->remoteNs !== null ) {
				return $this->remoteNs;
			}

			// Get the namespace data from the image api repo:
			// fetchImageQuery query caches results
			$data = $repo->fetchImageQuery( [
				'meta' => 'siteinfo',
				'siprop' => 'namespaces'
			] );

			if ( isset( $data['query']['namespaces'] ) ) {
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

			// Return the remote Ns, if found
			if ( $this->remoteNs !== null ) {
				return $this->remoteNs;
			}
		}

		return null;
	}

	/**
	 * Retrieve a list of TimedText pages in the database that start with
	 * the name of the file associated with this handler.
	 *
	 * If the file is on a foreign repo, will query the ForeignDb
	 */
	private function getTextPagesFromDb(): ?IResultWrapper {
		$ns = $this->getTimedTextNamespace();
		if ( $ns === null ) {
			wfDebug( 'Repo: ' . $this->file->getRepoName() . " does not have a TimedText namespace \n" );
			// No timed text namespace, don't try to look up timed text tracks
			return null;
		}

		$repo = $this->file->getRepo();
		if ( $repo instanceof LocalRepo ) {
			$dbr = $repo->getReplicaDB();
			$prefix = $this->file->getTitle()->getDBkey();
			return $dbr->newSelectQueryBuilder()
				->select( [ 'page_namespace', 'page_title' ] )
				->from( 'page' )
				->where( [
					'page_namespace' => $ns,
					$dbr->expr( 'page_title', IExpression::LIKE, new LikeValue( $prefix, $dbr->anyString() ) ),
				] )
				->limit( 300 )
				->orderBy( 'page_title' )
				->caller( __METHOD__ )
				->fetchResultSet();
		}

		return null;
	}

	/**
	 * Build the api query to find TimedText pages belonging to a remote file
	 */
	private function getRemoteTextPagesQuery(): ?array {
		$ns = $this->getTimedTextNamespace();
		if ( $ns === null ) {
			wfDebug( 'Repo: ' . $this->file->getRepoName() . " does not have a TimedText namespace \n" );
			// No timed text namespace, don't try to look up timed text tracks
			return null;
		}
		$canonicalTitle = Title::makeName(
			$this->file->getTitle()->getNamespace(),
			$this->file->getTitle()->getDbKey(),
			'',
			'',
			true
		);
		return [
			'action' => 'query',
			// For a remote wiki, we need to always use canonical namespace names
			'titles' => $canonicalTitle,
			'prop' => 'videoinfo',
			'viprop' => 'timedtext',
			'formatversion' => '2',
		];
	}

	/**
	 * Retrieve the text sources belonging to a remote file
	 * @param File $file The File's repo must implement IForeignRepoWithMWApi
	 */
	private function getRemoteTextSources( File $file ): array {
		$regenerator = function () use ( $file ) {
			/** @var IForeignRepoWithMWApi $repo */
			$repo = $file->getRepo();
			'@phan-var IForeignRepoWithMWApi $repo';
			wfDebug( "Get text tracks from remote api \n" );
			$query = $this->getRemoteTextPagesQuery();
			// Error in getting timed text namespace return empty array;
			if ( $query === null || !( $repo instanceof IForeignRepoWithMWApi ) ) {
				return [];
			}

			$data = $repo->fetchImageQuery( $query );

			return $this->getTextTracksFromData( $data );
		};

		$repoInfo = $file->getRepo()->getInfo();
		$cacheTTL = $repoInfo['descriptionCacheExpiry'] ?? 0;

		if ( $cacheTTL > 0 ) {
			$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
			$textTracks = $cache->getWithSetCallback(
				$cache->makeKey(
					'RemoteTextTracks-url',
					$this->file->getRepoName(),
					$this->file->getName()
				),
				$cacheTTL,
				$regenerator
			);
		} else {
			$textTracks = $regenerator();
		}

		return $textTracks;
	}

	/**
	 * Build an array of track information using a Database result
	 * Handles both local and foreign Db results
	 *
	 * @param IResultWrapper $data Database result with page titles
	 */
	public function getTextTracksFromRows( IResultWrapper $data ): array {
		$textTracks = [];

		$services = MediaWikiServices::getInstance();
		$langNames = $services->getLanguageNameUtils()->getLanguageNames();
		$languageFactory = $services->getLanguageFactory();

		foreach ( $data as $row ) {
			$titleParts = explode( '.', $row->page_title );
			if ( count( $titleParts ) >= 3 ) {
				$timedTextExtension = array_pop( $titleParts );
				$languageKey = array_pop( $titleParts );
			} else {
				continue;
			}
			// If there is no valid language continue:
			if ( !isset( $langNames[ $languageKey ] ) ) {
				continue;
			}

			$language = $languageFactory->getLanguage( $languageKey );
			foreach ( $this->formats as $format ) {
				$textTracks[] = [
					'src' => $this->getFullURL( $languageKey, $format ),
					'kind' => 'subtitles',
					'type' => $this->getContentType( $format ),
					'srclang' => $language->getHtmlCode(),
					'dir' => $language->getDir(),
					'label' => wfMessage( 'timedmedia-subtitle-language',
						$langNames[ $languageKey ],
						$languageKey )->text()
				];
			}
		}
		return $textTracks;
	}

	/**
	 * Build an array of track information using an API result
	 * @param mixed $data JSON decoded result from a query API request
	 */
	public function getTextTracksFromData( $data ): array {
		$textTracks = [];
		foreach ( $data['query']['pages'] ?? [] as $page ) {
			foreach ( $page['videoinfo'] ?? [] as $info ) {
				foreach ( $info['timedtext'] ?? [] as $track ) {
					foreach ( $this->formats as $format ) {
						if ( ( $track['type'] ?? '' ) === $this->getContentType( $format ) ) {
							// Add validation ?
							$textTracks[] = $track;
						}
					}
				}
			}
		}
		return $textTracks;
	}

	public function getContentType( string $timedTextExtension ): string {
		if ( $timedTextExtension === TimedTextPage::SRT_SUBTITLE_FORMAT ) {
			return 'text/x-srt';
		}
		if ( $timedTextExtension === TimedTextPage::VTT_SUBTITLE_FORMAT ) {
			return 'text/vtt';
		}
		return '';
	}

	/**
	 * Retrieve a url to the raw subtitle file
	 * Only use for local and foreignDb requests
	 */
	public function getFullURL( string $lang, string $format ): string {
		$title = $this->file->getTitle();
		// Note we need to use the canonical namespace in case this is a
		// foreign DB repo (Wikimedia Commons style) in a different language
		// than the current site.
		$canonicalTitle = Title::makeName(
			$title->getNamespace(),
			$title->getDbKey(),
			'',
			'',
			true
		);
		$params = [
			'action' => 'timedtext',
			'title' => $canonicalTitle,
			'lang' => $lang,
			'trackformat' => $format,
		];
		if ( !$this->file->isLocal() ) {
			$params['origin'] = '*';
		}
		$query = wfArrayToCgi( $params );

		// Note: This will return false if scriptDirUrl is not set for repo.
		$url = $this->file->getRepo()->makeUrl( $query, 'api' );
		if ( $url === false ) {
			throw new RuntimeException( 'TimedText subtitles requires scriptDirUrl to be set in $wgForeignFileRepos' );
		}
		return $url;
	}

	/**
	 * Convert subtitles between SubRIP (SRT) and WebVTT, laxly.
	 *
	 * @param string $from source format, one of TimedTextPage::SRT_SUBTITLE_FORMAT
	 *  or TimedTextPage::VTT_SUBTITLE_FORMAT
	 * @param string $to destination format, one of TimedTextPage::SRT_SUBTITLE_FORMAT
	 *  or TimedTextPage::VTT_SUBTITLE_FORMAT
	 * @param string $data source-formatted subtitles
	 * @param ParseError[] &$errors optional outparam to capture errors
	 * @return string destination-formatted subtitles
	 */
	public static function convertSubtitles( string $from, string $to, string $data, array &$errors = [] ): string {
		// Note that we convert even if the format is the same, to ensure
		// data format integrity.
		//
		// @todo cache the conversion in memcached
		switch ( $from ) {
			case TimedTextPage::SRT_SUBTITLE_FORMAT:
				$reader = new SrtReader();
				break;
			case TimedTextPage::VTT_SUBTITLE_FORMAT:
				// @todo once VttReader is implemented, use it.
				// For now throw an exception rather than a fatal error.
				throw new RuntimeException( 'vtt source pages are not yet supported' );
			default:
				throw new RuntimeException( 'Unsupported timedtext filetype' );
		}
		switch ( $to ) {
			case TimedTextPage::SRT_SUBTITLE_FORMAT:
				$writer = new SrtWriter();
				break;
			case TimedTextPage::VTT_SUBTITLE_FORMAT:
				$writer = new VttWriter();
				break;
			default:
				throw new RuntimeException( 'Unsupported timedtext filetype' );
		}
		try {
			$reader->read( $data );
			$cues = $reader->getCues();
			$errors = $reader->getErrors();

			return $writer->write( $cues );
		} catch ( Exception $e ) {
			throw new RuntimeException( 'Timed text track conversion failed: ' .
				$e->getMessage() );
		}
	}
}
