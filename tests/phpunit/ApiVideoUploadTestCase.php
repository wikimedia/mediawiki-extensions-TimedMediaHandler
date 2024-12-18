<?php

use MediaWiki\MainConfigNames;
use MediaWiki\Tests\Api\ApiUploadTestCase;

/**
 * Abstract test class to support Video Tests with video uploads
 * @author dale
 */
abstract class ApiVideoUploadTestCase extends ApiUploadTestCase {
	/**
	 * @return array set of test files with associated metadata
	 */
	public static function mediaFilesProvider() {
		return [
			[
				// Double wrap the file array to match phpunit data provider conventions
				[
					'mime' => 'application/ogg',
					'filePath' => __DIR__ . '/media/test5seconds.electricsheep.300x400.ogv',
					'size' => 301477,
					'width' => 400,
					'height' => 300,
					'mediatype' => MEDIATYPE_VIDEO,
					'bandwidth' => 449642,
					'framerate' => 30,
				]
			],
			[
				[
					'mime' => 'video/webm',
					'filePath' => __DIR__ . '/media/shuttle10seconds.1080x608.webm',
					'size' => 699018,
					'width' => 1080,
					'height' => 608,
					'mediatype' => MEDIATYPE_VIDEO,
					'bandwidth' => 522142,
					'framerate' => 29.97,
				]
			],
			[
				[
					'mime' => 'audio/midi',
					'filePath' => __DIR__ . '/media/c-major.midi',
					'size' => 262,
					'mediatype' => MEDIATYPE_AUDIO,
				],
			]
		];
	}

	protected function setUp(): void {
		parent::setUp();
		$this->overrideConfigValues( [
			MainConfigNames::UseInstantCommons => false,
			MainConfigNames::ForeignFileRepos => []
		] );
	}

	/**
	 * Fixture -- run after every test
	 * Clean up temporary files etc.
	 */
	protected function tearDown(): void {
		parent::tearDown();

		$testMediaFiles = self::mediaFilesProvider();
		foreach ( $testMediaFiles as $file ) {
			$file = $file[0];
			// Clean up and delete all files
			$this->deleteFileByFilename( basename( $file['filePath'] ) );
		}
	}

	/**
	 * Do login
	 * @return array
	 */
	private function login() {
		$user = $this->getTestUser();

		$params = [
			'action' => 'login',
			'lgname' => $user->getUser()->getName(),
			'lgpassword' => $user->getPassword()
		];
		[ $result, , $session ] = $this->doApiRequest( $params );
		$token = $result['login']['token'];

		$params = [
			'action' => 'login',
			'lgtoken' => $token,
			'lgname' => $user->getUser()->getName(),
			'lgpassword' => $user->getPassword()
		];
		[ , , $session ] = $this->doApiRequest( $params, $session );
		return $session;
	}

	/**
	 * uploads a file
	 * @param array $file
	 * @return array
	 */
	public function uploadFile( $file ) {
		// get a session object
		$session = $this->login();

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
			[ $result, , ] = $this->doApiRequestWithToken(
				$params,
				$session,
				$this->getTestUser()->getUser()
			);
		} catch ( Exception $e ) {
			// Could not upload mark test that called uploadFile as incomplete
			$this->markTestIncomplete( $e->getMessage() );
		}

		return $result;
	}

}
