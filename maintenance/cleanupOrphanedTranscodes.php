<?php
/**
 * Re-queue selected, or all, transcodes
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class CleanupOrphanedTranscodes extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( "remove any entries in the transcode table that do not " .
			" have an associated entry in the image table." );
		$this->addOption(
			'throttle',
			'Wait this many milliseconds after each batch. Default: 200',
			false,
			true
		);
		$this->setBatchSize( 100 );
	}

	public function execute() {
		$this->output( "Cleanup transcodes:\n" );

		$throttle = $this->getOption( 'throttle', 200 );
		$affectedRows = 0;
		while ( true ) {
			$batchRows = $this->doWork();
			if ( $batchRows == 0 ) {
				break;
			}
			$this->output( ".. removed $batchRows entries...\n" );
			$affectedRows += $batchRows;
			usleep( $throttle * 1000 );
		}

		$this->output( "Removed $affectedRows transcode table entries for files which do not exist.\n" );
	}

	private function doWork(): int {
		return WebVideoTranscode::cleanupOrphanedTranscodes( $this->getBatchSize() );
	}
}

// Tells it to run the class
$maintClass = CleanupOrphanedTranscodes::class;
require_once RUN_MAINTENANCE_IF_MAIN;
