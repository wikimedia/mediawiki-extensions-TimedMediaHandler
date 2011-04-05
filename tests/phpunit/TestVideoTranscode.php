<?php 
/**
 *  
 * @author michael dale
 */
class TesVideoTranscode {
	
	/**
	 * Once video files are uploaded test transcoding
	 * 
	 *  Test if a transcode job is added for a file once requested
	 * 
	 * @dataProvider TestApiUploadVideo::mediaFilesProvider
	 */
	function testAddingTranscodeJob( $file ){
		// Upload the file to the mediaWiki system 
		$result = $this->uploadFile( $file);
		
		$fileName = basename( $file['filePath'] );
		// Get a mediaWiki file object: 
		$mediaFile = wfFindFile( $fileName );
		$videoSources = WebVideoTranscode::getSources( $mediaFile );
		print_r( $videoSources );
		die();
	}
}