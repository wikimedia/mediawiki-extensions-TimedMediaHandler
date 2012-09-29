<?php
/**
 * Special:TimedMediaHandler
 *
 * Show some information about unprocessed jobs
 *
 * @file
 * @ingroup SpecialPage
 */

class SpecialTimedMediaHandler extends SpecialPage {
	private $transcodeStates = array(
		'active' => 'transcode_time_startwork IS NOT NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
		'failed' => 'transcode_error != ""',
		'queued' => 'transcode_time_startwork IS NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',

	);
	private $formats = array(
		'ogg' => 'img_name LIKE "%.ogv" OR img_name LIKE "%.ogg"',
		'webm' => 'img_name LIKE "%.webm"',
	);

	public function __construct( $request = null, $par = null ) {
		parent::__construct( 'TimedMediaHandler' );

	}

	public function execute( $par ) {
		// only show if user has right permissions
		if ( !$this->getUser()->isAllowed( 'transcode-status' ) ) {
			return;
		}

		$this->setHeaders();
		$out = $this->getOutput();

		$out->addModuleStyles( 'mediawiki.special' );

		$stats = $this->getStats();
		$out->addHTML(
			"<h2>"
			. $this->msg( 'timedmedia-videos',  $stats['videos']['total'] )->escaped()
			. "</h2>"
		);
		foreach ( $this->formats as $format => $condition ) {
			if ( $stats[ 'videos' ][ $format ] ) {
				$out->addHTML(
					$this->msg ( "timedmedia-$format-videos", $stats[ 'videos' ][ $format ] )->escaped()
					. "<br>"
				);
			}
		}
		$this->renderState( $out, 'transcodes', $stats, false );
		foreach ( $this->transcodeStates as $state => $condition ) {
			$this->renderState( $out, $state, $stats );
		}
	}

	/**
	 * @param OutputPage $out
	 * @param $state
	 * @param $stats
	 * @param bool $showTable
	 */
	private function renderState ( $out, $state, $stats, $showTable = true ) {
		global $wgEnabledTranscodeSet;
		if ( $stats[ $state ][ 'total' ] ) {
			$out->addHTML(
				"<h2>"
				. $this->msg( 'timedmedia-derivative-state-' . $state, $stats[ $state ]['total'] )->escaped()
				. "</h2>"
			);
			foreach( $wgEnabledTranscodeSet as $key ) {
				if ( $stats[ $state ][ $key ] ) {
					$out->addHTML(
						$stats[ $state ][ $key ]
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
	private function getTranscodes ( $state, $limit = 30 ) {
		$dbr = wfGetDB( DB_SLAVE );
		$files = array();
		$res = $dbr->select(
			'transcode',
			'*',
			$this->transcodeStates[ $state ], 
			__METHOD__,
			array( 'LIMIT' => $limit, 'ORDER BY' => 'transcode_time_error DESC' )
		);
		foreach( $res as $row ) {
			$transcode = array();
			foreach( $row as $k => $v ){
				$transcode[ str_replace( 'transcode_', '', $k ) ] = $v;
			}
			$files[] = $transcode;
		}
		return $files;
	}

	private function getTranscodesTable ( $state, $limit = 30 ) {
		$table = '<table class="wikitable">' . "\n"
			. '<tr>'
			. '<th>' . $this->msg( 'timedmedia-transcodeinfo' )->escaped() . '</th>'
			. '<th>' . $this->msg( 'timedmedia-file' )->escaped() . '</th>'
			. '</tr>'
			. "\n";

		foreach( $this->getTranscodes( $state, $limit ) as $transcode ) {
			$title = Title::newFromText( $transcode[ 'image_name' ], NS_FILE );
			$table .= '<tr>'
				. '<td>' . $this->msg('timedmedia-derivative-desc-' . $transcode[ 'key' ] )->escaped() . '</td>'
				. '<td>' . Linker::link( $title, $transcode[ 'image_name' ] ) . '</td>'
				. '</tr>'
				. "\n";
		}
		$table .=  '</table>';
		return $table;
	}

	private function getStats() {
		global $wgEnabledTranscodeSet;
		$stats = array();
		$dbr = wfGetDB( DB_SLAVE );
		$stats[ 'transcodes' ] = array( 'total' => 0 );
		foreach ( $this->transcodeStates as $state => $condition ) {
			$stats[ $state ] = array( 'total' => 0 );
		}
		foreach ( $wgEnabledTranscodeSet as $key ) {
			foreach ( $this->transcodeStates as $state => $condition ) {
				$stats[ $state ][ $key ] = (int)$dbr->selectField(
					'transcode',
					'COUNT(*)',
					'transcode_key = "' . $key . '" AND ' . $condition,
					__METHOD__
				);
				$stats[ $state ][ 'total' ] += $stats[ $state ][ $key ];
			}
			$stats[ 'transcodes' ][ $key ] = (int)$dbr->selectField(
				'transcode',
				'COUNT(*)',
				array( 'transcode_key' => $key ),
				__METHOD__
			);
			$stats[ 'transcodes' ][ $key ] -= $stats[ 'queued' ][ $key ];
			$stats[ 'transcodes' ][ 'total' ] += $stats[ 'transcodes' ][ $key ];
		}
		$stats[ 'videos' ] = array( 'total' => 0 );
		foreach( $this->formats as $format => $condition ) {
			$stats[ 'videos' ][ $format ] = (int)$dbr->selectField(
				'image',
				'COUNT(*)',
				'img_media_type = "VIDEO" AND (' . $condition . ')',
				__METHOD__
			);
			$stats[ 'videos' ][ 'total' ] += $stats[ 'videos' ][ $format ];
		}
		return $stats;
	}
}
