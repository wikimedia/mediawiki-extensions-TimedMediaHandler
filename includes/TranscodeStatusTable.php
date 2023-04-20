<?php

namespace MediaWiki\TimedMediaHandler;

use File;
use Html;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use User;
use Xml;

/**
 * TranscodeStatusTable outputs a "transcode" status table to the ImagePage
 *
 * If logged in as autoconfirmed users can reset transcode states
 * via the transcode api entry point
 *
 */
class TranscodeStatusTable {
	/**
	 * @param File $file
	 * @param IContextSource $context
	 * @return string
	 */
	public static function getHTML( $file, IContextSource $context ) {
		// Add transcode table css and javascript:
		$context->getOutput()->addModules( [ 'ext.tmh.transcodetable' ] );

		$o = '<h2 id="transcodestatus">' . wfMessage( 'timedmedia-status-header' )->escaped() . '</h2>';
		// Give the user a purge page link
		$o .= MediaWikiServices::getInstance()->getLinkRenderer()->makeLink(
			$file->getTitle(),
			$context->msg( 'timedmedia-update-status' )->text(),
			[],
			[ 'action' => 'purge' ]
		);

		$o .= self::getTranscodesTable( $file, $context->getUser() );

		return $o;
	}

	/**
	 * Get the video or audio codec for the defined transcode,
	 * for grouping/sorting purposes.
	 * @param string $key
	 * @return string
	 */
	public static function codecFromTranscodeKey( $key ) {
		if ( isset( WebVideoTranscode::$derivativeSettings[$key] ) ) {
			$settings = WebVideoTranscode::$derivativeSettings[$key];
			if ( isset( $settings['videoCodec'] ) ) {
				return $settings['videoCodec'];
			}

			if ( isset( $settings['audioCodec'] ) ) {
				return $settings['audioCodec'];
			}
			// else
			// this this shouldn't happen...
			// fall through
		}
		// else
		// derivative type no longer defined or invalid def?
		// fall through
		return $key;
	}

	/**
	 * @param File $file
	 * @param User $user
	 * @return string
	 */
	public static function getTranscodesTable( $file, User $user ) {
		$transcodeRows = WebVideoTranscode::getTranscodeState( $file );

		if ( empty( $transcodeRows ) ) {
			return '<p>' . wfMessage( 'timedmedia-no-derivatives' )->escaped() . '</p>';
		}

		uksort( $transcodeRows, static function ( $a, $b ) {
			$formatOrder = [ 'vp9', 'vp8', 'h264', 'theora', 'opus', 'vorbis', 'aac' ];

			$aFormat = self::codecFromTranscodeKey( $a );
			$bFormat = self::codecFromTranscodeKey( $b );
			$aIndex = array_search( $aFormat, $formatOrder );
			$bIndex = array_search( $bFormat, $formatOrder );

			if ( $aIndex === false && $bIndex === false ) {
				return -strnatcmp( $a, $b );
			}
			if ( $aIndex === false ) {
				return 1;
			}
			if ( $bIndex === false ) {
				return -1;
			}
			if ( $aIndex === $bIndex ) {
				return -strnatcmp( $a, $b );
			}
			return ( $aIndex - $bIndex );
		} );

		$o = Xml::openElement( 'table',
			[ 'class' => 'wikitable mw-filepage-transcodestatus' ]
		) . "\n"
			. '<tr>'
			. '<th>' . wfMessage( 'timedmedia-transcodeinfo' )->escaped() . '</th>'
			. '<th>' . wfMessage( 'timedmedia-transcodebitrate' )->escaped() . '</th>'
			. '<th>' . wfMessage( 'timedmedia-direct-link' )->escaped() . '</th>';

		if ( $user->isAllowed( 'transcode-reset' ) ) {
			$o .= '<th>' . wfMessage( 'timedmedia-actions' )->escaped() . '</th>';
		}

		$o .= '<th>' . wfMessage( 'timedmedia-status' )->escaped() . '</th>';
		$o .= '<th>' . wfMessage( 'timedmedia-transcodeduration' )->escaped() . '</th>';
		$o .= "</tr>\n";

		foreach ( $transcodeRows as $transcodeKey => $state ) {
			$o .= '<tr>';
			// Encode info:
			$o .= '<td>' . wfMessage( 'timedmedia-derivative-' . $transcodeKey )->escaped() . '</td>';
			$o .= '<td>' . self::getTranscodeBitrate( $file, $state ) . '</td>';

			// Download file
			$o .= '<td>';
			$o .= ( $state['time_success'] !== null ) ?
				'<a href="' . self::getSourceUrl( $file, $transcodeKey ) . '" title="' . wfMessage
				( 'timedmedia-download' )->escaped() . '"><div class="download-btn"><span>' .
				wfMessage( 'timedmedia-download' )->escaped() . '</span></div></a></td>' :
				wfMessage( 'timedmedia-not-ready' )->escaped();
			$o .= '</td>';

			// Check if we should include actions:
			if ( $user->isAllowed( 'transcode-reset' ) ) {
				// include reset transcode action buttons
				$o .= '<td class="mw-filepage-transcodereset"><a href="#" data-transcodekey="' .
					htmlspecialchars( $transcodeKey ) . '">' . wfMessage( 'timedmedia-reset' )->escaped() .
					'</a></td>';
			}

			// Status:
			$o .= '<td>' . self::getStatusMsg( $file, $state ) . '</td>';
			$o .= '<td>' . self::getTranscodeDuration( $file, $state ) . '</td>';

			$o .= '</tr>';
		}
		$o .= Xml::closeElement( 'table' );

		return $o;
	}

