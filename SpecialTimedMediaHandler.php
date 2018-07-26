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
	private $transcodeStates = [
		// phpcs:ignore Generic.Files.LineLength.TooLong
		'active' => 'transcode_time_startwork IS NOT NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
		'failed' => 'transcode_time_startwork IS NOT NULL AND transcode_time_error IS NOT NULL',
		'queued' => 'transcode_time_addjob IS NOT NULL AND transcode_time_startwork IS NULL',
		'missing' => 'transcode_time_addjob IS NULL',
	];
	private $formats = [
		'ogg' => 'img_major_mime="application" AND img_minor_mime = "ogg"',
		'webm' => 'img_major_mime="video" AND img_minor_mime = "webm"',
	];
	private $audioFormats = [
		'ogg' => 'img_major_mime="application" AND img_minor_mime = "ogg"',
		'webm' => 'img_major_mime="audio" AND img_minor_mime = "webm"',
		'flac' => 'img_major_mime="audio" AND img_minor_mime="x-flac"',
		'wav' => 'img_major_mime="audio" AND img_minor_mime="wav"',
	];

	public function __construct( $request = null, $par = null ) {
		parent::__construct( 'TimedMediaHandler', 'transcode-status' );
	}

	public function execute( $par ) {
		$this->setHeaders();
		$out = $this->getOutput();

		$out->addModuleStyles( 'mediawiki.special' );

		$stats = $this->getStats();

		foreach ( [ 'audios', 'videos' ] as $type ) {
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
				if ( isset( $states[ $state ] )
					&& isset( $states[ $state ][ $key ] )
					&& $states[ $state ][ $key ] ) {
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

	private function getTranscodes( $state, $limit = 50 ) {
		global $wgMemc;
		$memcKey = wfMemcKey( 'TimedMediaHandler', 'files', $state );
		$files = $wgMemc->get( $memcKey );
		if ( !$files ) {
			$dbr = wfGetDB( DB_REPLICA );
			$files = [];
			$res = $dbr->select(
				'transcode',
				[ 'transcode_image_name', 'transcode_key' ],
				$this->transcodeStates[ $state ],
				__METHOD__,
				[ 'LIMIT' => $limit, 'ORDER BY' => 'transcode_time_error DESC' ]
			);
			foreach ( $res as $row ) {
				$transcode = [];
				foreach ( $row as $k => $v ) {
					$transcode[ str_replace( 'transcode_', '', $k ) ] = $v;
				}
				$files[] = $transcode;
			}
			$wgMemc->add( $memcKey, $files, 60 );
		}
		return $files;
	}

	private function getTranscodesTable( $state, $limit = 50 ) {
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
				. '<td>' . Linker::link( $title, $transcode[ 'image_name' ] ) . '</td>'
				. '</tr>'
				. "\n";
		}
		$table .= '</table>';
		return $table;
	}

	private function getStats() {
		global $wgMemc;
		$allTranscodes = WebVideoTranscode::enabledTranscodes();

		$memcKey = wfMemcKey( 'TimedMediaHandler', 'stats', '1' /* version */ );
		$stats = $wgMemc->get( $memcKey );
		if ( !$stats ) {
			$dbr = wfGetDB( DB_REPLICA );
			$stats = [];
			$stats[ 'videos' ] = [ 'total' => 0 ];
			foreach ( $this->formats as $format => $condition ) {
				$stats[ 'videos' ][ $format ] = (int)$dbr->selectField(
					'image',
					'COUNT(*)',
					'img_media_type = "VIDEO" AND (' . $condition . ')',
					__METHOD__
				);
				$stats[ 'videos' ][ 'total' ] += $stats[ 'videos' ][ $format ];
			}
			$stats[ 'audios' ] = [ 'total' => 0 ];
			foreach ( $this->audioFormats as $format => $condition ) {
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
		global $wgMemc;
		$allTranscodes = WebVideoTranscode::enabledTranscodes();

		$memcKey = wfMemcKey( 'TimedMediaHandler', 'states' );
		$states = $wgMemc->get( $memcKey );
		if ( !$states ) {
			$dbr = wfGetDB( DB_REPLICA );
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
				$res = $dbr->select( 'transcode',
					[ 'COUNT(*) as count', 'transcode_key' ],
					$cond,
					__METHOD__,
					[ 'GROUP BY' => 'transcode_key' ]
				);
				foreach ( $res as $row ) {
					$key = $row->transcode_key;
					$states[ $state ][ $key ] = $row->count;
					$states[ $state ][ 'total' ] += $states[ $state ][ $key ];
				}
			}
			$res = $dbr->select( 'transcode',
				[ 'COUNT(*) as count', 'transcode_key' ],
				[ 'transcode_key' => $allTranscodes ],
				__METHOD__,
				[ 'GROUP BY' => 'transcode_key' ]
			);
			foreach ( $res as $row ) {
				$key = $row->transcode_key;
				$states[ 'transcodes' ][ $key ] = $row->count;
				$states[ 'transcodes' ][ $key ] -= $states[ 'queued' ][ $key ];
				$states[ 'transcodes' ][ $key ] -= $states[ 'missing' ][ $key ];
				$states[ 'transcodes' ][ $key ] -= $states[ 'active' ][ $key ];
				$states[ 'transcodes' ][ $key ] -= $states[ 'failed' ][ $key ];
				$states[ 'transcodes' ][ 'total' ] += $states[ 'transcodes' ][ $key ];
			}
			$wgMemc->add( $memcKey, $states, 60 );
		}
		return $states;
	}

	protected function getGroupName() {
		return 'media';
	}
}
