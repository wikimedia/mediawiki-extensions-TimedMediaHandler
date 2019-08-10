<?php
/**
 * Report on number and size of transcodes.
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\TimedMediaHandler\TimedText;

class ConvertSubtitles extends Maintenance {

	private $count = 0;
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
		global $wgEnableLocalTimedText;
		if ( !$wgEnableLocalTimedText ) {
			$this->error( "Requires \$wgEnableLocalTimedText on.\n" );
			return false;
		}

		global $wgTimedTextNS;

		$dbr = wfGetDB( DB_REPLICA );
		$where = [ 'page_namespace' => $wgTimedTextNS ];
		if ( $this->hasOption( 'file' ) ) {
			$file = Title::newFromText( $this->getOption( 'file' ), NS_FILE );
			if ( $file ) {
				$where[] = 'page_title ' . $dbr->buildLike( $file->getDbKey() . '.', $dbr->anyString() );
			} else {
				$this->error( "Invalid file title\n" );
				return;
			}
		}
		$result = $dbr->select( 'page',
			[ 'page_namespace', 'page_title' ],
			$where,
			__METHOD__,
			[ 'ORDER BY' => 'page_namespace,page_title' ] );
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
		$page = WikiPage::factory( $title );
		if ( $page->isRedirect() ) {
			$title = $page->getRedirectTarget();
			$page = WikiPage::factory( $title );
		}
		$raw = $page->getContent()->getNativeData();

		$errors = [];
		$out = TextHandler::convertSubtitles(
			'srt',
			$this->getOption( 'format', 'vtt' ),
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

	public function formatError( TimedText\ParseError $error ) {
		return $error->getError() .
			" at line " .
			$error->getLine() .
			": " .
			$error->getInput();
	}
}

$maintClass = ConvertSubtitles::class; // Tells it to run the class
require_once RUN_MAINTENANCE_IF_MAIN;
