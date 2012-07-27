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

class CleanupUnusedTranscodes extends Maintenance {
	public function execute() {
		global $wgEnabledTranscodeSet;

		$this->output( "Cleanup unused transcodes:\n" );
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'transcode',
			'*',
			'transcode_key NOT IN ("'. implode('", "', $wgEnabledTranscodeSet ).'")',
			__METHOD__
		);
		foreach ( $res as $row ) {
			$this->output(
				'remove: '. $row->transcode_image_name . ' ' . $row->transcode_key . "\n"
			);
			$title = Title::newFromText( $row->transcode_image_name, NS_FILE );
			$file = wfFindFile( $title );
			WebVideoTranscode::removeTranscodes( $file, $row->transcode_key );
		}
		$this->output( "Finished!\n" );
	}
}

$maintClass = 'CleanupUnusedTranscodes'; // Tells it to run the class
require_once( RUN_MAINTENANCE_IF_MAIN );
