<?php
/**
 * TranscodeStatusTable outputs a "transcode" status table to the ImagePage
 *
 * If logged in as autoconfirmed users can reset transcode states
 * via the transcode api entry point
 *
 */
class TranscodeStatusTable {
	/**
	 * @param $file File
	 * @return string
	 */
	public static function getHTML( $file ){
		global $wgUser, $wgOut;

		// Add transcode table css and javascript:
		$wgOut->addModules( array( 'ext.tmh.transcodetable' ) );

		$o = '<h2 id="transcodestatus">' . wfMessage( 'timedmedia-status-header' )->escaped() . '</h2>';
		// Give the user a purge page link
		$o.= Linker::link(
			$file->getTitle(),
			wfMessage('timedmedia-update-status')->escaped(),
			array(),
			array( 'action'=> 'purge' )
		);

		$o.= Xml::openElement( 'table', array( 'class' => 'wikitable mw-filepage-transcodestatus' ) ) . "\n"
			. '<tr>'
			. '<th>' . wfMessage( 'timedmedia-status' )->escaped() . '</th>'
			. '<th>' . wfMessage( 'timedmedia-transcodeinfo' )->escaped() . '</th>'
			. '<th>' . wfMessage( 'timedmedia-direct-link' )->escaped() . '</th>';

		if( $wgUser->isAllowed( 'transcode-reset' ) ) {
			$o.= '<th>' . wfMessage( 'timedmedia-actions' )->escaped() . '</th>';
		}

		$o.= "</tr>\n";

		$o.= self::getTranscodeRows( $file );

		$o.=  Xml::closeElement( 'table' );
		return $o;
	}

	/**
	 * @param $file File
	 * @return string
	 */
	public static function getTranscodeRows( $file ){
		global $wgUser;
		$o='';

		$transcodeRows = WebVideoTranscode::getTranscodeState( $file );

		foreach( $transcodeRows as $transcodeKey => $state ){
			$o.='<tr>';
			// Status:
			$o.='<td>' . self::getStatusMsg( $file, $state ) . '</td>';

			// Encode info:
			$o.='<td>' . wfMessage('timedmedia-derivative-desc-' . $transcodeKey )->escaped() . '</td>';

			// Download file
			$o.='<td>';
			$o.= ( !is_null( $state['time_success'] ) ) ?
				'<a href="'.self::getSourceUrl( $file, $transcodeKey ) .'" title="'.wfMessage
				('timedmedia-download' )->escaped() .'"><div class="download-btn"></div></a></td>' :
				wfMessage('timedmedia-not-ready' )->escaped();
			$o.='</td>';

			// Check if we should include actions:
			if( $wgUser->isAllowed( 'transcode-reset' ) ){
				// include reset transcode action buttons
				$o.='<td class="mw-filepage-transcodereset"><a href="#" data-transcodekey="' .
					htmlspecialchars( $transcodeKey ). '">' . wfMessage('timedmedia-reset')->escaped() .
					'</a></td>';
			}
			$o.='</tr>';
		}
		return $o;
	}

	/**
	 * @param $file File
	 * @param $transcodeKey string
	 * @return string
	 */
	public static function getSourceUrl( $file, $transcodeKey ){
		return WebVideoTranscode::getTranscodedUrlForFile( $file, $transcodeKey );
	}

	/**
	 * @param $file File
	 * @param $state
	 * @return string
	 */
	public static function getStatusMsg( $file, $state ){
		global $wgContLang;
		// Check for success:
		if( !is_null( $state['time_success'] ) ) {
			return wfMessage( 'timedmedia-completed-on',
				$wgContLang->timeAndDate( $state[ 'time_success' ] ) )->escaped();
		}
		// Check for error:
		if( !is_null( $state['time_error'] ) ){
			$attribs = array();
			if( !is_null( $state['error'] ) ){
				$attribs = array(
					'class' => 'mw-tmh-pseudo-error-link',
					'data-error' => $state['error'],
				);
			}

			return Html::rawElement( 'span', $attribs,
				wfMessage( 'timedmedia-error-on',
					$wgContLang->timeAndDate( $state['time_error'] ) )->escaped()
			);
		}

		//$db = wfGetDB( DB_SLAVE );
		// Check for started encoding
		if( !is_null( $state['time_startwork'] ) ){
			$timePassed = time() - wfTimestamp ( TS_UNIX, $state['time_startwork'] );
			// Get the rough estimate of time done: ( this is not very costly considering everything else
			// that happens in an action=purge video page request )
			/*$filePath = WebVideoTranscode::getTargetEncodePath( $file, $state['key'] );
			if( is_file( $filePath ) ){
				$targetSize = WebVideoTranscode::getProjectedFileSize( $file, $state['key'] );
				if( $targetSize === false ){
					$doneMsg = wfMessage( 'timedmedia-unknown-target-size', $wgLang->formatSize( filesize( $filePath ) ) )->escaped();
				} else {
					$doneMsg = wfMessage('timedmedia-percent-done', round( filesize( $filePath ) / $targetSize, 2 ) )->escaped();
				}
			}	*/
			// Predicting percent done is not working well right now ( disabled for now )
			$doneMsg = '';
			return wfMessage(
				'timedmedia-started-transcode',
				TimedMediaHandler::getTimePassedMsg( $timePassed ), $doneMsg
			)->escaped();
		}
		// Check for job added ( but not started encoding )
		if( !is_null( $state['time_addjob'] ) ){
			$timePassed = time() - wfTimestamp ( TS_UNIX, $state['time_addjob'] );
			return wfMessage(
				'timedmedia-in-job-queue',
				TimedMediaHandler::getTimePassedMsg( $timePassed )
			)->escaped();
		}
		// Return unknown status error:
		return wfMessage('timedmedia-status-unknown')->escaped();
	}
}
