<?php
/**
 * Retry transcodes for a given key or error.
 * This script can be used after updating/fixing
 * the encoding pipeline to rerun transcodes
 * that are known to work now.
 *
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

class RetryTranscodes extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "key", "retry all failed transcodes for given key", false, true );
		$this->addOption( "error", "retry all failed transcodes matching error substring", false, true );
		$this->mDescription = "retry transcodes for given key or error";
	}
	public function execute() {
		if ( !$this->hasOption( "error" ) && !$this->hasOption( "key" ) ) {
			$this->output( "You have to provide --key and/or --error\n" );
			return;
		}
		$dbw = wfGetDB( DB_MASTER );
		$cond = [];
		$cond[] = 'transcode_time_error IS NOT NULL';
		if ( $this->hasOption( "key" ) ) {
			$cond['transcode_key'] = $this->getOption( 'key' );
		}
		if ( $this->hasOption( "error" ) ) {
			$cond[] = "transcode_error " . $dbw->buildLike( $dbw->anyString(),
				$this->getOption( 'error' ), $dbw->anyString() );
		}
		do {
			$res = $dbw->select( 'transcode', 'transcode_id',
				$cond, __METHOD__, [ 'LIMIT' => 100 ] );
			$ids = [];
			foreach ( $res as $row ) {
				$ids[] = $row->transcode_id;
			}
			if ( $ids ) {
				$dbw->delete( 'transcode',
					[ 'transcode_id' => $ids ], __METHOD__ );
				wfWaitForSlaves();
			}
		} while ( $ids );
	}
}

$maintClass = 'RetryTranscodes'; // Tells it to run the class
require_once ( RUN_MAINTENANCE_IF_MAIN );
