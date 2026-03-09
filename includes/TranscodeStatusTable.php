<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Context\IContextSource;
use MediaWiki\FileRepo\File\File;
use MediaWiki\Html\Html;
use MediaWiki\Html\TemplateParser;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Utils\MWTimestamp;
use Wikimedia\Timestamp\TimestampFormat;

/**
 * TranscodeStatusTable outputs a "transcode" status table to the ImagePage
 *
 * If logged in as autoconfirmed users can reset transcode states
 * via the transcode api entry point
 */
class TranscodeStatusTable {
	private readonly TemplateParser $templateParser;

	public function __construct(
		private readonly IContextSource $context,
		private readonly LinkRenderer $linkRenderer,
	) {
		$this->templateParser = new TemplateParser( __DIR__ . '/../templates' );
	}

	public function getHTML( File $file ): string {
		// Add transcode table css and javascript:
		$this->context->getOutput()->addModules( [ 'ext.tmh.transcodetable' ] );

		$o = '<h2 id="transcodestatus">' . wfMessage( 'timedmedia-status-header' )->escaped() . '</h2>';
		// Give the user a purge page link
		$o .= $this->linkRenderer->makeLink(
			$file->getTitle(),
			$this->context->msg( 'timedmedia-update-status' )->text(),
			[],
			[ 'action' => 'purge' ]
		);

		$o .= $this->getTranscodesTable( $file );

		return $o;
	}

	/**
	 * Get the video or audio codec for the defined transcode,
	 * for grouping/sorting purposes.
	 */
	public static function codecFromTranscodeKey( string $key ): string {
		$transcodePresets = MediaWikiServices::getInstance()->getService( 'TimedMediaHandler.TranscodePresets' );
		$settings = $transcodePresets->findByKey( $key );
		if ( $settings ) {
			if ( $settings->videoCodec ) {
				return $settings->videoCodec;
			}

			if ( $settings->audioCodec ) {
				return $settings->audioCodec;
			}
			// else
			// this shouldn't happen...
			// fall through
		}
		// else
		// derivative type no longer defined or invalid def?
		// fall through
		return $key;
	}

	public function getTranscodesTable( File $file ): string {
		$transcodeRows = WebVideoTranscode::getTranscodeState( $file );

		if ( !$transcodeRows ) {
			return '<p>' . wfMessage( 'timedmedia-no-derivatives' )->escaped() . '</p>';
		}

		uksort( $transcodeRows, static function ( $a, $b ) {
			$formatOrder = [ 'mpd', 'vp9', 'vp8', 'h264', 'theora', 'mpeg4', 'mjpeg', 'opus', 'mp3', 'vorbis', 'aac' ];

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
			return $aIndex - $bIndex;
		} );

		return $this->templateParser->processTemplate(
			'TranscodeStatusTable',
			$this->transcodeRowsToTemplateParams( $transcodeRows, $file )
		);
	}

