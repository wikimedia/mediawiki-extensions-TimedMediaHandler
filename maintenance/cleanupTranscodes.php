<?php
/**
 * cleanup transcodes no longer defined in $wgEnabledTranscodeSet
 *
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = dirname( __FILE__ ) . '/../../..';
}
require_once( "$IP/maintenance/Maintenance.php" );

class CleanupTranscodes extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "key", "remove all transcodes for given key", false, true );
		$this->addOption( "all", "remove all transcodes", false, false );
		$this->mDescription = "cleanup transcodes left over after changing encoding profiles.";
	}
	public function execute() {
		global $wgEnabledTranscodeSet;

		if ( $this->hasOption( "all" ) ) {
			$where = array();
		} elseif ( $this->hasOption( "key" ) ) {
			$where = array( 'transcode_key' =>  $this->getOption( 'key' ) );
		} else {
			$where = 'transcode_key NOT IN ("'. implode('", "', $wgEnabledTranscodeSet ).'")';
		}
		$this->output( "Cleanup transcodes:\n" );
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'transcode', '*', $where, __METHOD__ );
		foreach ( $res as $row ) {
			$this->output(
				'remove: '. $row->transcode_image_name . ' ' . $row->transcode_key . "\n"
			);
			$title = Title::newFromText( $row->transcode_image_name, NS_FILE );
			$file = wfLocalFile( $title );
			WebVideoTranscode::removeTranscodes( $file, $row->transcode_key );
		}
		$this->output( "Finished!\n" );
	}
}

$maintClass = 'CleanupTranscodes'; // Tells it to run the class
require_once( RUN_MAINTENANCE_IF_MAIN );
