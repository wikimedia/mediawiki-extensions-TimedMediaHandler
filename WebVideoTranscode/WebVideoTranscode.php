<?php

/*
 * WebVideoTranscode provides:
 *  encode keys
 *  encode settings  
 *  
 * 	extends api to return all the streams
 *  extends video tag output to provide all the available sources
 */


/*
 * Main WebVideoTranscode Class hold some constants and config values
 */
class WebVideoTranscode {
	
	/**
	* Key constants for the derivatives,
	* this key is appended to the derivative file name
	*
	* If you update the wgDerivativeSettings for one of these keys
	* and want to re-generate the video you should also update the
	* key constant. ( Or just run a maintenance script to delete all
	* the assets for a given profile )
	* 
	* Msg keys for derivatives are set as follows: 
	* $messages['timedmedia-derivative-200_200kbs.ogv'] => 'Ogg 200';
	*/
	
	// Ogg Profiles
	const ENC_OGV_2MBS = '220_200kbs.ogv';
	const ENC_OGV_5MBS = '360_560kbs.ogv';
	const ENC_OGV_9MBS = '480_880kbs.ogv';
	const ENC_OGV_HQ_VBR = '720_VBR.ogv';
	
	// WebM profiles: 	
	const ENC_WEBM_5MBS = '360_560kbs.webm';
	const ENC_WEBM_9MBS = '480_900kbs.webm';
	const ENC_WEBM_HQ_VBR = '720_VBR.webm';
	
	// Static cache of transcode state per instantiation 
	public static $transcodeStateCache = null;
	
	/**
	* Encoding parameters are set via firefogg encode api
	*
	* For clarity and compatibility with passing down
	* client side encode settings at point of upload
	*
	* http://firefogg.org/dev/index.html
	*/
	public static $derivativeSettings = array(
		WebVideoTranscode::ENC_OGV_2MBS =>
			array(
				'maxSize'			=> '220', // 160P or around there 
				'videoBitrate'		=> '160',
				'audioBitrate'		=> '32',
				'samplerate'		=> '22050',
				'framerate'			=> '18',
				'channels'			=> '1',
				'noUpscaling'		=> 'true',
				'twopass' 			=> 'true',
				'keyframeInterval'	=> '64',
				'bufDelay'			=> '128',
				'videoCodec' 		=> 'theora',
			),
		WebVideoTranscode::ENC_OGV_5MBS =>
			array(
				'maxSize'			=> '480', // 360P
				'videoBitrate'		=> '512',
				'audioBitrate'		=> '48',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'videoCodec' 			=> 'theora',
			),
		WebVideoTranscode::ENC_OGV_9MBS =>
			array(
				'maxSize'			=> '640', // 480P
				'videoBitrate'		=> '786',
				'audioBitrate'		=> '96',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'videoCodec' 		=> 'theora',
			),

		WebVideoTranscode::ENC_OGV_HQ_VBR =>
			array(
				'maxSize'			=> '1280', // 720P
				'videoQuality'		=> 6,
				'audioQuality'		=> 3,
				'noUpscaling'		=> 'true',
				'keyframeInterval'	=> '128',
				'videoCodec' 		=> 'theora',
			),	

		// WebM transcode:
		WebVideoTranscode::ENC_WEBM_5MBS => 
			array(
				'maxSize'			=> '480', // 380P
				'videoBitrate'		=> '512',
				'audioBitrate'		=> '48',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'videoCodec' 		=> 'vp8',
			),
		WebVideoTranscode::ENC_WEBM_9MBS =>
			array(
			 	'maxSize'			=> '640', // 480P
				'videoBitrate'		=> '786',
				'audioBitrate'		=> '96',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'videoCodec' 		=> 'vp8',
			),
		WebVideoTranscode::ENC_WEBM_HQ_VBR =>
			 array(
				'maxSize'			=> '1280', // 720P
				'videoQuality'		=> 7,
				'audioQuality'		=> 3,
				'noUpscaling'		=> 'true',
				'videoCodec' 		=> 'vp8',
			)
	);
	
