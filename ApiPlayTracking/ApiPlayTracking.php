<?php
/**
 * Extend the API for play request tracking
 *
 * @file
 * @ingroup API
 */

class ApiPlayTracking extends ApiBase {

	/**
	 * Runs when the API is called with "playtracking", takes in "filename"
	 * and "client" serialized data for ogg support analysis
	 *
	 * @see includes/api/ApiBase#execute()
	 */
	public function execute() {
		global $wgEnablePlayTracking, $wgSecretKey, $wgUser, $wgPlayTrackingRate;
		if( ! $wgEnablePlayTracking ){
			$this->dieUsageMsg( array( 'unknownerror', 'Play tracking is not enabled' ) );
		}
		$params = $this->extractRequestParams();
		$this->validateParams( $params );

		// Salt the user id with $wgSecretKey to get a unique key per user
		$clientId = md5( $wgUser->getName() . $wgSecretKey );

		// Insert into the play_tracking table
		$dbw = & wfGetDB( DB_WRITE );
		$dbw->insert(
			'play_tracking',
			array(
				'track_filename' => $params[ 'filename' ],
				'track_client_id' => $clientId,
				'track_clientplayer' => $params[ 'client' ],
				'track_rate' => $wgPlayTrackingRate
			),
			__METHOD__
		);
	}

	/**
	 * Required parameter check
	 * @param $params params extracted from the POST
	 */
 	protected function validateParams( $params ) {
		$required = array( 'filename', 'client' );
		foreach ( $required as $arg ) {
			if ( !isset( $params[$arg] ) ) {
				$this->dieUsageMsg( array( 'missingparam', $arg ) );
			}
		}
	}

	/**
	* Setup the ApiTracking tables
	*/
	public static function schema() {
		global $wgExtNewTables, $wgExtNewIndexes;

		$wgExtNewTables[] = array(
			'play_tracking',
			dirname( __FILE__ ) . '/ApiPlayTracking.sql'
		);

		return true;
	}

	public function getParamDescription() {
		return array(
			'filename' => 'title of filename played',
			'client'  => 'seralized data about client playback support',
		);
	}

	public function getDescription() {
		return array(
			'Track user audio and video play requests.'
		);
	}

	public function getAllowedParams() {
		return array(
			'filename' => null,
			'client' => null,
		);
	}
	public function getVersion() {
		return __CLASS__ . ': $Id: ApiPlayTracking.php 59374 2009-11-24 01:06:56Z dale $';
	}
}

?>