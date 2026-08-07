<?php
/**
 * Re-queue selected, or all, transcodes
 */
// @codeCoverageIgnoreStart
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . "/TimedMediaMaintenance.php";
// @codeCoverageIgnoreEnd

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class RequeueTranscodes extends TimedMediaMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( "Remove and re-queue all successful media transcodes for files that currently exist." );

		$this->addOption( "key", "only process transcodes for the given format key or comma-delimited list",
			false, true );
		$this->addOption( "error", "also process transcodes that previously failed" );
		$this->addOption( "stalled", "also process active transcodes that were started but not yet finished" );
		$this->addOption( "missing", "also process transcodes for files that don't exist (and remove them)" );
		$this->addOption( "force", "force processing regardless of state (success, failed, active, and missing)" );
		$this->addOption( "remove", "only remove transcodes without re-queuing" );

		$this->addOption( "throttle", "throttle on the queue" );
		$this->addOption( "manual-override", "override soft limits on output file size" );
		$this->addOption( "remux", "use remuxing from another transcode where possible" );

		$this->addOption( "dry-run", "print remove/enqueue actions that would be taken" );
	}

	public function execute() {
		$this->output( "Iterating through all matching files...\n" );
		parent::execute();
		$this->output( "Finished!\n" );
	}

	/** @inheritDoc */
	public function processFile( File $file ) {
		$dbw = $this->getServiceContainer()->getConnectionProvider()->getReplicaDatabase();
		$repo = $file->repo;
		$dryRun = $this->hasOption( 'dry-run' );

		WebVideoTranscode::cleanupTranscodes( $file );

		$keys = [];
		if ( $this->hasOption( 'key' ) ) {
			$keys = preg_split( '/\s*,\s*/', $this->getOption( 'key' ) );
		}

		$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
		$toRemove = [];
		$toAdd = [];
		foreach ( $state as $key => $item ) {
			$path = WebVideoTranscode::getDerivativeFilePath( $file, $key );

			if ( $keys && !in_array( $key, $keys ) ) {
				$run = false;
			} elseif ( $this->hasOption( 'force' ) || $this->hasOption( 'remove' ) ) {
				$run = true;
			} elseif ( $this->hasOption( 'error' ) &&
				( (int)( $item['state'] ?? -1 ) === WebVideoTranscode::STATE_FAILED ) ) {
				$run = true;
			} elseif ( $this->hasOption( 'stalled' ) &&
				( (int)( $item['state'] ?? -1 ) === WebVideoTranscode::STATE_QUEUED ) ) {
				$run = true;
			} elseif ( $this->hasOption( 'missing' ) && $path && !$repo->fileExists( $path ) ) {
				$run = true;
			} elseif ( (int)( $item['state'] ?? -1 ) === WebVideoTranscode::STATE_MISSING ) {
				$run = true;
			} else {
				$run = false;
			}

			if ( $run ) {
				$this->output( $file->getName() . "\n" );
				$toRemove[] = $key;
				if ( !$this->hasOption( 'remove' ) ) {
					$toAdd[] = $key;
				}
			}
		}

		natsort( $toRemove );
		foreach ( $toRemove as $key ) {
			if ( $dryRun ) {
				$this->output( ".. would remove $key\n" );
			} else {
				$this->output( ".. removing $key\n" );
				WebVideoTranscode::removeTranscodes( $file, $key );
			}
		}

		$throttle = $this->hasOption( 'throttle' );
		natsort( $toAdd );
		foreach ( $toAdd as $key ) {
			if ( !WebVideoTranscode::isTranscodeEnabled( $file, $key ) ) {
				// don't enqueue too-big files
				$this->output( ".. skipping disabled transcode $key\n" );
				continue;
			}

			$startTime = microtime( true );
			$startSize = 0;
			if ( $throttle ) {
				$startSize = WebVideoTranscode::getQueueSize( $file, $key );
			}

			$options = [
				'manualOverride' => $this->hasOption( 'manual-override' ),
				'remux' => $this->hasOption( 'remux' ),
			];
			if ( $dryRun ) {
				$this->output( ".. would queue $key\n" );
			} else {
				$this->output( ".. queueing $key\n" );
				WebVideoTranscode::updateJobQueue( $file, $key, $options );
			}

			while ( $throttle ) {
				$size = WebVideoTranscode::getQueueSize( $file, $key );
				$delta = microtime( true ) - $startTime;
				if ( $size > $startSize && $delta < self::TIMEOUT_SEC ) {
					$this->output( ".. (queue $size)\n" );
					sleep( 1 );
				} else {
					$this->output( "\n" );
					break;
				}
			}
		}
	}

	/**
	 * If the queue counts get fouled up, go ahead and time out throttle checks
	 * after one hour.
	 */
	private const TIMEOUT_SEC = 3600;
}

// Tells it to run the class
// @codeCoverageIgnoreStart
$maintClass = RequeueTranscodes::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
