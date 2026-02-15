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
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use Wikimedia\ObjectCache\WANObjectCache;
use Wikimedia\Rdbms\Database;
use Wikimedia\Rdbms\IConnectionProvider;
use Wikimedia\Rdbms\SelectQueryBuilder;

class SpecialTranscodeStatistics extends SpecialPage {

	/**
	 * @var string[]
	 */
	private $transcodeStates = [
		'success' => WebVideoTranscode::STATE_SUCCESS,
		'active'  => WebVideoTranscode::STATE_ACTIVE,
		'failed'  => WebVideoTranscode::STATE_FAILED,
		'queued'  => WebVideoTranscode::STATE_QUEUED,
		'missing' => WebVideoTranscode::STATE_MISSING,
	];

	public function __construct(
		private readonly IConnectionProvider $dbProvider,
		private readonly WANObjectCache $cache,
		private readonly TranscodePresets $transcodePresets,
	) {
		parent::__construct( 'TranscodeStatistics', 'transcode-status' );
	}

	/** @inheritDoc */
	public function execute( $par ) {
		$this->setHeaders();
		$this->checkPermissions();
		$out = $this->getOutput();
		$out->addModuleStyles( 'mediawiki.special' );

		$states = $this->getStateCounts();

		foreach ( $this->transcodeStates as $state => $condition ) {
			$this->renderState(
				$out,
				$state,
				$states,
				$state === 'active' || $state === 'failed' || $state === 'queued'
			);
		}
	}

	private function renderState(
		OutputPage $out,
		string $state,
		array $states,
		bool $showTable = true
	): void {
		$allTranscodes = $this->transcodePresets->enabledTranscodes();
		if ( $states[ $state ][ 'total' ] ) {
			// Give grep a chance to find the usages:
			// timedmedia-derivative-state-transcodes, timedmedia-derivative-state-active,
			// timedmedia-derivative-state-queued, timedmedia-derivative-state-failed,
			// timedmedia-derivative-state-missing
			$out->addHTML(
				"<h2>"
				. $this->msg(
					'timedmedia-derivative-state-' . ( $state === 'success' ? 'transcodes' : $state )
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

	private function getTranscodes( string $stateName, int $limit = 50 ): false|array {
		$fname = __METHOD__;
		$state = $this->transcodeStates[$stateName] ?? null;

		return $this->cache->getWithSetCallback(
			$this->cache->makeKey( 'TimedMediaHandler-files', $stateName, $limit ),
			$this->cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $state, $limit, $fname ) {
				$dbr = $this->dbProvider->getReplicaDatabase();
				$setOpts += Database::getCacheSetOptions( $dbr );

				$files = [];
				$res = $dbr->newSelectQueryBuilder()
					->select( [ 'transcode_image_name', 'transcode_key' ] )
					->from( 'transcode' )
					->where( [
						'transcode_key' => $this->transcodePresets->enabledTranscodes(),
						'transcode_state' => $state,
					] )
					->limit( $limit )
					->orderBy( 'transcode_touched', SelectQueryBuilder::SORT_DESC )
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

	private function getStateCounts(): array {
		$fname = __METHOD__;

		$allTranscodes = $this->transcodePresets->enabledTranscodes();
		return $this->cache->getWithSetCallback(
			$this->cache->makeKey( 'TimedMediaHandler-states' ),
			$this->cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $fname, $allTranscodes ) {
				$dbr = $this->dbProvider->getReplicaDatabase();
				$setOpts += Database::getCacheSetOptions( $dbr );

				$states = [];
				foreach ( $this->transcodeStates as $state => $condition ) {
					$states[ $state ] = [ 'total' => 0 ];
					foreach ( $allTranscodes as $type ) {
						// Important to pre-initialize, as can give
						// warnings if you don't have a lot of things in transcode table.
						$states[ $state ][ $type ] = 0;
					}
				}

				$res = $this->dbProvider->getReplicaDatabase()->newSelectQueryBuilder()
					->select( [ 'count' => 'COUNT(*)', 'transcode_key', 'transcode_state' ] )
					->from( 'transcode' )
					->where( [ 'transcode_key' => $allTranscodes ] )
					->groupBy( [ 'transcode_key', 'transcode_state' ] )
					->caller( $fname )
					->fetchResultSet();

				$stateMap = array_flip( $this->transcodeStates );
				foreach ( $res as $row ) {
					$key = $row->transcode_key;
					$stateVal = (int)$row->transcode_state;
					$stateName = $stateMap[ $stateVal ];

					$states[ $stateName ][ $key ] = (int)$row->count;
					$states[ $stateName ][ 'total' ] += $states[ $stateName ][ $key ];
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
