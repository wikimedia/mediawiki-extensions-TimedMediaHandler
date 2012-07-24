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
	public function __construct( $request = null, $par = null ) {
		parent::__construct( 'TimedMediaHandler' );

	}

	public function execute( $par ) {
		global $wgEnabledTranscodeSet;


		$this->setHeaders();
		$out = $this->getOutput();

		$out->addModuleStyles( 'mediawiki.special' );

		// only show if user has right permissions
		if( !$this->getUser()->isAllowed( 'transcode-status' ) ){
			return;
		}

		// fallback for non-JS
		$stats = $this->getStats();
		$out->addHTML( "<h2>Videos (" . $stats['videos']['total'] . ")</h2>" );
		$out->addHTML( "Ogg Theora/Vorbis {$stats['videos']['ogv']}<br>" );
		$out->addHTML( "WebM VP8/Vorbis {$stats['videos']['webm']}<br>" );

		$out->addHTML( "<h2>Transcodes (" . $stats['transcodes']['total'] . ")</h2>" );
		foreach($wgEnabledTranscodeSet as $profile ) {
			$out->addHTML( "$profile {$stats['transcodes'][$profile]}<br>" );
		}
		if ( $stats['failed']['total'] ) {
			$out->addHTML( "<h2>Failed Transcodes (" . $stats['failed']['total'] . ")</h2>" );
			foreach($wgEnabledTranscodeSet as $profile ) {
				if ( $stats['failed'][$profile] ) {
					$out->addHTML( "$profile {$stats['failed'][$profile]}<br>" );
				}
			}
			$out->addHTML( "<ul>" );
			foreach( $this->getFailedFiles() as $name ) {
				$title = Title::newFromText( $name, NS_FILE );
				$out->addHTML( "<li>" . Linker::link( $title, $name ) ."</li>" );
			}
			$out->addHTML( "</ul>" );
		}
		if ( $stats['jobs']['total'] ) {
			$out->addHTML( "<h2>Transcode jobs in queue (" . $stats['jobs']['total'] . ")</h2>" );
			foreach($wgEnabledTranscodeSet as $profile ) {
				if ( $stats['jobs'][$profile] ) {
					$out->addHTML( "$profile {$stats['jobs'][$profile]}<br>" );
				}
			}
			$out->addHTML( "<ul>" );
			foreach( $this->getQueuedTranscodes() as $job ) {
				$title = Title::newFromText( $job[ 'file' ], NS_FILE );
				$name = $job[ 'file' ] . ' ('. $job[ 'key' ] . ')';
				$out->addHTML( "<li>" . Linker::link( $title, $name ) ."</li>" );
			}
			$out->addHTML( "</ul>" );
		}
		if ( $stats['active']['total'] ) {
			$out->addHTML( "<h2>Currently encoding (" . $stats['active']['total'] . ")</h2>" );
			foreach($wgEnabledTranscodeSet as $profile ) {
				if ( $stats['actives'][$profile] ) {
					$out->addHTML( "$profile {$stats['actives'][$profile]}<br>" );
				}
			}
			$out->addHTML( "<ul>" );
			foreach( $this->getActiveTranscodes() as $job ) {
				$title = Title::newFromText( $job[ 'file' ], NS_FILE );
				$name = $job[ 'file' ] . ' ('. $job[ 'key' ] . ')';
				$out->addHTML( "<li>" . Linker::link( $title, $name ) ."</li>" );
			}
			$out->addHTML( "</ul>" );
		}
	}

	private function getFailedFiles() {
		$dbr = wfGetDB( DB_SLAVE );
		$files = array();
		$res = $dbr->select(
			'transcode',
			'transcode_image_name',
			'transcode_error != ""',
			__METHOD__,
			array( 'LIMIT' => 30, 'ORDER BY' => 'transcode_time_error DESC' )
		);
		foreach ( $res as $row ) {
			$files[] = $row->transcode_image_name;
		}
		return $files;
	}

	private function getActiveTranscodes() {
		$dbr = wfGetDB( DB_SLAVE );
		$files = array();
		$res = $dbr->select(
			'transcode',
			'*',
			'transcode_time_startwork IS NOT NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
			__METHOD__,
			array( 'LIMIT' => 30, 'ORDER BY' => 'transcode_time_error DESC' )
		);
		foreach ( $res as $row ) {
			$files[] = array(
				'file' => $row->transcode_image_name,
				'key' => $row->transcode_key
			);
		}
		return $files;
	}

	private function getQueuedTranscodes() {
		$dbr = wfGetDB( DB_SLAVE );
		$files = array();
		$res = $dbr->select(
			'transcode',
			'*',
			'transcode_time_startwork IS NULL AND transcode_time_error IS NULL',
			__METHOD__,
			array( 'LIMIT' => 30, 'ORDER BY' => 'transcode_time_addjob DESC' )
		);
		foreach ( $res as $row ) {
			$files[] = array(
				'file' => $row->transcode_image_name,
				'key' => $row->transcode_key
			);
		}
		return $files;
	}
	private function getStats() {
		global $wgEnabledTranscodeSet;
		$stats = array();
		$dbr = wfGetDB( DB_SLAVE );
		$stats[ 'jobs' ] = array( 'total'=>0 );
		$stats[ 'transcodes' ] = array( 'total'=>0 );
		$stats[ 'failed' ] = array( 'total'=>0 );
		foreach($wgEnabledTranscodeSet as $profile ) {
			#jobs
			$stats['jobs'][$profile] = (int)$dbr->selectField(
				'transcode',
				'COUNT(*)',
				'transcode_key = "'.$profile.'" AND transcode_time_startwork IS NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
				__METHOD__
			);
			$stats['jobs']['total'] += $stats['jobs'][$profile];
			#failed transcodes
			$stats['failed'][$profile] = (int)$dbr->selectField(
				'transcode',
				'COUNT(*)',
				'transcode_key = "'.$profile.'" AND transcode_error != ""',
				__METHOD__
			);
			$stats['failed']['total'] += $stats['failed'][$profile];
			#active transcodes
			$stats['active'][$profile] = (int)$dbr->selectField(
				'transcode',
				'COUNT(*)',
				'transcode_key = "'.$profile.'" AND transcode_time_startwork IS NOT NULL AND transcode_time_success IS NULL AND transcode_time_error IS NULL',
				__METHOD__
			);
			$stats['active']['total'] += $stats['active'][$profile];
			#all transcodes
			$stats['transcodes'][$profile] = (int)$dbr->selectField(
				'transcode',
				'COUNT(*)',
				array('transcode_key' => $profile ),
				__METHOD__
			);
			$stats['transcodes']['total'] += $stats['transcodes'][$profile];
		}
		$stats[ 'videos' ] = array( 'total'=>0 );
		$stats['videos']['ogv'] = (int)$dbr->selectField(
			'image',
			'COUNT(*)',
			'img_media_type = "VIDEO" AND (img_name LIKE "%.ogv" OR img_name LIKE "%.ogg")',
			__METHOD__
		);
		$stats['videos']['webm'] = (int)$dbr->selectField(
			'image',
			'COUNT(*)',
			'img_media_type = "VIDEO" AND img_name LIKE "%.webm"',
			__METHOD__
		);
		$stats['videos']['total'] = $stats['videos']['ogv'] + $stats['videos']['webm'];
		return $stats;
	}
}
