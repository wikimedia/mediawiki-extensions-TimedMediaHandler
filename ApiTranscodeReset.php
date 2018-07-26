<?php

/**
 * Allows users with the 'transcode-reset' right to reset / re-run a transcode job.
 *
 * You can specify must specify a media asset title. You optionally can specify
 * a transcode key, to only reset a single transcode job for a particular media asset.
 * @ingroup API
 */
class ApiTranscodeReset extends ApiBase {
	public function execute() {
		global $wgUser, $wgEnableTranscode, $wgWaitTimeForTranscodeReset;
		// Check if transcoding is enabled on this wiki at all:
		if ( !$wgEnableTranscode ) {
			if ( is_callable( [ $this, 'dieWithError' ] ) ) {
				$this->dieWithError( 'apierror-timedmedia-disabledtranscode', 'disabledtranscode' );
			} else {
				$this->dieUsage( 'Transcode is disabled on this wiki', 'disabledtranscode' );
			}
		}

		// Confirm the user has the transcode-reset right
		if ( is_callable( [ $this, 'checkUserRightsAny' ] ) ) {
			$this->checkUserRightsAny( 'transcode-reset' );
		} else {
			if ( !$wgUser->isAllowed( 'transcode-reset' ) ) {
				$this->dieUsage( 'You don\'t have permission to reset transcodes', 'missingpermission' );
			}
		}
		$params = $this->extractRequestParams();

		// Make sure we have a valid Title
		$titleObj = Title::newFromText( $params['title'] );
		if ( !$titleObj || $titleObj->isExternal() ) {
			if ( is_callable( [ $this, 'dieWithError' ] ) ) {
				$this->dieWithError( [ 'apierror-invalidtitle', wfEscapeWikiText( $params['title'] ) ] );
			} else {
				$this->dieUsageMsg( [ 'invalidtitle', $params['title'] ] );
			}
		}
		// Make sure the title can be transcoded
		if ( !TimedMediaHandlerHooks::isTranscodableTitle( $titleObj ) ) {
			if ( is_callable( [ $this, 'dieWithError' ] ) ) {
				$this->dieWithError(
					[
						'apierror-timedmedia-invalidtranscodetitle',
						wfEscapeWikiText( $titleObj->getPrefixedText() )
					],
					'invalidtranscodetitle'
				);
			} else {
				$this->dieUsage( "{$titleObj->getPrefixedText()} is not transcodable.",
					'invalidtranscodetitle' );
			}
		}
		$transcodeKey = false;
		// Make sure its a enabled transcode key we are trying to remove:
		// ( if you update your transcode keys the api is not how you purge the database of expired keys )
		if ( isset( $params['transcodekey'] ) ) {
			$transcodeSet = WebVideoTranscode::enabledTranscodes();
			if ( !in_array( $params['transcodekey'], $transcodeSet ) ) {
				if ( is_callable( [ $this, 'dieWithError' ] ) ) {
					$this->dieWithError(
						[ 'apierror-timedmedia-badtranscodekey', wfEscapeWikiText( $params['transcodekey'] ) ],
						'badtranscodekey'
					);
				} else {
					$this->dieUsage(
						'Invalid or disabled transcode key: ' . htmlspecialchars( $params['transcodekey'] ),
						'badtranscodekey'
					);
				}
			} else {
				$transcodeKey = $params['transcodekey'];
			}
		}

		// Don't reset if less than 1 hour has passed and we have no error )
		$file = wfFindFile( $titleObj );
		$timeSinceLastReset = self::checkTimeSinceLastRest( $file, $transcodeKey );
		if ( $timeSinceLastReset < $wgWaitTimeForTranscodeReset ) {
			$msg = wfMessage(
				'apierror-timedmedia-notenoughtimereset',
				TimedMediaHandler::getTimePassedMsg( $wgWaitTimeForTranscodeReset - $timeSinceLastReset )
			);
			if ( is_callable( [ $this, 'dieWithError' ] ) ) {
				$this->dieWithError( $msg, 'notenoughtimereset' );
			} else {
				$this->dieUsage( $msg->inLanguage( 'en' )->useDatabase( false )->text(),
					'notenoughtimereset' );
			}
		}

		// All good do the transcode removal:
		WebVideoTranscode::removeTranscodes( $file, $transcodeKey );

		// Oh and we wanted to reset it, right? Trigger again.
		WebVideoTranscode::updateJobQueue( $file, $transcodeKey );

		$logEntry = new ManualLogEntry( 'timedmediahandler', 'resettranscode' );
		$logEntry->setPerformer( $this->getUser() );
		$logEntry->setTarget( $titleObj );
		$logEntry->setParameters( [
			'4::transcodekey' => $transcodeKey,
		] );
		$logid = $logEntry->insert();

		$this->getResult()->addValue( null, 'success', 'removed transcode' );
	}

	/**
	 * @param File $file
	 * @param string|bool $transcodeKey
	 * @return int|string
	 */
	public static function checkTimeSinceLastRest( $file, $transcodeKey ) {
		global $wgWaitTimeForTranscodeReset;
		$transcodeStates = WebVideoTranscode::getTranscodeState( $file );
		if ( $transcodeKey ) {
			if ( ! $transcodeStates[$transcodeKey] ) {
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
		if ( !is_null( $state['time_error'] ) ) {
			return $wgWaitTimeForTranscodeReset + 1;
		}
		// return wait time from most recent event
		foreach ( [ 'time_success', 'time_startwork', 'time_addjob' ] as $timeField ) {
			if ( !is_null( $state[ $timeField ] ) ) {
				return $db->timestamp() - $db->timestamp( $state[ $timeField ] );
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

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	protected function getDescription() {
		return 'Users with the \'transcode-reset\' right can reset and re-run a transcode job';
	}

	protected function getAllowedParams() {
		return [
			'title' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			],
			'transcodekey' => null,
			'token' => null,
		];
	}

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	protected function getParamDescription() {
		return [
			'title' => 'The media file title',
			'transcodekey' => 'The transcode key you wish to reset',
			'token' => 'An edit token obtained via action=tokens',
		];
	}

	public function needsToken() {
		return 'csrf';
	}

	public function getTokenSalt() {
		return '';
	}

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	protected function getExamples() {
		return [
			'Reset all transcodes for Clip.webm :',
			'    api.php?action=transcodereset&title=File:Clip.webm&token=%2B\\',
			'Reset the \'360_560kbs.webm\' transcode key for clip.webm. ' .
			'Get a list of transcode keys via a \'transcodestatus\' query',
			'    api.php?action=transcodereset&title=File:Clip.webm' .
			'&transcodekey=360_560kbs.webm&token=%2B\\',
		];
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
