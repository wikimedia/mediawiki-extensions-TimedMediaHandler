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
		global $wgEnabledTranscodeSet;

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
			. wfMsgHtml ( 'timedmedia-videos',  $stats['videos']['total'] )
			. "</h2>"
		);
		foreach ( $this->formats as $format => $condition ) {
			if ( $stats[ 'videos' ][ $format ] ) {
				$out->addHTML(
					wfMsgHtml ( "timedmedia-$format-videos", $stats[ 'videos' ][ $format ] )
					. "<br>"
				);
			}
		}
		$this->renderState( $out, 'transcodes', $stats, false );
		foreach ( $this->transcodeStates as $state => $condition ) {
			$this->renderState( $out, $state, $stats );
		}
	}

	private function renderState ( $out, $state, $stats, $showTable = true ) {
		global $wgEnabledTranscodeSet;
		if ( $stats[ $state ][ 'total' ] ) {
			$out->addHTML(
				"<h2>"
				. wfMsgHtml( 'timedmedia-derivative-state-' . $state, $stats['transcodes']['total'] )
				. "</h2>"
			);
			foreach( $wgEnabledTranscodeSet as $key ) {
				if ( $stats[ $state ][ $key ] ) {
					$out->addHTML(
						$stats[ $state ][ $key ]
						. ' '
						. wfMsgHtml( 'timedmedia-derivative-desc-' . $key )
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
			. '<th>' . wfMsgHtml( 'timedmedia-transcodeinfo' ) . '</th>'
			. '<th>' . wfMsgHtml( 'timedmedia-file' ) . '</th>'
			. '</tr>'
			. "\n";

		foreach( $this->getTranscodes( $state, $limit ) as $transcode ) {
			$title = Title::newFromText( $transcode[ 'image_name' ], NS_FILE );
			$table .= '<tr>'
				. '<td>' . wfMsgHtml('timedmedia-derivative-desc-' . $transcode[ 'key' ]) . '</td>'
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
