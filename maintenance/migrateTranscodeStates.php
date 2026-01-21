<?php
/**
 * @license GPL-2.0-or-later
 * @file
 */

use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use Wikimedia\Rdbms\RawSQLExpression;

// @codeCoverageIgnoreStart
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
// @codeCoverageIgnoreEnd

class MigrateTranscodeStates extends LoggedUpdateMaintenance {

	/**
	 * Mapping of transcode states to their corresponding conditions.
	 * Taken from the SpecialTranscodeStatistics class.
	 *
	 * @var array<int, string>
	 */
	private array $transcodeStates = [
		// Note: these queries should check prefixes of the index transcode_time_inx
		// phpcs:ignore Generic.Files.LineLength.TooLong
		WebVideoTranscode::STATE_ACTIVE  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		WebVideoTranscode::STATE_FAILED  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS     NULL AND transcode_time_error IS NOT NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		WebVideoTranscode::STATE_QUEUED  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS     NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		WebVideoTranscode::STATE_MISSING => 'transcode_time_addjob IS     NULL AND transcode_time_startwork IS     NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		WebVideoTranscode::STATE_SUCCESS => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS NOT NULL AND transcode_time_error IS     NULL',
	];

	/** @inheritDoc */
	protected function getUpdateKey() {
		return __CLASS__;
	}

	/** @inheritDoc */
	protected function doDBUpdates() {
		$batchSize = $this->getBatchSize();
		$dbw = $this->getDB( DB_PRIMARY );

		if ( !$dbw->fieldExists( 'transcode', 'transcode_state', __METHOD__ ) ) {
			$this->output( "Run update.php to create transcode_state.\n" );
			return false;
		}
		$this->output( "Migrating transcode states...\n" );

		$maxId = $dbw->newSelectQueryBuilder()
			->select( 'transcode_id' )
			->from( 'transcode' )
			->where( [ 'transcode_state' => null ] )
			->orderBy( 'transcode_id', 'DESC' )
			->limit( 1 )
			->caller( __METHOD__ )
			->fetchField();

		if ( $maxId === null ) {
			$this->output( "No transcode records found.\n" );
			return true;
		}

		$updated = 0;
		$last = 0;

		while ( $last < $maxId ) {
			foreach ( $this->transcodeStates as $state => $condition ) {
				$res = $dbw->newSelectQueryBuilder()
					->select( 'transcode_id' )
					->from( 'transcode' )
					->where( [ $dbw->expr( 'transcode_id', '>', $last ), 'transcode_state' => null ] )
					->andWhere( new RawSQLExpression( $condition ) )
					->orderBy( 'transcode_id' )
					->limit( $batchSize )
					->caller( __METHOD__ )
					->fetchFieldValues();

				if ( $res === [] ) {
					continue;
				}

				$dbw->newUpdateQueryBuilder()
					->update( 'transcode' )
					->set( [
						'transcode_state' => $state,
						'transcode_touched' => $dbw->timestamp(),
					] )
					->where( [ 'transcode_id' => $res ] )
					->caller( __METHOD__ )->execute();

				$updated += $dbw->affectedRows();
				$last = max( $last, ...$res );
			}

			$this->output( "... transcode_id=$last, updated $updated\n" );
			$this->waitForReplication();
		}

		$this->output( "Completed migration of transcode states, $updated rows updated.\n" );
		return true;
	}
}

// @codeCoverageIgnoreStart
$maintClass = MigrateTranscodeStates::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