	static public function getDerivativeFilePath( &$file, $transcodeKey){
		return dirname( 
				$file->getThumbPath(
					$file->thumbName( array() )
				)
			) . '/' . 
			$file->getName() . '.' .
			$transcodeKey ;
	}
	/**
	 * Get the target encode path for a video encode
	 * 
	 * @param File $file
	 * @param String $transcodeKey
	 * 
	 * @returns the local target encode path
	 */
	static public function getTargetEncodePath( &$file, $transcodeKey ){
		// TODO probably should use some other temporary non-web accessible location for 
		// in-progress encodes.
		$filePath = self::getDerivativeFilePath( $file, $transcodeKey );
		$ext = strtolower( pathinfo( "$filePath", PATHINFO_EXTENSION ) );
		return "{$filePath}.queue.{$ext}";
	}
	/**
	 * Get the max size of the web stream ( constant bitrate ) 
	 */
	static public function getMaxSizeWebStream(){
		global $wgEnabledTranscodeSet;
		$maxSize = 0;
		foreach( $wgEnabledTranscodeSet as $transcodeKey ){
			if( isset( self::$derivativeSettings[$transcodeKey]['videoBitrate'] ) ){
				$maxSize = self::$derivativeSettings[$transcodeKey]['maxSize'];
			}
		}
		return $maxSize;
	}
	
	/** 
	 * Static function to get the set of video assets 
	 * Checks if the file is local or remote and grabs respective sources
	 */
	static public function getSources( &$file , $options = array() ){
		if( $file->isLocal() ){
			return self::getLocalSources( $file , $options );
		}else {
			return self::getRemoteSources( $file , $options );
		}
	}
	/**
	 * Grabs sources from the remote repo via ApiQueryVideoInfo.php entry point. 
	 * 
	 * Because this works on both TimedMediaHandler commons and no TimedMediaHandler commons
	 */
	static public function getRemoteSources(&$file , $options = array() ){
		global $wgMemc;
		// Setup source attribute options
		$dataPrefix = in_array( 'nodata', $options )? '': 'data-';
		
		// Use descriptionCacheExpiry as our expire for timed text tracks info
		if ( $file->repo->descriptionCacheExpiry > 0 ) {
			wfDebug("Attempting to get sources from cache...");
			$key = $file->repo->getLocalCacheKey( 'WebVideoSources', 'url', $file->getName() );
			$sources = $wgMemc->get($key);
			if ( $sources ) {
				wfDebug("Success found sources in local cache\n");
				return $sources;
			}
			wfDebug("source cache miss\n");
		}
		wfDebug("Get Video sources from remote api \n");
		$data = $file->repo->fetchImageQuery(  array( 
			'action' => 'query',
			'prop' => 'videoinfo',
			'viprop' => 'derivatives',
			'title' => $file->getTitle()->getDBKey()
		) );
		
		if( isset( $data['warnings'] ) && isset( $data['warnings']['query'] )
			&& $data['warnings']['query']['*'] == "Unrecognized value for parameter 'prop': videoinfo" )
		{
			// Commons does not yet have TimedMediaHandler. 
			// Use the normal file repo system single source:
			return array( self::getPrimarySourceAttributes( $file, array( $dataPrefix ) ) );
		}
		$sources = array();
		// Generate the source list from the data response:
		if( $data['query'] && $data['query']['pages'] ){
			$vidResult = first( $data['query']['pages'] );
			if( $vidResult['videoinfo'] ){
				$derResult =  first( $vidResult['videoinfo'] );
				$derivatives = $derResult['derivatives'];
				foreach( $derivatives as $derivativeSource ){
					$sources[] = $derivativeSource;
				}
			}
		}
		
		// Update the cache: 
		if ( $sources && $this->file->repo->descriptionCacheExpiry > 0 ) {
			$wgMemc->set( $key, $sources, $this->file->repo->descriptionCacheExpiry );
		}
		
		return $sources;
		
	}

