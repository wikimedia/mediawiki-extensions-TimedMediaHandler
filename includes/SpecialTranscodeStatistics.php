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

use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use OutputPage;
use SpecialPage;
use Wikimedia\Rdbms\Database;
use Wikimedia\Rdbms\SelectQueryBuilder;

class SpecialTranscodeStatistics extends SpecialPage {
	/** @var string[] */
	private $transcodeStates = [
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'active' => 'transcode_time_startwork IS NOT NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
		'failed' => 'transcode_time_startwork IS NOT NULL AND transcode_time_error IS NOT NULL',
		'queued' => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NULL',
		'missing' => 'transcode_time_addjob IS NULL',
	];

	/** @inheritDoc */
	public function __construct( $request = null, $par = null ) {
		parent::__construct( 'TranscodeStatistics', 'transcode-status' );
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
	private function renderState( $out, $state, $states, $showTable = true ) {
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
	 * @param string $state
	 * @param int $limit
	 *
	 * @return false|array
	 */
	private function getTranscodes( $state, $limit = 50 ) {
		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$fname = __METHOD__;

		return $cache->getWithSetCallback(
			$cache->makeKey( 'TimedMediaHandler-files', $state ),
			$cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $state, $limit, $fname ) {
				$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
				$dbr = $lbFactory->getReplicaDatabase();
				$setOpts += Database::getCacheSetOptions( $dbr );

				$files = [];
				$res = $dbr->newSelectQueryBuilder()
					->select( [ 'transcode_image_name', 'transcode_key' ] )
					->from( 'transcode' )
					->where( $this->transcodeStates[ $state ] )
					->limit( $limit )
					->orderBy( 'transcode_time_error', SelectQueryBuilder::SORT_DESC )
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

	/**
	 * @param string $state
	 * @param int $limit
	 *
	 * @return string
	 */
	private function getTranscodesTable( $state, $limit = 50 ) {
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

	/**
	 * @return array
	 */
	private function getStates() {
		$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
		$fname = __METHOD__;

		return $cache->getWithSetCallback(
			$cache->makeKey( 'TimedMediaHandler-states' ),
			$cache::TTL_MINUTE,
			function ( $oldValue, &$ttl, array &$setOpts ) use ( $fname ) {
				$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
				$dbr = $lbFactory->getReplicaDatabase();
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
						->select( [ 'COUNT(*) as count', 'transcode_key' ] )
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
					->select( [ 'COUNT(*) as count', 'transcode_key' ] )
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

	public function isListed() {
		return true;
	}
}
