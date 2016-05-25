<?php
/**
 * Re-queue selected, or all, transcodes
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

class RequeueTranscodes extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "file", "re-queue selected formats only for the given file", false, true );
		$this->addOption( "key", "re-queue for given format key", false, true );
		$this->addOption( "error", "re-queue formats that previously failed", false, false );
		$this->addOption( "stalled", "re-queue formats that were started but not finished",
			false, false );
		$this->addOption( "all", "re-queue all output formats", false, false );
		$this->mDescription = "re-queue existing and missing media transcodes.";
	}

	public function execute() {
		$this->output( "Cleanup transcodes:\n" );
		$dbr = wfGetDB( DB_SLAVE );
		$where = [ 'img_media_type' => 'VIDEO' ];
		if ( $this->hasOption( 'file' ) ) {
			$title = Title::newFromText( $this->getOption( 'file' ), NS_FILE );
			if ( !$title ) {
				$this->output( "Invalid --file option provided" );
				return;
			}
			$where['img_name'] = $title->getDBkey();
		}
		$res = $dbr->select( 'image', [ 'img_name' ], $where, __METHOD__ );
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->img_name, NS_FILE );
			$file = wfLocalFile( $title );
			$handler = $file ? $file->getHandler() : null;
			if ( $file && $handler && $handler instanceof TimedMediaHandler ) {
				$this->output( $file->getName() . "\n" );
				$this->processFile( $file );
			}
		}
		$this->output( "Finished!\n" );
	}

	public function processFile( File $file ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;
		$transcodeSet = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );
		$dbw = wfGetDB( DB_MASTER );

		if ( $this->hasOption( "all" ) ) {
			$toAdd = $toRemove = $transcodeSet;
		} elseif ( $this->hasOption( "key" ) ) {
			$toAdd = $toRemove = [ $this->getOption( 'key' ) ];
		} else {
			$toAdd = $transcodeSet;
			$toRemove = [];
			$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
			foreach ( $state as $key => $item ) {
				if ( $this->hasOption( 'error' ) && ( $item['time_error'] || !$item['time_addjob'] ) ) {
					$toRemove[] = $key;
					continue;
				}
				if ( $this->hasOption( 'stalled' ) &&
					( $item['time_addjob'] && !$item['time_success'] && !$item['time_error'] ) ) {
					$toRemove[] = $key;
					continue;
				}
			}
		}

		$state = WebVideoTranscode::cleanupTranscodes( $file );

		if ( $toRemove ) {
			$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
			$keys = array_intersect( $toRemove, array_keys( $state ) );
			natsort( $keys );
			foreach ( $keys as $key ) {
				$this->output( ".. removing $key\n" );
				WebVideoTranscode::removeTranscodes( $file, $key );
			}
		}

		if ( $toAdd ) {
			$keys = $toAdd;
			$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
			natsort( $keys );
			foreach ( $keys as $key ) {
				if ( !WebVideoTranscode::isTranscodeEnabled( $file, $key ) ) {
					// don't enqueue too-big files
					continue;
				}
				if ( !array_key_exists( $key, $state ) || !$state[$key]['time_addjob'] ) {
					$this->output( ".. queueing $key\n" );
					WebVideoTranscode::updateJobQueue( $file, $key );
				}
			}
		}
	}
}

$maintClass = 'RequeueTranscodes'; // Tells it to run the class
require_once RUN_MAINTENANCE_IF_MAIN;
