<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	// Eclipse helper - will be ignored in production
	require_once( 'ApiBase.php' );
}

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
		if( !$wgEnableTranscode ){
			$this->dieUsage( 'Transcode is disabled on this wiki', 'disabledtranscode' );
		}

		// Confirm the user has the transcode-reset right
		if( !$wgUser->isAllowed( 'transcode-reset' ) ){
			$this->dieUsage( 'You don\'t have permission to reset transcodes', 'missingpermission' );
		}
		$params = $this->extractRequestParams();

		// Make sure we have a valid Title
		$titleObj = Title::newFromText( $params['title'] );
		if ( !$titleObj || $titleObj->isExternal() ) {
			$this->dieUsageMsg( array( 'invalidtitle', $params['title'] ) );
		}
		// Make sure the title can be transcoded
		if( !TimedMediaHandlerHooks::isTranscodableTitle( $titleObj ) ){
			$this->dieUsageMsg( array( 'invalidtranscodetitle', $params['title'] ) );
		}
		$transcodeKey = false;
		// Make sure its a enabled transcode key we are trying to remove:
		// ( if you update your transcode keys the api is not how you purge the database of expired keys )
		if( isset( $params['transcodekey'] ) ){
			global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;
			$transcodeSet = array_merge($wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet);
			if( !in_array( $params['transcodekey'], $transcodeSet ) ){
				$this->dieUsage( 'Invalid or disabled transcode key: ' . htmlspecialchars( $params['transcodekey'] ) , 'badtranscodekey' );
			} else {
				$transcodeKey = $params['transcodekey'];
			}
		}

		// Don't reset if less than 1 hour has passed and we have no error )
		$file = wfFindFile( $titleObj );
		$timeSinceLastReset = self::checkTimeSinceLastRest( $file, $transcodeKey );
		if( $timeSinceLastReset < $wgWaitTimeForTranscodeReset){
			$this->dieUsage( 'Not enough time has passed since the last reset of this transcode. ' .
				TimedMediaHandler::getTimePassedMsg( $wgWaitTimeForTranscodeReset - $timeSinceLastReset  ) .
				' until this transcode can be reset', 'notenoughtimereset');
		}

		// All good do the transcode removal:
		WebVideoTranscode::removeTranscodes( $file, $transcodeKey );

		$this->getResult()->addValue(null, 'success', 'removed transcode');
	}

	/**
	 * @param $file
	 * @param $transcodeKey
	 * @return int|string
	 */
	static public function checkTimeSinceLastRest( $file, $transcodeKey ){
		global $wgWaitTimeForTranscodeReset;
		$transcodeStates = WebVideoTranscode::getTranscodeState( $file );
		if( $transcodeKey ){
			if( ! $transcodeStates[$transcodeKey] ){
				// transcode key not found
				return $wgWaitTimeForTranscodeReset + 1;
			}
			return self::getStateResetTime( $transcodeStates[$transcodeKey] );
		}
		// least wait is set to reset time:
		$leastWait = $wgWaitTimeForTranscodeReset + 1;
		// else check for lowest reset time
		foreach($transcodeStates as $state ){
			$ctime = self::getStateResetTime( $state );
			if( $ctime < $leastWait){
				$leastWait = $ctime;
			}
		}
		return $leastWait;
	}

	/**
	 * @param $state
	 * @return int|string
	 */
	static public function getStateResetTime( $state ){
		global $wgWaitTimeForTranscodeReset;
		$db = wfGetDB( DB_SLAVE );
		// if an error return waitTime +1
		if( !is_null( $state['time_error']) ){
			return $wgWaitTimeForTranscodeReset + 1;
		}
		// return wait time from most recent event
		foreach( array( 'time_success', 'time_startwork', 'time_addjob' ) as $timeField ){
			if( !is_null( $state[ $timeField ] )){
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
		return array(
			'title' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'transcodekey' => null,
			'token' => null,
		);
	}

	/**
	 * @deprecated since MediaWiki core 1.25
	 */
	protected function getParamDescription() {
		return array(
			'title' => 'The media file title',
			'transcodekey' => 'The transcode key you wish to reset',
			'token' => 'An edit token obtained via action=tokens',
		);
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
		return array(
			'Reset all transcodes for Clip.webm :',
			'    api.php?action=transcodereset&title=File:Clip.webm&token=%2B\\',
			'Reset the \'360_560kbs.webm\' transcode key for clip.webm. Get a list of transcode keys via a \'transcodestatus\' query',
			'    api.php?action=transcodereset&title=File:Clip.webm&transcodekey=360_560kbs.webm&token=%2B\\',
		);
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 */
	protected function getExamplesMessages() {
		return array(
			'action=transcodereset&title=File:Clip.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-1',
			'action=transcodereset&title=File:Clip.webm&transcodekey=360_560kbs.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-2',
		);
	}
}