	/**
	 * @param File $file
	 * @param string $transcodeKey
	 * @return string
	 */
	public static function getSourceUrl( $file, $transcodeKey ) {
		return WebVideoTranscode::getTranscodedUrlForFile( $file, $transcodeKey );
	}

	/**
	 * @param File $file
	 * @param array $state
	 * @return string
	 */
	public static function getTranscodeDuration( File $file, array $state ) {
		global $wgLang;
		if ( $state['time_success'] !== null ) {
			$startTime = (int)wfTimestamp( TS_UNIX, $state['time_startwork'] );
			$endTime = (int)wfTimestamp( TS_UNIX, $state['time_success'] );
			$delta = $endTime - $startTime;
			return $wgLang->formatTimePeriod( $delta );
		}
		return '';
	}

	/**
	 * @param File $file
	 * @param array $state
	 * @return string
	 */
	public static function getTranscodeBitrate( File $file, array $state ) {
		global $wgLang;
		if ( $state['time_success'] !== null ) {
			return $wgLang->formatBitrate( $state['final_bitrate'] );
		}
		return '';
	}

	/**
	 * @param File $file
	 * @param array $state
	 * @return string
	 */
	public static function getStatusMsg( $file, $state ) {
		// Check for success:
		if ( $state['time_success'] !== null ) {
			return wfMessage( 'timedmedia-completed-on' )
				->dateTimeParams( $state[ 'time_success' ] )->escaped();
		}
		// Check for error:
		if ( $state['time_error'] !== null ) {
			$attribs = [];
			if ( $state['error'] !== null ) {
				$attribs = [
					'class' => 'mw-tmh-pseudo-error-link',
					'data-error' => $state['error'],
				];
			}

			return Html::rawElement( 'span', $attribs,
				wfMessage( 'timedmedia-error-on' )
					->dateTimeParams( $state['time_error'] )->escaped()
			);
		}

		// Check for started encoding
		if ( $state['time_startwork'] !== null ) {
			$timePassed = time() - (int)wfTimestamp( TS_UNIX, $state['time_startwork'] );
			// Get the rough estimate of time done: ( this is not very costly considering everything else
			// that happens in an action=purge video page request )
			/*$filePath = WebVideoTranscode::getTargetEncodePath( $file, $state['key'] );
			if ( is_file( $filePath ) ) {
				$targetSize = WebVideoTranscode::getProjectedFileSize( $file, $state['key'] );
				if ( $targetSize === false ) {
					$doneMsg = wfMessage( 'timedmedia-unknown-target-size',
						$wgLang->formatSize( filesize( $filePath ) ) )->escaped();
				} else {
					$doneMsg = wfMessage('timedmedia-percent-done',
						round( filesize( $filePath ) / $targetSize, 2 ) )->escaped();
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
		if ( $state['time_addjob'] !== null ) {
			$timePassed = time() - (int)wfTimestamp( TS_UNIX, $state['time_addjob'] );
			return wfMessage(
				'timedmedia-in-job-queue',
				TimedMediaHandler::getTimePassedMsg( $timePassed )
			)->escaped();
		}
		// Return unknown status error:
		return wfMessage( 'timedmedia-status-unknown' )->escaped();
	}
}
