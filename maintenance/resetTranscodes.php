<?php
/**
 * reset stalled transcodes
 *
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = dirname( __FILE__ ) . '/../../..';
}
require_once( "$IP/maintenance/Maintenance.php" );

class ResetTranscodes extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Reset stalled transcodes, that are no longer in the job queue.";
	}
	public function execute() {
		global $wgEnabledTranscodeSet;
		$where = array(
			"transcode_time_startwork" => NULL,
			"transcode_time_error" => NULL
		);
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'transcode', '*', $where, __METHOD__ );
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->transcode_image_name, NS_FILE );
			// re-insert WebVideoTranscodeJob,
			// will only be added if not in queue
			// due to deduplication
			$job = new WebVideoTranscodeJob( $title, array(
				'transcodeMode' => 'derivative',
				'transcodeKey' => $row->transcode_key,
			) );
			$job->insert();
		}
	}
}

$maintClass = 'ResetTranscodes'; // Tells it to run the class
require_once( RUN_MAINTENANCE_IF_MAIN );
