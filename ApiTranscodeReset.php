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
		global $wgUser, $wgEnabledTranscodeSet, $wgEnableTranscode;		
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
			$this->dieUsage( array( 'invalidtranscodetitle', $params['title'] ) );
		}
		$file = wfFindFile( $titleObj );
		WebVideoTranscode::removeTranscodeJobs( $file, isset( $params['transcodekey'] )? $params['transcodekey']: false );
		
		$this->getResult()->addValue(null, 'success', 'removed transcode');
	}

	public function mustBePosted() {
		return true;
	}

	public function isWriteMode() {
		return true;
	}

	protected function getDescription() {
		return 'Users with the \'transcode-reset\' right can reset and re-run a transcode job';
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'code' => 'missingpermission', 'info' => 'You don\'t have permission to reset transcodes'),
			array( 'code' => 'disabledtranscode', 'info' => 'Transcode is disabled on this wiki' ),
			array( 'invalidtitle', 'title' ),
		) );
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

	protected function getParamDescription() {
		return array(
			'title' => 'The media file title',
			'transcodekey' => 'The transcode key you wish to reset',
			'token' => 'You can get one of these through prop=info',
		);
	}

	public function needsToken() {
		return true;
	}

	public function getTokenSalt() {
		return '';
	}

	protected function getExamples() {
		return array(
			'Reset all transcodes for Clip.webm :',
			'    api.php?action=transcodereset&title=File:Clip.webm&token=%2B\\',
			'Reset the \'360_560kbs.webm\' transcode key for clip.webm. Get a list of transcode keys via a \'transcodestatus\' query',
			'    api.php?action=transcodereset&title=File:Clip.webm&transcodekey=360_560kbs.webm&token=%2B\\',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id: ApiTranscodeReset.php 89751 $';
	}
	
}