	private function transcodeRowsToTemplateParams( array $transcodeRows, File $file ): array {
		$transcodeRowsForTemplate = [];
		foreach ( $transcodeRows as $transcodeKey => $state ) {
			$transcodeRowsForTemplate[] = [
				'transcodeKey' => $transcodeKey,
				'msg-derivative-key' => wfMessage( 'timedmedia-derivative-' . $transcodeKey ),
				'bitrate' => $this->getTranscodeBitrate( $file, $state ),
				'transcode-success' => (int)( $state['state'] ?? -1 ) === WebVideoTranscode::STATE_SUCCESS,
				'transcode-downloadable' => (int)( $state['state'] ?? -1 ) === WebVideoTranscode::STATE_SUCCESS &&
					$transcodeKey !== 'mpd',
				'msg-timedmedia-download' => wfMessage( 'timedmedia-download' ),
				// Download file
				//
				// Note the <a download> attribute only is applied on same-origin URLs.
				// The "?download" query string append will work on servers configured
				// the way the Wikimedia production servers are, but other sites that
				// store files offsite may not have the same setup.
				//
				// On failure, these should devolve to either downloading or loading a
				// media file inline, depending on the format and browser and server
				// config.
				'downloadUrl' => wfAppendQuery( self::getSourceUrl( $file, $transcodeKey ), 'download' ),
				'msg-timedmedia-reset' => wfMessage( 'timedmedia-reset' ),
				'html-transcode-status' => self::getStatusMsg( $file, $state ),
				'transcode-duration' => $this->getTranscodeDuration( $file, $state ),
			];
		}

		return [
			'msg-timedmedia-transcodeinfo' => wfMessage( 'timedmedia-transcodeinfo' ),
			'msg-timedmedia-transcodebitrate' => wfMessage( 'timedmedia-transcodebitrate' ),
			'msg-timedmedia-not-ready' => wfMessage( 'timedmedia-not-ready' ),
			'msg-timedmedia-direct-link' => wfMessage( 'timedmedia-direct-link' ),
			'msg-timedmedia-actions' => wfMessage( 'timedmedia-actions' ),
			'msg-timedmedia-status' => wfMessage( 'timedmedia-status' ),
			'msg-timedmedia-transcodeduration' => wfMessage( 'timedmedia-transcodeduration' ),
			'has-reset' => $this->context->getUser()->isAllowed( 'transcode-reset' ),
			'transcodeRows' => $transcodeRowsForTemplate,
		];
	}

	public static function getSourceUrl( File $file, string $transcodeKey ): string {
		return WebVideoTranscode::getTranscodedUrlForFile( $file, $transcodeKey );
	}

	public function getTranscodeDuration( File $file, array $state ): string {
		if ( (int)( $state['state'] ?? -1 ) === WebVideoTranscode::STATE_SUCCESS ) {
			$startTime = (int)wfTimestamp( TimestampFormat::UNIX, $state['time_startwork'] );
			$endTime = (int)wfTimestamp( TimestampFormat::UNIX, $state['time_success'] );
			$delta = $endTime - $startTime;
			return $this->context->getLanguage()->formatTimePeriod( $delta );
		}
		return '';
	}

	public function getTranscodeBitrate( File $file, array $state ): string {
		if ( (int)( $state['state'] ?? -1 ) === WebVideoTranscode::STATE_SUCCESS ) {
			if ( $state['final_bitrate'] == WebVideoTranscode::VARIABLE_BITRATE ) {
				return wfMessage( 'timedmedia-variable-bitrate' )->escaped();
			}
			return $this->context->getLanguage()->formatBitrate( $state['final_bitrate'] );
		}
		return '';
	}

	public static function getStatusMsg( File $file, array $state ): string {
		$transcodeState = (int)( $state['state'] ?? -1 );
		// Check for success:
		if ( $transcodeState === WebVideoTranscode::STATE_SUCCESS ) {
			return wfMessage( 'timedmedia-completed-on' )
				->dateTimeParams( $state[ 'touched' ] )->escaped();
		}
		// Check for error:
		if ( $transcodeState === WebVideoTranscode::STATE_FAILED ) {
			$attribs = [];
			if ( $state['error'] !== null ) {
				$attribs = [
					'class' => 'mw-tmh-pseudo-error-link',
					'data-error' => $state['error'],
				];
			}

			return Html::rawElement( 'span', $attribs,
				wfMessage( 'timedmedia-error-on' )
					->dateTimeParams( $state['touched'] )->escaped()
			);
		}

		// Check for started encoding
		if ( $transcodeState === WebVideoTranscode::STATE_ACTIVE ) {
			// Predicting percent done is not working well right now (disabled for now)
			$doneMsg = '';
			return wfMessage(
				'timedmedia-started-transcode',
				( new MWTimestamp( $state['touched'] ) )->getRelativeTimestamp(), $doneMsg
			)->escaped();
		}
		// Check for job added (but not started encoding)
		if ( $transcodeState === WebVideoTranscode::STATE_QUEUED ) {
			return wfMessage(
				'timedmedia-in-job-queue',
				( new MWTimestamp( $state['touched'] ) )->getRelativeTimestamp()
			)->escaped();
		}
		// Return unknown status error:
		return wfMessage( 'timedmedia-status-unknown' )->escaped();
	}
}
