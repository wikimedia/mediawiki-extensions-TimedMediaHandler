<?php
/**
 * TestApiVideoInfo test case.
 * 
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */
class TestApiVideoInfo extends ApiTestCaseUpload {	
	/**
	 * @depends testUploadVideoFiles
	 */
	function testVideoInfo() {
		// Once video files are uploaded grab all the media files and run api info test on them
		$mediaFiles = TestApiUploadVideo::getTestMediaList();
		foreach( $mediaFiles as $file ){
			
		}
	}
	
}