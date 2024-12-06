<?php
/**
 * Special:TranscodeStatistics
 *
 * Show some information about unprocessed jobs
 *
 * @file
 * @ingroup SpecialPage
 */

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Output\OutputPage;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use Wikimedia\ObjectCache\WANObjectCache;
use Wikimedia\Rdbms\Database;
use Wikimedia\Rdbms\IConnectionProvider;
use Wikimedia\Rdbms\SelectQueryBuilder;

class SpecialTranscodeStatistics extends SpecialPage {
	/**
	 * index on transcode_time_addjob,transcode_time_startwork,transcode_time_error,transcode_key,transcode_image_name
	 *
	 * @var string[]
	 */
	private $transcodeStates = [
		// Note: these queries should check prefixes of the index transcode_time_inx
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'active'  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'failed'  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS     NULL AND transcode_time_error IS NOT NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'queued'  => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS     NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'missing' => 'transcode_time_addjob IS     NULL AND transcode_time_startwork IS     NULL AND transcode_time_success IS     NULL AND transcode_time_error IS     NULL',
	];

	private IConnectionProvider $dbProvider;
	private WANObjectCache $cache;

	public function __construct(
		IConnectionProvider $dbProvider,
		WANObjectCache $cache
	) {
		parent::__construct( 'TranscodeStatistics', 'transcode-status' );
		$this->dbProvider = $dbProvider;
		$this->cache = $cache;
	}

	/** @inheritDoc */
	public function execute( $par ) {
		$this->setHeaders();
		$this->checkPermissions();
		$out = $this->getOutput();

		$out->addModuleStyles( 'mediawiki.special' );

		$states = $this->getStates();
		$this->renderState( $out, 'transcodes', $states, false );
		foreach ( $this->transcodeStates as $state => $condition ) {
			$this->renderState( $out, $state, $states, $state !== 'missing' );
		}
	}

	/**
	 * @param OutputPage $out
	 * @param string $state
	 * @param array $states
	 * @param bool $showTable
	 */
	private function renderState( $out, string $state, array $states, bool $showTable = true ): void {
		$allTranscodes = WebVideoTranscode::enabledTranscodes();
		if ( $states[ $state ][ 'total' ] ) {
			// Give grep a chance to find the usages:
			// timedmedia-derivative-state-transcodes, timedmedia-derivative-state-active,
			// timedmedia-derivative-state-queued, timedmedia-derivative-state-failed,
			// timedmedia-derivative-state-missing
			$out->addHTML(
				"<h2>"
				. $this->msg(
					'timedmedia-derivative-state-' . $state
				)->numParams( $states[ $state ]['total'] )->escaped()
				. "</h2>"
			);
			foreach ( $allTranscodes as $key ) {
				if ( isset( $states[$state][$key] ) && $states[$state][$key] ) {
					$out->addHTML(
						htmlspecialchars( $this->getLanguage()->formatNum( $states[ $state ][ $key ] ) )
						. ' '
						. $this->msg( 'timedmedia-derivative-desc-' . $key )->escaped()
						. "<br>" );
				}
			}
			if ( $showTable ) {
				$out->addHTML( $this->getTranscodesTable( $state ) );
			}
		}
	}