	/**
	 * Based on the $wgEnabledTranscodeSet set of enabled derivatives we 
	 * sync the database with $wgEnabledTranscodeSet and return sources that are ready
	 * 	
	 * If no transcode is in progress or ready add the job to the jobQueue
	 * 
	 * @param {Object} File object
	 * @param {Object} Options, a set of options:
	 * 					'nodata' Strips the data- attribute, useful when your output is not html
	 * @returns an associative array of sources suitable for <source> tag output
	 */
	static public function getLocalSources( &$file , $options=array() ){
		global $wgEnabledTranscodeSet, $wgLang;
		$sources = array();
		
		// Add the original file: 
		$source = self::getPrimarySourceAttributes( $file, $options );
				
		// Just directly return audio sources ( No transcoding for audio for now ) 
		if( $file->getHandler()->isAudio( $file ) ){			
			return array( $source );
		}
		
		// Add the source to the sources array
		$sources[] = $source;
		
		// Setup local variables 
		$fileName = $file->getName();
		
		$addOggFlag = false;
		$addWebMFlag = false;
		
		$ext = pathinfo( "$fileName", PATHINFO_EXTENSION);
		
		// Check the source file for .webm extension 
		if( strtolower( $ext )== 'webm' ) {
			$addWebMFlag = true;
		} else {
			// If not webm assume ogg as the source file 	
			$addOggFlag = true;
		}		
		
		// Now Check for derivatives and add to transcode table if missing:
		foreach( $wgEnabledTranscodeSet as $transcodeKey ){
			$codec =  self::$derivativeSettings[$transcodeKey]['videoCodec'];
			// Check if we should add derivative to job queue 
			// Skip if we have both an Ogg & WebM and if target encode larger than source
			if( self::isTargetLargerThanFile( $file, self::$derivativeSettings[$transcodeKey]['maxSize']) ){				
				continue;
			}			
			// if we going to try add source for this derivative, update codec flags: 
			if( $codec == 'theora' ){
				$addOggFlag = true;
			}
			if( $codec == 'vp8' ){
				$addWebMFlag = true;
			}
			// Try and add the source
			self::addSourceIfReady( $file, $sources, $transcodeKey, $options );
		}	
		// Make sure we have at least one ogg and webm encode 
		if( !$addOggFlag || !$addWebMFlag ){
			foreach( $wgEnabledTranscodeSet as $transcodeKey ){
				if( !$addOggFlag && self::$derivativeSettings[$transcodeKey]['videoCodec'] == 'theora' ){
					self::addSourceIfReady( $file, $sources, $transcodeKey, $options );
					$addOggFlag = true;
				}
				if( !$addWebMFlag && self::$derivativeSettings[$transcodeKey]['videoCodec'] == 'vp8' ){
					self::addSourceIfReady( $file, $sources, $transcodeKey, $options );
					$addWebMFlag = true;
				}
			}
		}
		return $sources;
	}
	
	/**
	 * Get the transcode state for a given filename and transcodeKey
	 * 
	 * @param {string} $fileName
	 */
	public static function isTranscodeReady( $fileName, $transcodeKey ){
		
		// Check if we need to populate the transcodeState cache: 
		if( !self::$transcodeStateCache || !isset( self::$transcodeStateCache[ $fileName ] ) ) {
			self::getTranscodeStateCache( $fileName );
		}
		// If no state is found the cache for this file is false: 
		if( !isset( self::$transcodeStateCache[ $fileName ][ $transcodeKey ]) 
				||
			self::$transcodeStateCache[ $fileName ][ $transcodeKey ] === false )
		{
			return false;
		}
		// Else return the state:
		return self::$transcodeStateCache[$fileName][$transcodeKey]['ready'];
	}
	/**
	 * Clear the transcode state cache: 
	 */
	public static function clearTranscodeCache(){
		self::$transcodeStateCache = null;
	}

