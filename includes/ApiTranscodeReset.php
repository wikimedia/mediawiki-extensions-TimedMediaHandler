<?php

namespace MediaWiki\TimedMediaHandler;

use ApiBase;
use ApiMain;
use File;
use ManualLogEntry;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use RepoGroup;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * Allows users with the 'transcode-reset' right to reset / re-run a transcode job.
 *
 * You can specify must specify a media asset title. You optionally can specify
 * a transcode key, to only reset a single transcode job for a particular media asset.
 * @ingroup API
 */
class ApiTranscodeReset extends ApiBase {
	/** @var RepoGroup */
	private $repoGroup;

	/**
	 * @param ApiMain $main
	 * @param string $action
	 * @param RepoGroup $repoGroup
	 */
	public function __construct(
		ApiMain $main,
		$action,
		RepoGroup $repoGroup
	) {
		parent::__construct( $main, $action );
		$this->repoGroup = $repoGroup;
	}

	public function execute() {
		// Check if transcoding is enabled on this wiki at all:
		if ( !$this->getConfig()->get( 'EnableTranscode' ) ) {
			$this->dieWithError( 'apierror-timedmedia-disabledtranscode', 'disabledtranscode' );
		}

		$params = $this->extractRequestParams();
		$titleObj = Title::newFromText( $params['title'] );

		// Make sure we have a valid Title
		if ( !$titleObj || $titleObj->isExternal() ) {
			$this->dieWithError( [ 'apierror-invalidtitle', wfEscapeWikiText( $params['title'] ) ] );
		}

		// Check that the user has permmission to reset transcodes on the file
		$this->checkTitleUserPermissions( $titleObj, 'transcode-reset' );

		// Make sure the title can be transcoded
		if ( !Hooks::isTranscodableTitle( $titleObj ) ) {
			$this->dieWithError(
				[
					'apierror-timedmedia-invalidtranscodetitle',
					wfEscapeWikiText( $titleObj->getPrefixedText() )
				],
				'invalidtranscodetitle'
			);
		}
		$transcodeKey = false;
		// Make sure it's an enabled transcode key we are trying to remove:
		// ( if you update your transcode keys the api is not how you purge the database of expired keys )
		if ( isset( $params['transcodekey'] ) ) {
			$transcodeSet = WebVideoTranscode::enabledTranscodes();
			if ( !in_array( $params['transcodekey'], $transcodeSet, true ) ) {
				$this->dieWithError(
					[ 'apierror-timedmedia-badtranscodekey', wfEscapeWikiText( $params['transcodekey'] ) ],
					'badtranscodekey'
				);
			} else {
				$transcodeKey = $params['transcodekey'];
			}
		}

		// Don't reset if less than 1 hour has passed and we have no error )
		$file = $this->repoGroup->findFile( $titleObj );
		$timeSinceLastReset = self::checkTimeSinceLastRest( $file, $transcodeKey );
		$waitTimeForTranscodeReset = $this->getConfig()->get( 'WaitTimeForTranscodeReset' );
		if ( $timeSinceLastReset < $waitTimeForTranscodeReset ) {
			$msg = $this->msg(
				'apierror-timedmedia-notenoughtimereset',
				TimedMediaHandler::getTimePassedMsg( $waitTimeForTranscodeReset - $timeSinceLastReset )
			);
			$this->dieWithError( $msg, 'notenoughtimereset' );
		}

		// All good do the transcode removal:
		WebVideoTranscode::removeTranscodes( $file, $transcodeKey );

		// Oh and we wanted to reset it, right? Trigger again.
		$manualOverride = true;
		WebVideoTranscode::updateJobQueue( $file, $transcodeKey, $manualOverride );

		$logEntry = new ManualLogEntry( 'timedmediahandler', 'resettranscode' );
		$logEntry->setPerformer( $this->getUser() );
		$logEntry->setTarget( $titleObj );
		$logEntry->setParameters( [
			'4::transcodekey' => $transcodeKey,
		] );
		$logEntry->insert();

		$this->getResult()->addValue( null, 'success', 'removed transcode' );
	}

	/**
	 * @param File $file
	 * @param string|false $transcodeKey
	 * @return int|string
	 */
	public static function checkTimeSinceLastRest( $file, $transcodeKey ) {
		global $wgWaitTimeForTranscodeReset;
		$transcodeStates = WebVideoTranscode::getTranscodeState( $file );
		if ( $transcodeKey ) {
			if ( !$transcodeStates[$transcodeKey] ) {
				// transcode key not found
				return $wgWaitTimeForTranscodeReset + 1;
			}
			return self::getStateResetTime( $transcodeStates[$transcodeKey] );
		}
		// least wait is set to reset time:
		$leastWait = $wgWaitTimeForTranscodeReset + 1;
		// else check for lowest reset time
		foreach ( $transcodeStates as $state ) {
			$ctime = self::getStateResetTime( $state );
			if ( $ctime < $leastWait ) {
				$leastWait = $ctime;
			}
		}
		return $leastWait;
	}

	/**
	 * @param array $state
	 * @return int|string
	 */
	public static function getStateResetTime( $state ) {
		global $wgWaitTimeForTranscodeReset;
		$db = wfGetDB( DB_REPLICA );
		// if an error return waitTime +1
		if ( $state['time_error'] !== null ) {
			return $wgWaitTimeForTranscodeReset + 1;
		}
		// return wait time from most recent event
		foreach ( [ 'time_success', 'time_startwork', 'time_addjob' ] as $timeField ) {
			if ( ( $state[ $timeField ] ) !== null ) {
				return (int)$db->timestamp() - (int)$db->timestamp( $state[ $timeField ] );
			}
		}
		// No time info, return resetWaitTime
		return $wgWaitTimeForTranscodeReset + 1;
	}

	public function mustBePosted() {
		return true;
	}

	public function isWriteMode() {
		return true;
	}

	/** @inheritDoc */
	protected function getAllowedParams() {
		return [
			'title' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true
			],
			'transcodekey' => null,
			'token' => null,
		];
	}

	public function needsToken() {
		return 'csrf';
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 * @return array
	 */
	protected function getExamplesMessages() {
		return [
			'action=transcodereset&title=File:Clip.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-1',
			'action=transcodereset&title=File:Clip.webm&transcodekey=360_560kbs.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-2',
		];
	}
}
