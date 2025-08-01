<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiMain;
use MediaWiki\FileRepo\File\File;
use MediaWiki\FileRepo\RepoGroup;
use MediaWiki\Logging\ManualLogEntry;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\Rdbms\IConnectionProvider;

/**
 * Allows users with the 'transcode-reset' right to reset / re-run a transcode job.
 *
 * You can specify must specify a media asset title. You optionally can specify
 * a transcode key, to only reset a single transcode job for a particular media asset.
 * @ingroup API
 */
class ApiTranscodeReset extends ApiBase {
	private readonly TranscodableChecker $transcodableChecker;

	/**
	 * @param ApiMain $main
	 * @param string $action
	 * @param IConnectionProvider $dbProvider
	 * @param RepoGroup $repoGroup
	 */
	public function __construct(
		ApiMain $main,
		string $action,
		private readonly IConnectionProvider $dbProvider,
		private readonly RepoGroup $repoGroup,
	) {
		parent::__construct( $main, $action );
		$this->transcodableChecker = new TranscodableChecker(
			$this->getConfig(),
			$repoGroup
		);
	}

	public function execute(): void {
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
		if ( !$this->transcodableChecker->isTranscodableTitle( $titleObj ) ) {
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
		$timeSinceLastReset = $this->checkTimeSinceLastReset( $file, $transcodeKey );
		$waitTimeForTranscodeReset = $this->getConfig()->get( 'WaitTimeForTranscodeReset' );
		if ( $timeSinceLastReset < $waitTimeForTranscodeReset ) {
			$msg = $this->msg(
				'apierror-timedmedia-notenoughtimereset',
			)->durationParams( $waitTimeForTranscodeReset - $timeSinceLastReset );
			$this->dieWithError( $msg, 'notenoughtimereset' );
		}

		// All good do the transcode removal:
		WebVideoTranscode::removeTranscodes( $file, $transcodeKey );

		// Oh and we wanted to reset it, right? Trigger again.
		$options = [
			'manualOverride' => true,
		];
		WebVideoTranscode::updateJobQueue( $file, $transcodeKey, $options );

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
	public function checkTimeSinceLastReset( File $file, $transcodeKey ) {
		$dbw = $file->repo->getPrimaryDB();
		$transcodeStates = WebVideoTranscode::getTranscodeState( $file, $dbw );
		if ( $transcodeKey ) {
			if ( !$transcodeStates[$transcodeKey] ) {
				// transcode key not found
				return $this->getConfig()->get( 'WaitTimeForTranscodeReset' ) + 1;
			}
			return $this->getStateResetTime( $transcodeStates[$transcodeKey] );
		}
		// least wait is set to reset time:
		$leastWait = $this->getConfig()->get( 'WaitTimeForTranscodeReset' ) + 1;
		// else check for lowest reset time
		foreach ( $transcodeStates as $state ) {
			$ctime = $this->getStateResetTime( $state );
			if ( $ctime < $leastWait ) {
				$leastWait = $ctime;
			}
		}
		return $leastWait;
	}

	/**
	 * @return int|string
	 */
	public function getStateResetTime( array $state ) {
		$db = $this->dbProvider->getReplicaDatabase();
		// if an error return waitTime +1
		if ( $state['time_error'] !== null ) {
			return $this->getConfig()->get( 'WaitTimeForTranscodeReset' ) + 1;
		}
		// return wait time from most recent event
		foreach ( [ 'time_success', 'time_startwork', 'time_addjob' ] as $timeField ) {
			if ( ( $state[ $timeField ] ) !== null ) {
				return (int)$db->timestamp() - (int)$db->timestamp( $state[ $timeField ] );
			}
		}
		// No time info, return resetWaitTime
		return $this->getConfig()->get( 'WaitTimeForTranscodeReset' ) + 1;
	}

	/** @inheritDoc */
	public function mustBePosted(): bool {
		return true;
	}

	/** @inheritDoc */
	public function isWriteMode(): bool {
		return true;
	}

	/** @inheritDoc */
	protected function getAllowedParams(): array {
		return [
			'title' => [
				ParamValidator::PARAM_TYPE => 'string',
				ParamValidator::PARAM_REQUIRED => true
			],
			'transcodekey' => null,
			'token' => null,
		];
	}

	/** @inheritDoc */
	public function needsToken(): string {
		return 'csrf';
	}

	/**
	 * @see ApiBase::getExamplesMessages()
	 */
	protected function getExamplesMessages(): array {
		return [
			'action=transcodereset&title=File:Clip.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-1',
			'action=transcodereset&title=File:Clip.webm&transcodekey=360_560kbs.webm&token=123ABC'
				=> 'apihelp-transcodereset-example-2',
		];
	}
}
