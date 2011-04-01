<?php 
/**
 * TestApiUploadVideo test case.
 * 
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */

class TestApiUploadVideo extends ApiUploadTest {

	/**
	 * @depends testLogin
	 */
	public function testVideoUpload( $session ){
		global $wgUser;
		$wgUser = self::$users['uploader']->user;

		$extension = 'ogv';
		$mimeType = 'video/ogg';
		
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