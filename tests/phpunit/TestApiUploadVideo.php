<?php 
/**
 * TestApiUploadVideo test case.
 * 
 * NOTE: This build heavily on ApiUploadTest ( would need to refactor ApiUploadTest for this to work better ) 
 * 
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */

global $IP;
require_once( "$IP/tests/phpunit/includes/api/ApiTestCaseUpload.php" );

/**
 * @group Database
 * @group Destructive
 *
 * This is pretty sucky... needs to be prettified.
 */
class TestApiUploadVideo extends ApiTestCaseUpload {

	/**
	 * Testing login
	 * XXX this is a funny way of getting session context
	 */
	function testLogin() {
		$user = self::$users['uploader'];

		$params = array(
			'action' => 'login',
			'lgname' => $user->username,
			'lgpassword' => $user->password
		);
		list( $result, , $session ) = $this->doApiRequest( $params );
		$this->assertArrayHasKey( "login", $result );
		$this->assertArrayHasKey( "result", $result['login'] );
		$this->assertEquals( "NeedToken", $result['login']['result'] );
		$token = $result['login']['token'];

		$params = array(
			'action' => 'login',
			'lgtoken' => $token,
			'lgname' => $user->username,
			'lgpassword' => $user->password
		);
		list( $result, , $session ) = $this->doApiRequest( $params, $session );
		$this->assertArrayHasKey( "login", $result );
		$this->assertArrayHasKey( "result", $result['login'] );
		$this->assertEquals( "Success", $result['login']['result'] );
		$this->assertArrayHasKey( 'lgtoken', $result['login'] );

		return $session;

	}

	/**
	 * @depends testLogin
	 */
	public function testVideoUpload( $session ){
		global $wgUser;
		$wgUser = self::$users['uploader']->user;

		$extension = 'ogv';
		$mimeType = 'application/ogg';
		
		// Grab a test ogg video: 		
		$filePath =  dirname( __FILE__ ) . '/media/test5seconds.electricsheep.300x400.ogv';
		
		$fileSize = filesize( $filePath );
		$fileName = basename( $filePath );
		
		// remove if already in thd db:
		$this->deleteFileByFileName( $fileName );
		$this->deleteFileByContent( $filePath );

		if (! $this->fakeUploadFile( 'file', $fileName, $mimeType, $filePath ) ) {
			$this->markTestIncomplete( "Couldn't upload file!\n" );
		}

		$params = array(
			'action' => 'upload',
			'filename' => $fileName,
			'file' => 'dummy content',
			'comment' => 'dummy comment',
			'text'	=> "This is the page text for $fileName",
		);

		$exception = false;
		try {
			list( $result, , ) = $this->doApiRequestWithToken( $params, $session );
		} catch ( UsageException $e ) {
			$exception = true;
		}

		$this->assertTrue( isset( $result['upload'] ) );
		$this->assertEquals( 'Success', $result['upload']['result'] );
		$this->assertEquals( $fileSize, ( int )$result['upload']['imageinfo']['size'] );
		$this->assertEquals( $mimeType, $result['upload']['imageinfo']['mime'] );
		$this->assertFalse( $exception );

		// clean up
		$this->deleteFileByFilename( $fileName );
		unlink( $filePath );
	}
}

