<?php
/**
 * Abstract test class to support Video Tests with video uploads
 * @author dale
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

// Include core class ApiTestCaseUpload ( not part of base autoLoader )
// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.wgPrefix
global $IP;
require_once "$IP/tests/phpunit/includes/api/ApiTestCaseUpload.php";

abstract class ApiTestCaseVideoUpload extends ApiTestCaseUpload {
	/**
	 * @return Array set of test files with associated metadata
	 */
	static function mediaFilesProvider() {
		return [
			[
				// Double wrap the file array to match phpunit data provider conventions
				[
					'mime' => 'application/ogg',
					'filePath' => __DIR__ . '/media/test5seconds.electricsheep.300x400.ogv',
					"size" => 301477,
					"width"  => 400,
					"height" => 300,
					"mediatype" => "VIDEO",
					"bandwidth" => 452216,
					"framerate" => 30
				]
			],
			[
				[
					'mime' => 'video/webm',
					'filePath' => __DIR__ . '/media/shuttle10seconds.1080x608.webm',
					"size" => 699018,
					"width" => 1080,
					"height" => 608,
					"mediatype" => "VIDEO",
					"bandwidth" => 522142,
					"framerate" => 29.97
				]
			]
		];
	}

	/**
	 * Fixture -- run after every test
	 * Clean up temporary files etc.
	 */
	protected function tearDown() {
		parent::tearDown();

		$testMediaFiles = $this->mediaFilesProvider();
		foreach ( $testMediaFiles as $file ) {
			$file = $file[0];
			// Clean up and delete all files
			$this->deleteFileByFilename( $file['filePath'] );
		}
	}

	/**
	 * Do login
	 * @param string $user
	 * @return array
	 */
	protected function doLogin( $user = 'sysop' ) {
		$user = self::$users['uploader'];

		$params = [
			'action' => 'login',
			'lgname' => $user->getUser()->getName(),
			'lgpassword' => $user->getPassword()
		];
		list( $result, , $session ) = $this->doApiRequest( $params );
		$token = $result['login']['token'];

		$params = [
			'action' => 'login',
			'lgtoken' => $token,
			'lgname' => $user->getUser()->getName(),
			'lgpassword' => $user->getPassword()
		];
		list( $result, , $session ) = $this->doApiRequest( $params, $session );
		return $session;
	}

	/**
	 * uploads a file
	 * @param array $file
	 * @return array
	 */
	public function uploadFile( $file ) {
		global $wgUser;
		// get a session object
		$session = $this->doLogin();
		// Update the global user:
		$wgUser = self::$users['uploader']->getUser();

		// Upload the media file:
		$fileName = basename( $file['filePath'] );

		// remove if already in thd db:
		$this->deleteFileByFileName( $fileName );
		$this->deleteFileByContent( $file['filePath'] );

		if ( !$this->fakeUploadFile( 'file', $fileName, $file['mime'], $file['filePath'] ) ) {
			$this->markTestIncomplete( "Couldn't upload file!\n" );
		}

		$params = [
			'action' => 'upload',
			'filename' => $fileName,
			'file' => 'dummy content',
			'comment' => 'dummy comment',
			'text'	=> "This is the page text for $fileName",
			// This uploadFile function supports video tests not a test upload warnings
			'ignorewarnings' => true
		];

		try {
			list( $result, , ) = $this->doApiRequestWithToken( $params, $session );
		} catch ( Exception $e ) {
			// Could not upload mark test that called uploadFile as incomplete
			$this->markTestIncomplete( $e->getMessage() );
		}

		return $result;
	}

}
