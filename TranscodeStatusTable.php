<?php 
/** 
 * TranscodeStatusTable outputs a "transcode" status table to the ImagePage
 * 
 * If logged in as autoconfirmed users can reset transcode states
 * via the transcode api entry point
 * 
 */
class TranscodeStatusTable {
	
	public static function getHTML( $file ){
		global $wgUser, $wgOut;
		
		// Add transcode table css and javascript:  
		$wgOut->addModules( array( 'ext.tmh.transcodetable' ) );
		
		$o = '<h2>' . wfMsgHtml( 'timedmedia-status-header' ) . '</h2>';
		// Give the user a purge page link
		$o.= Linker::link( $file->getTitle(), wfMsg('timedmedia-update-status'), array(), array( 'action'=> 'purge' ) );
		
		$o.= Xml::openElement( 'table', array( 'class' => 'wikitable transcodestatus' ) ) . "\n"
			. '<tr>'
			. '<th>'.wfMsgHtml( 'timedmedia-status' ) .'</th>'			
			. '<th>' . wfMsgHtml( 'timedmedia-transcodeinfo' ) . '</th>'
			. '<th>'.wfMsgHtml( 'timedmedia-direct-link' ) .'</th>';
			
		if( $wgUser->isAllowed( 'transcode-reset' ) ){
			$o.= '<th>' . wfMsgHtml( 'timedmedia-actions' ) . '</th>';
		}
			
		$o.= "</tr>\n";
			
		$o.= self::getTranscodeRows( $file );
		
		$o.=  Xml::closeElement( 'table' );
		return $o;
	}
	
	public static function getTranscodeRows( $file ){
		global $wgUser;
		$o='';
		$transcodeRows = WebVideoTranscode::getTranscodeState( $file->getTitle()->getDbKey() );
		
		foreach( $transcodeRows as $transcodeKey => $state ){
			$o.='<tr>';
			// Status: 
			$o.='<td>' . self::getStatusMsg( $file, $state ) . '</td>';						
			
			// Encode info:
			$o.='<td>' . wfMsgHtml('timedmedia-derivative-desc-' . $transcodeKey ) . '</td>';

			// Download file
			$o.='<td>';
			$o.= ( !is_null( $state['time_success'] ) ) ? 				
				'<a href="'.self::getSourceUrl( $file, $transcodeKey ) .'" title="'.wfMsg('timedmedia-download' ) .'"><div class="download-btn"></div></a></td>' :
				wfMsgHtml('timedmedia-not-ready' );
			$o.='</td>';
			
			// Check if we should include actions: 
			if( $wgUser->isAllowed( 'transcode-reset' ) ){
				// include reset transcode action buttons
				$o.='<td class="transcodereset"><a href="#" data-transcodekey="' . htmlspecialchars( $transcodeKey ). '">' . wfMsg('timedmedia-reset') . '</a></td>';
			}
			$o.='</tr>';
		}
		return $o;
	}
	public static function getSourceUrl( $file, $transcodeKey ){
		$fileName = $file->getTitle()->getDbKey();
		
		$thumbName = $file->thumbName( array() );
		$thumbUrl = $file->getThumbUrl( $thumbName );
		$thumbUrlDir = dirname( $thumbUrl );
		return $thumbUrlDir . '/' .$file->getName() . '.' . $transcodeKey;
	}
	
	public static function getStatusMsg( $file, $state ){
		// Check for success:
		if( !is_null( $state['time_success'] ) ) {
			return wfMsgHtml('timedmedia-completed-on', $state['time_success'] );
		}
		// Check for error: 
		if( !is_null( $state['time_error'] ) ){
			if( !is_null( $state['error'] ) ){
				$showErrorLink = Linker::link( $file->getTitle(), wfMsg('timedmedia-show-error'), array(
					'title' => wfMsgHtml('timedmedia-error-on', $state['time_error'] ),
					'class' => 'errorlink',
					'data-error' => $state['error']
				));
			}
			return wfMsgHtml('timedmedia-error-on', $state['time_error'] ) . $showErrorLink;
		}		
		$db = wfGetDB( DB_SLAVE );
		// Check for started encoding
		if( !is_null( $state['time_startwork'] ) ){
			$timePassed = wfTimestampNow() - $db->timestamp( $state['time_startwork'] );

			// Get the rough estimate of time done: ( this is not very costly considering everything else
			// that happens in an action=purge video page request ) 
			/*$filePath = WebVideoTranscode::getTargetEncodePath( $file, $state['key'] );
			if( is_file( $filePath ) ){
				$targetSize = WebVideoTranscode::getProjectedFileSize( $file, $state['key'] );
				if( $targetSize === false ){
					$doneMsg = wfMsgHtml('timedmedia-unknown-target-size', $wgLang->formatSize( filesize( $filePath ) ) );
				} else {
					$doneMsg = wfMsgHtml('timedmedia-percent-done', round( filesize( $filePath ) / $targetSize, 2 ) );
				}
			}	*/
			// predicting percent done is not working well right now ( disabled for now )
			$doneMsg = '';
			return wfMsgHtml('timedmedia-started-transcode', self::getTimePassedMsg( $timePassed ), $doneMsg );
		}
		// Check for job added ( but not started encoding )
		if( !is_null( $state['time_addjob'] ) ){
			$timePassed =  wfTimestampNow() - $db->timestamp( $state['time_addjob'] ) ;
			return wfMsgHtml('timedmedia-in-job-queue', self::getTimePassedMsg( $timePassed ) );
		}
		// Return unknown status error:
		return wfMsgHtml('timedmedia-status-unknown');
	}
	public static function getTimePassedMsg( $timePassed ){		
		$t['days'] = floor($timePassed/60/60/24);
		$t['hours'] = floor($timePassed/60/60)%24;
		$t['minutes'] = floor($timePassed/60)%60;
		$t['seconds'] = $timePassed%60;			
		
		foreach( $t as $k => $v ){
			if($v == 0 ){
				unset( $t[$k] );
			}else{
				$t[$k] = wfMsg( 'timedmedia-' . $k, $v);
			}
		}
		if( count( $t ) == 0 ){
			$t = array( wfMsg( 'timedmedia-seconds', 0) ) ;
		}
		// Call to the correct set of significant measurements:
		return wfMsgHtml( 'timedmedia-time-' . count($t) . '-measurements', $t);
	}
}