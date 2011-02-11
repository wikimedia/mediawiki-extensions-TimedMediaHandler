<?php 
/**
 * Job for transcode jobs
 *
 * @file
 * @ingroup JobQueue
 */

/**
 * Job for web video transcode
 *
 * Support two modes 
 * 1) non-free media transcode ( dealys the media file being inserted, adds note to talk page once ready)
 * 2) derivatives for video ( makes new sources for the asset )
 * 
 * @ingroup JobQueue
 */
class WebVideoTranscodeJob extends Job {
	
	public function __construct( $title, $params, $id = 0 ) {
		parent::__construct( 'webVideoTranscode', $title, $params, $id );
	}

	public function run() {
		
	}
	
}