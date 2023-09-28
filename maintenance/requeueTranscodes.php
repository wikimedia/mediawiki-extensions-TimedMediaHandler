<?php
/**
 * Re-queue selected, or all, transcodes
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . "/TimedMediaMaintenance.php";

use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class RequeueTranscodes extends TimedMediaMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "key", "re-queue for given format key", false, true );
		$this->addOption( "error", "re-queue formats that previously failed" );
		$this->addOption( "stalled", "re-queue formats that were started but not finished" );
		$this->addOption( "missing", "queue formats that were never started" );
		$this->addOption( "all", "re-queue all output formats" );
		$this->addOption( "throttle", "throttle on the queue" );
		$this->addOption( "manual-override", "override soft limits on output file size" );
		$this->addDescription( "re-queue existing and missing media transcodes." );
	}

	public function execute() {
		$this->output( "Cleanup transcodes:\n" );
		parent::execute();
		$this->output( "Finished!\n" );
	}

	/**
	 * @param File $file
	 */
	public function processFile( File $file ) {
		$this->output( $file->getName() . "\n" );

		$transcodeSet = WebVideoTranscode::enabledTranscodes();
		$dbw = wfGetDB( DB_PRIMARY );

		WebVideoTranscode::cleanupTranscodes( $file );

		if ( $this->hasOption( "all" ) ) {
			$toAdd = $toRemove = $transcodeSet;
		} elseif ( $this->hasOption( "key" ) ) {
			$toAdd = $toRemove = [ $this->getOption( 'key' ) ];
		} else {
			$toAdd = $transcodeSet;
			$toRemove = [];
			$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
			foreach ( $state as $key => $item ) {
				if ( $this->hasOption( 'error' ) && $item['time_error'] ) {
					$toRemove[] = $key;
					continue;
				}
				if ( $this->hasOption( 'stalled' ) &&
					( $item['time_addjob'] && !$item['time_success'] && !$item['time_error'] ) ) {
					$toRemove[] = $key;
					continue;
				}
				if ( $this->hasOption( 'missing' ) &&
					( !$item['time_addjob'] ) ) {
					$toRemove[] = $key;
					continue;
				}
			}
		}

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

					$manualOverride = $this->hasOption( 'manual-override' );
					if ( !$this->hasOption( 'throttle' ) ) {
						WebVideoTranscode::updateJobQueue( $file, $key, $manualOverride );
					} else {
						$startSize = WebVideoTranscode::getQueueSize( $file, $key );
						WebVideoTranscode::updateJobQueue( $file, $key, $manualOverride );
						while ( true ) {
							$size = WebVideoTranscode::getQueueSize( $file, $key );
							if ( $size > $startSize ) {
								$this->output( ".. (queue $size) " );
								sleep( 1 );
							} else {
								$this->output( "\n" );
								break;
							}
						}
					}
				}
			}
		}
	}
}

// Tells it to run the class
$maintClass = RequeueTranscodes::class;
require_once RUN_MAINTENANCE_IF_MAIN;