	/**
	 * Populates the transcode table with the current DB state of transcodes 
	 * if transcodes are not found in the database their state is set to "false"
	 * 
	 * @param string $fileName key
	 */
	public static function getTranscodeStateCache( $fileName ){
		wfProfileIn( __METHOD__ );
		$res = wfGetDB( DB_SLAVE )->select( 'transcode', 
				array( 'transcode_key', 'transcode_time_success','transcode_time_addjob','transcode_final_bitrate' ) , 
				array( 'transcode_image_name' => $fileName ),
				__METHOD__, 
				array( 'LIMIT' => 100 )
		);			
		// Populate the per transcode state cache   
		foreach ( $res as $row ) {
			self::$transcodeStateCache[$fileName][ $row->transcode_key ] = array(
				'ready' => !is_null( $row->transcode_time_success ),
				'bitrate' => $row->transcode_final_bitrate,
				'addjob' => $row->transcode_time_addjob,		
			);
		}		
		wfProfileOut( __METHOD__ );
	}
	/**
	 * Remove any transcode jobs associated with a given $fileName
	 * 
	 * also remove the transcode files: 
	 */
	public static function removeTranscodeJobs( &$file ){
		$fileName = $file->getTitle()->getDbKey();
		
		$res = wfGetDB( DB_SLAVE )->select( 'transcode', 
			array( 'transcode_key' ),
			array( 'transcode_image_name' => $fileName )
		);
		// remove the file
		foreach( $res as $transcodeRow ){
			$filePath = self::getDerivativeFilePath($file, $transcodeRow->transcode_key );
			if( ! @unlink( $filePath ) ){
				wfDebug( "Could not delete file $filePath\n" );
			}
		} 
		// Remove the db entries
		wfGetDB( DB_MASTER )->delete( 'transcode', 
		 	array( 'transcode_image_name' => $fileName ),
		 	__METHOD__ 
		);
	}
	