	/**
	 * @return false|array
	 */
	private function getTranscodes( string $state, int $limit = 50 ) {
		$fname = __METHOD__;

		return $this->cache->getWithSetCallback(
			$this->cache->makeKey( 'TimedMediaHandler-files', $state ),
			$this->cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $state, $limit, $fname ) {
				$dbr = $this->dbProvider->getReplicaDatabase();
				$setOpts += Database::getCacheSetOptions( $dbr );

				$files = [];
				$res = $dbr->newSelectQueryBuilder()
					->select( [ 'transcode_image_name', 'transcode_key' ] )
					->from( 'transcode' )
					->where( $this->transcodeStates[ $state ] )
					->limit( $limit )
					->orderBy( [
						'transcode_time_addjob',
						'transcode_time_startwork',
						'transcode_time_success',
						'transcode_time_error',
					], SelectQueryBuilder::SORT_DESC )
					->caller( $fname )
					->fetchResultSet();

				foreach ( $res as $row ) {
					$transcode = [];
					foreach ( $row as $k => $v ) {
						$transcode[ str_replace( 'transcode_', '', $k ) ] = $v;
					}
					$files[] = $transcode;
				}

				return $files;
			}
		);
	}

	private function getTranscodesTable( string $state, int $limit = 50 ): string {
		$linkRenderer = $this->getLinkRenderer();
		$table = '<table class="wikitable">' . "\n"
			. '<tr>'
			. '<th>' . $this->msg( 'timedmedia-transcodeinfo' )->escaped() . '</th>'
			. '<th>' . $this->msg( 'timedmedia-file' )->escaped() . '</th>'
			. '</tr>'
			. "\n";

		foreach ( $this->getTranscodes( $state, $limit ) as $transcode ) {
			$title = Title::newFromText( $transcode[ 'image_name' ], NS_FILE );
			$table .= '<tr>'
				. '<td>' . $this->msg(
					'timedmedia-derivative-desc-' . $transcode[ 'key' ]
				)->escaped() . '</td>'
				. '<td>' . $linkRenderer->makeLink( $title, $transcode[ 'image_name' ] ) . '</td>'
				. '</tr>'
				. "\n";
		}
		$table .= '</table>';
		return $table;
	}

	private function getStates(): array {
		$fname = __METHOD__;

		return $this->cache->getWithSetCallback(
			$this->cache->makeKey( 'TimedMediaHandler-states' ),
			$this->cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $fname ) {
				$dbr = $this->dbProvider->getReplicaDatabase();
				$setOpts += Database::getCacheSetOptions( $dbr );

				$allTranscodes = WebVideoTranscode::enabledTranscodes();

				$states = [];
				$states[ 'transcodes' ] = [ 'total' => 0 ];
				foreach ( $this->transcodeStates as $state => $condition ) {
					$states[ $state ] = [ 'total' => 0 ];
					foreach ( $allTranscodes as $type ) {
						// Important to pre-initialize, as can give
						// warnings if you don't have a lot of things in transcode table.
						$states[ $state ][ $type ] = 0;
					}
				}
				foreach ( $this->transcodeStates as $state => $condition ) {
					$cond = [ 'transcode_key' => $allTranscodes ];
					$cond[] = $condition;
					$res = $dbr->newSelectQueryBuilder()
						->select( [ 'count' => 'COUNT(*)', 'transcode_key' ] )
						->from( 'transcode' )
						->where( $cond )
						->groupBy( 'transcode_key' )
						->caller( $fname )
						->fetchResultSet();
					foreach ( $res as $row ) {
						$key = $row->transcode_key;
						$states[ $state ][ $key ] = $row->count;
						$states[ $state ][ 'total' ] += $states[ $state ][ $key ];
					}
				}
				$res = $dbr->newSelectQueryBuilder()
					->select( [ 'count' => 'COUNT(*)', 'transcode_key' ] )
					->from( 'transcode' )
					->where( [ 'transcode_key' => $allTranscodes ] )
					->groupBy( 'transcode_key' )
					->caller( $fname )
					->fetchResultSet();
				foreach ( $res as $row ) {
					$key = $row->transcode_key;
					$states[ 'transcodes' ][ $key ] = $row->count;
					$states[ 'transcodes' ][ $key ] -= $states[ 'queued' ][ $key ];
					$states[ 'transcodes' ][ $key ] -= $states[ 'missing' ][ $key ];
					$states[ 'transcodes' ][ $key ] -= $states[ 'active' ][ $key ];
					$states[ 'transcodes' ][ $key ] -= $states[ 'failed' ][ $key ];
					$states[ 'transcodes' ][ 'total' ] += $states[ 'transcodes' ][ $key ];
				}

				return $states;
			},
			[ 'lockTSE' => 30 ]
		);
	}

	/** @inheritDoc */
	protected function getGroupName() {
		return 'media';
	}
}
