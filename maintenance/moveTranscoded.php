<?php
/**
 * move transcoded files from thumb to transcoded container
 *
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = dirname( __FILE__ ) . '/../../..';
}
require_once( "$IP/maintenance/Maintenance.php" );

class MoveTranscoded extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "move transcoded files from thumb to transcoded container.";
	}
	public function execute() {
		global $wgEnabledTranscodeSet;

		$this->output( "Move transcoded files:\n" );
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'transcode', '*', array(), __METHOD__ );
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->transcode_image_name, NS_FILE );
			$file = wfLocalFile( $title );
			if ( !$file ) {
				continue;
			}
			$oldPath = $file->getThumbPath( $file->getName() . '.' . $row->transcode_key );

			$newPath = WebVideoTranscode::getDerivativeFilePath( $file, $row->transcode_key );
			if ($oldPath != $newPath) {
				if( $file->repo->fileExists( $oldPath ) ){
					if( $file->repo->fileExists( $newPath ) ){
						$res = $file->repo->quickPurge( $oldPath );
						if( !$res ){
							wfDebug( "Could not delete file $oldPath\n" );
						} else {
							$this->output( "deleted $oldPath, exists in transcoded container\n" );
						}
					} else {
						$this->output( " $oldPath => $newPath\n" );
						$file->repo->quickImport( $oldPath, $newPath );
						$file->repo->quickPurge( $oldPath );
					}
				}
			}

		}
		$this->output( "Finished!\n" );
	}
}

$maintClass = 'MoveTranscoded'; // Tells it to run the class
require_once( RUN_MAINTENANCE_IF_MAIN );