	/**
	 * Add a source to the sources list if the transcode job is ready
	 * if the source is not found update the job queue
	 */
	public static function addSourceIfReady( &$file, &$sources, $transcodeKey, $dataPrefix = '' ){
		global $wgLang;
		$fileName = $file->getTitle()->getDbKey();
		// Check if the transcode is ready: 			
		if( self::isTranscodeReady( $fileName, $transcodeKey ) ){
			$sources[] = self::getDerivativeSourceAttributes( $file, $transcodeKey, $dataPrefix );
		} else {
			self::updateJobQueue( $file, $transcodeKey ); 
		}
	}
	/**
	 * Get the primary "source" asset used for other derivatives
	 */
	static public function getPrimarySourceAttributes($file, $options = array() ){
		global $wgLang;
		// Setup source attribute options
		$dataPrefix = in_array( 'nodata', $options )? '': 'data-';
		$src = in_array( 'fullurl', $options)?  wfExpandUrl( $file->getUrl() ) : $file->getUrl();
		
		$source = array(
			'src' => $src,
			'title' => wfMsg('timedmedia-source-file-desc',  
								$file->getHandler()->getMetadataType(),
								$wgLang->formatNum( $file->getWidth() ),
								$wgLang->formatNum( $file->getHeight() ),
								$wgLang->formatBitrate( $file->getHandler()->getBitrate( $file ) )
							),
			"{$dataPrefix}shorttitle" => wfMsg('timedmedia-source-file', wfMsg( 'timedmedia-' . $file->getHandler()->getMetadataType() ) ),							
			"{$dataPrefix}width" => $file->getWidth(),
			"{$dataPrefix}height" => $file->getHeight(),
		);
		 		
		$bitrate = $file->getHandler()->getBitrate( $file );
		if( $bitrate ) 
			$source["{$dataPrefix}bandwidth"] = round ( $bitrate );
			
		// For video include framerate:
		if( !$file->getHandler()->isAudio( $file ) ){
			$framerate = $file->getHandler()->getFramerate( $file );
			if( $framerate ) 
				$source[ "{$dataPrefix}framerate" ] = $framerate;
		}
		return $source;
	}
	/**
	 * Get derivative "source" attributes 
	 */
	static public function getDerivativeSourceAttributes($file, $transcodeKey, $options = array() ){
		$dataPrefix = in_array( 'nodata', $options )? '': 'data-';
		
		
		$fileName = $file->getTitle()->getDbKey();
		
		$thumbName = $file->thumbName( array() );
		$thumbUrl = $file->getThumbUrl( $thumbName );
		$thumbUrlDir = dirname( $thumbUrl );
		
		list( $width, $height ) = WebVideoTranscode::getMaxSizeTransform( 
			$file, 
			self::$derivativeSettings[$transcodeKey]['maxSize'] 
		);
		
		$framerate = ( isset( self::$derivativeSettings[$transcodeKey]['framerate'] ) )? 
						self::$derivativeSettings[$transcodeKey]['framerate'] :
						$file->getHandler()->getFramerate( $file );
		// Setup the url src: 
		$src = $thumbUrlDir . '/' .$file->getName() . '.' . $transcodeKey;
		$src = in_array( 'fullurl', $options)?  wfExpandUrl( $src ) : $src;
		return array(
				'src' => $src,
				'title' => wfMsg('timedmedia-derivative-desc-' . $transcodeKey ),
				"{$dataPrefix}shorttitle" => wfMsg('timedmedia-derivative-' . $transcodeKey),
				
				// Add data attributes per emerging DASH / webTV adaptive streaming attributes
				// eventually we will define a manifest xml entry point.
				"{$dataPrefix}width" => $width,
				"{$dataPrefix}height" => $height,
				// a "ready" transcode should have a bitrate:  
				"{$dataPrefix}bandwidth" => self::$transcodeStateCache[$fileName][ $transcodeKey ]['bitrate'],
				"{$dataPrefix}framerate" => $framerate,
			);
	}
	/**
	 * Update the job queue if the file is not already in the job queue:
	 * @param object File object 
	 * @param 
	 */	
	public static function updateJobQueue( &$file, $transcodeKey ){
		wfProfileIn( __METHOD__ );
				
		$fileName = $file->getTitle()->getDbKey();
				
		// Check if we need to update the transcode state:
		if( !self::$transcodeStateCache || !isset( self::$transcodeStateCache[ $fileName ] ) ) {
			self::getTranscodeStateCache( $fileName );
		}
		
		// Check if the job has been added: 
		if( ! isset( self::$transcodeStateCache[$fileName][$transcodeKey] ) 
				||
			is_null( self::$transcodeStateCache[$fileName][$transcodeKey]['addjob'] ) )
		{
			// add to job queue and update the db
			$job = new WebVideoTranscodeJob( $file->getTitle(), array(
				'transcodeMode' => 'derivative',
				'transcodeKey' => $transcodeKey,
			) );					
			$jobId = $job->insert();
			if( $jobId ){				
				$db = wfGetDB( DB_MASTER );
				// update the transcode state: 
				if( ! isset( self::$transcodeStateCache[$fileName][$transcodeKey] ) ){
					// insert the transcode row with jobadd time					
					$db->insert(
						'transcode', 
						array(
							'transcode_image_name' => $fileName,
							'transcode_key' => $transcodeKey,
							'transcode_time_addjob' => $db->timestamp()
						),
						__METHOD__
					);	
				} else {
					// update job start time
					$db->update(
						'transcode', 
						array(
							'transcode_time_addjob' => $db->timestamp()
						),
						array(
							'transcode_image_name' => $fileName,
							'transcode_key' => $transcodeKey,
						),
						__METHOD__
					);
				}
				// Update the state cache   
				self::getTranscodeStateCache( $fileName );
			}
			// no jobId ? error out in some way? 
		}
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * Transforms the size per a given "maxSize" 
	 *  if maxSize is > file, file size is used
	 */
	public static function getMaxSizeTransform( &$file, $targetMaxSize ){		
		$sourceWidth = $file->getWidth();
		$sourceHeight = $file->getHeight();
		if( WebVideoTranscode::isTargetLargerThanFile( $file, $targetMaxSize) ){
			return array(
				$sourceWidth,
				$sourceHeight
			);
		}
		// Get the aspect ratio percentage
		$ar = intval( $sourceWidth ) / intval( $sourceHeight );
		if ( $sourceWidth > $targetMaxSize ) {
			return array(
				intval( $targetMaxSize ),
				intval( $targetMaxSize / $ar)
			);
		} else {
			return array(
				intval( $targetMaxSize ),
				intval( $targetMaxSize / $ar)
			);
      	}
	}
	/**
	 * Test if a given transcode target is larger than the source file
	 * 
	 * @param $transcodeKey The static transcode key
	 * @param $file {Object} File object
	 */
	public static function isTargetLargerThanFile( &$file, $targetMaxSize ){
		$largerSize = ( $file->getWidth() > $file->getHeight() )?$file->getWidth(): $file->getHeight();		
		return ( $targetMaxSize > $largerSize );
	}
}

