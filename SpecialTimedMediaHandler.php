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
		'failed' => 'transcode_error != "" AND transcode_time_success IS NULL',
		'queued' => 'transcode_time_startwork IS NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',

	);
	private $formats = array(
		'ogg' => 'img_major_mime="application" AND img_minor_mime = "ogg"',
		'webm' => 'img_major_mime="video" AND img_minor_mime = "webm"',
	);
	private $audioFormats = array(
		'ogg' => 'img_major_mime="application" AND img_minor_mime = "ogg"',
		'webm' => 'img_major_mime="audio" AND img_minor_mime = "webm"',
		'flac' => 'img_major_mime="audio" AND img_minor_mime="x-flac"',
		'wav' => 'img_major_mime="audio" AND img_minor_mime="wav"',
	);

	public function __construct( $request = null, $par = null ) {
		parent::__construct( 'TimedMediaHandler', 'transcode-status' );
	}

	public function execute( $par ) {
		$this->setHeaders();
		$out = $this->getOutput();

		$out->addModuleStyles( 'mediawiki.special' );

		$stats = $this->getStats();

		foreach( array( 'audios', 'videos' ) as $type ) {
			// for grep timedmedia-audios, timedmedia-videos
			$out->addHTML(
				"<h2>"
				. $this->msg( 'timedmedia-' . $type )->numParams( $stats[$type]['total'] )->escaped()
				. "</h2>"
			);
			// Give grep a chance to find the usages: timedmedia-ogg-videos, timedmedia-webm-videos,
			// timedmedia-ogg-audios, timedmedia-flac-audios, timedmedia-wav-audios
			$formats = $type == 'audios' ? $this->audioFormats : $this->formats;
			foreach ( $formats as $format => $condition ) {
				if ( $stats[ $type ][ $format ] ) {
					$out->addHTML(
						$this->msg( "timedmedia-$format-$type" )->numParams( $stats[ $type ][ $format ] )->escaped()
						. Html::element( 'br' )
					);
				}
			}
		}

		$states = $this->getStates();
		$this->renderState( $out, 'transcodes', $states, false );
		foreach ( $this->transcodeStates as $state => $condition ) {
			$this->renderState( $out, $state, $states );
		}
	}

	/**
	 * @param OutputPage $out
	 * @param $state
	 * @param $states
	 * @param bool $showTable
	 */
	private function renderState ( $out, $state, $states, $showTable = true ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;
		$allTranscodes = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );
		if ( $states[ $state ][ 'total' ] ) {
			// Give grep a chance to find the usages:
			// timedmedia-derivative-state-transcodes, timedmedia-derivative-state-active,
			// timedmedia-derivative-state-queued, timedmedia-derivative-state-failed
			$out->addHTML(
				"<h2>"
				. $this->msg( 'timedmedia-derivative-state-' . $state, $states[ $state ]['total'] )->escaped()
				. "</h2>"
			);
			foreach( $allTranscodes as $key ) {
				if ( isset( $states[ $state ] )
					&& isset( $states[ $state ][ $key ] )
					&& $states[ $state ][ $key ] ) {
					$out->addHTML(
						$states[ $state ][ $key ]
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

	private function getTranscodes ( $state, $limit = 50 ) {
		global $wgMemc;
		$memcKey = wfMemcKey( 'TimedMediaHandler', 'files', $state );
		$files = $wgMemc->get( $memcKey );
		if ( !$files ) {
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
			$wgMemc->add( $memcKey, $files, 60 );
		}
		return $files;
	}

	private function getTranscodesTable ( $state, $limit = 50 ) {
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
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet, $wgMemc;
		$allTranscodes = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );

		$memcKey= wfMemcKey( 'TimedMediaHandler', 'stats', '1' /* version */ );
		$stats = $wgMemc->get( $memcKey );
		if ( !$stats ) {
			$dbr = wfGetDB( DB_SLAVE );
			$stats = array();
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
			$stats[ 'audios' ] = array( 'total' => 0 );
			foreach( $this->audioFormats as $format => $condition ) {
				$stats[ 'audios' ][ $format ] = (int)$dbr->selectField(
					'image',
					'COUNT(*)',
					'img_media_type = "AUDIO" AND (' . $condition . ')',
					__METHOD__
				);
				$stats[ 'audios' ][ 'total' ] += $stats[ 'audios' ][ $format ];
			}
		}
		return $stats;
	}

	private function getStates() {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet, $wgMemc;
		$allTranscodes = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );

		$memcKey = wfMemcKey( 'TimedMediaHandler', 'states' );
		$states = $wgMemc->get( $memcKey );
		if ( !$states ) {
			$dbr = wfGetDB( DB_SLAVE );
			$states = array();
			$states[ 'transcodes' ] = array( 'total' => 0 );
			foreach ( $this->transcodeStates as $state => $condition ) {
				$states[ $state ] = array( 'total' => 0 );
				foreach( $allTranscodes as $type ) {
					// Important to pre-initialize, as can give
					// warnings if you don't have a lot of things in transcode table.
					$states[ $state ][ $type ] = 0;
				}
			}
			foreach ( $this->transcodeStates as $state => $condition ) {
				$cond = array( 'transcode_key' => $allTranscodes );
				$cond[] = $condition;
				$res = $dbr->select( 'transcode',
					array('COUNT(*) as count', 'transcode_key'),
					$cond,
					__METHOD__,
					array( 'GROUP BY' => 'transcode_key' )
				);
				foreach( $res as $row ) {
					$key = $row->transcode_key;
					$states[ $state ][ $key ] = $row->count;
					$states[ $state ][ 'total' ] += $states[ $state ][ $key ];
				}
			}
			$res = $dbr->select( 'transcode',
				array( 'COUNT(*) as count', 'transcode_key' ),
				array( 'transcode_key' => $allTranscodes ),
				__METHOD__,
				array( 'GROUP BY' => 'transcode_key' )
			);
			foreach( $res as $row ) {
				$key = $row->transcode_key;
				$states[ 'transcodes' ][ $key ] = $row->count;
				$states[ 'transcodes' ][ $key ] -= $states[ 'queued' ][ $key ];
				$states[ 'transcodes' ][ 'total' ] += $states[ 'transcodes' ][ $key ];
			}
			$wgMemc->add( $memcKey, $states, 60 );
		}
		return $states;
	}
}
