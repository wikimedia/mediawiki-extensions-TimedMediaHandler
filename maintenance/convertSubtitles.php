<?php
/**
 * Report on number and size of transcodes.
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\Content\TextContent;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
use MediaWiki\TimedMediaHandler\TimedText;
use MediaWiki\TimedMediaHandler\TimedTextPage;
use MediaWiki\Title\Title;
use Wikimedia\Rdbms\IExpression;
use Wikimedia\Rdbms\LikeValue;

class ConvertSubtitles extends Maintenance {

	/** @var int */
	private $count = 0;
	/** @var int */
	private $failed = 0;

	public function __construct() {
		parent::__construct();
		$this->addOption( "file", "process a single given TimedText 'file'", false, true );
		$this->addOption( "format", "output format, one of 'vtt' or 'srt'", false, true );
		$this->addOption( "dump", "dump output to stdout along with status", false, false );
		$this->addDescription( "convert one or more subtitle files and report on success." );
		$this->requireExtension( 'TimedMediaHandler' );
	}

	public function execute() {
		$services = $this->getServiceContainer();
		$dbr = $services->getDBLoadBalancerFactory()->getReplicaDatabase();
		$where = [ 'page_namespace' => $services->getMainConfig()->get( 'TimedTextNS' ) ];
		if ( $this->hasOption( 'file' ) ) {
			$file = Title::newFromText( $this->getOption( 'file' ), NS_FILE );
			if ( $file ) {
				$where[] = $dbr->expr( 'page_title', IExpression::LIKE,
					new LikeValue( $file->getDbKey() . '.', $dbr->anyString() ) );
			} else {
				$this->fatalError( "Invalid file title\n" );
			}
		}
		$result = $dbr->newSelectQueryBuilder()
			->select( [ 'page_namespace', 'page_title' ] )
			->from( 'page' )
			->where( $where )
			->orderBy( [ 'page_namespace', 'page_title' ] )
			->caller( __METHOD__ )
			->fetchResultSet();
		foreach ( $result as $row ) {
			$data = $this->processWork( [
				'page_namespace' => $row->page_namespace,
				'page_title' => $row->page_title,
			] );
			$this->count++;
			if ( $data['result'] === 0 ) {
				$this->output( "success on {$data['page_title']}\n" );
			} else {
				$err = implode( "\n    ", $data['errors'] );
				$this->output( "failure on {$data['page_title']}: $err\n" );
				$this->failed++;
			}
			if ( $this->hasOption( 'dump' ) ) {
				$this->output( $data['output'] . "\n" );
			}
		}

		$this->output( "$this->count subtitle conversions, $this->failed failed\n" );
	}

	/**
	 * Actual converter work. Split out so it's easy to parallelize
	 * or reuse in future.
	 *
	 * @param array $data
	 * @return array
	 */
	public function processWork( $data ) {
		$title = Title::makeTitle( $data['page_namespace'], $data['page_title'] );
		$wikiPageFactory = $this->getServiceContainer()->getWikiPageFactory();
		$page = $wikiPageFactory->newFromTitle( $title );
		if ( $page->isRedirect() ) {
			$title = $page->getRedirectTarget();
			$page = $wikiPageFactory->newFromTitle( $title );
		}
		$content = $page->getContent();
		$raw = $content instanceof TextContent ? $content->getText() : '';

		$errors = [];
		$out = TextHandler::convertSubtitles(
			TimedTextPage::SRT_SUBTITLE_FORMAT,
			$this->getOption( 'format', TimedTextPage::VTT_SUBTITLE_FORMAT ),
			$raw,
			$errors
		);
		if ( count( $errors ) ) {
			return $data + [
				'result' => 1,
				'errors' => array_map( [ $this, 'formatError' ], $errors ),
				'output' => $out,
			];
		} else {
			return $data + [
				'result' => 0,
				'output' => $out,
			];
		}
	}

	/**
	 * @param TimedText\ParseError $error
	 *
	 * @return string
	 */
	public function formatError( TimedText\ParseError $error ) {
		return $error->getError() .
			" at line " .
			$error->getLine() .
			": " .
			$error->getInput();
	}
}

// Tells it to run the class
$maintClass = ConvertSubtitles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
