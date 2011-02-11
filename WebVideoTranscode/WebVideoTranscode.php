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
	* Msg keys for derivatives are the profile constant with underscores instead of . 
	* 200_200kbs.ogv look up would be: 
	* $messages['timedmedia-derivative-200_200kbs_ogv'] => 'Ogg 200';
	*/
	
	// Ogg Profiles
	const ENC_OGV_2MBS = '200_200kbs.ogv';
	const ENC_OGV_4MBS = '360_400kbs.ogv';
	const ENC_OGV_6MBS = '480_600kbs.ogv';
	const ENC_OGV_HQ_VBR = '720_VBR.ogv';
	
	// WebM profiles: 	
	const ENC_WEBM_6MBS = '480_600kbs.webm';
	const ENC_WEBM_HQ_VBR = '720_VBR.webm';

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
				'maxSize'			=> '200',
				'videoBitrate'		=> '128',
				'audioBitrate'		=> '32',
				'samplerate'		=> '22050',
				'framerate'			=> '15',
				'channels'			=> '1',
				'noUpscaling'		=> 'true',
				'twopass' 			=> 'true',
				'keyframeInterval'	=> '64',
				'bufDelay'			=> '128',
				'codec' 			=> 'theora',
			),
	   WebVideoTranscode::ENC_OGV_4MBS =>
			array(
				'maxSize'			=> '360',
				'videoBitrate'		=> '368',
				'audioBitrate'		=> '48',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'codec' 			=> 'theora',
			),
		WebVideoTranscode::ENC_OGV_6MBS =>
			array(
				'maxSize'			=> '480',
				'videoBitrate'		=> '512',
				'audioBitrate'		=> '96',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'codec' 			=> 'theora',
			),
			
		// WebM transcode:
		WebVideoTranscode::ENC_WEBM_6MBS =>
			array(
			 	'maxSize'			=> '512',
				'videoBitrate'		=> '512',
				'audioBitrate'		=> '96',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256',
				'codec' 			=> 'vp8',
			),
		WebVideoTranscode::ENC_WEBM_HQ_VBR =>
			 array(
				'maxSize'			=> '720',
				'videoQuality'		=> 7,
				'audioQuality'		=> 3,
				'noUpscaling'		=> 'true',
				'codec' 			=> 'vp8',
			)
	);	
	static public function getDerivativeFilePath($file, $transcodeKey){
		return dirname( 
					$file->getThumbPath(
						$file->thumbName( array() )
					)
				) . '/' . 
				$file->getName() . '.' .
				$transcodeKey ;
	}
	static public function getTargetEncodePath( $file, $transcodeKey ){
		return self::getDerivativeFilePath( $file, $transcodeKey ) . '.tmp';
	}
	
	/** 
	 * Static function to get the set of video assets 
	 * 
	 * Based on the $wgEnabledTranscodeSet set of enabled derivatives 
	 * 
	 * In progress assets have .tmp extension and we don't add jobQueue for them. 
	 * 	This assumes "cheap" stat calls and "costly" jobQueue sql queries
	 * 	
	 * If no transcode is in progress or ready add the job to the jobQueue
	 * 
	 * @param {Object} File object
	 * @returns an associative array of sources suitable for <source> tag output
	 */
	static public function getSources( $file ){
		global $wgEnabledTranscodeSet;
		$sources = array();
		
		// Setup local variables 
		$fileName = $file->getName();
		
		// Add the source file: 
		$sources[] = array(
			'src' => $file->getUrl()
		);
		
		$thumbName = $file->thumbName( array() );		
		$thumbUrl = $file->getThumbUrl( $thumbName );
		$thumbUrlDir = dirname( $thumbUrl );		
		
		$hasOggFlag = false;
		$hasWebMFlag = false;
		// Check the source file for .webm extension 
		if( preg_match( "/$.webm/i", $fileName ) ) {
			$hasWebMFlag = true;
		} else {
			// we only support ogg and webm so assume oky if we have .webm
			$hasOggFlag = true;
		}
		
		foreach($wgEnabledTranscodeSet as $transcodeKey){
			$derivativeFile = self::getDerivativeFilePath( $file, $transcodeKey);
			if( is_file( $derivativeFile ) ){
				$sources[] = array(
					'src' => $thumbUrlDir . '/' .$fileName . '.' . $transcodeKey
				);
			} else {
				// Skip if transcode is smaller than source 
				// And we have at least one ogg and one WebM encode for this source
				$codec = self::$derivativeSettings[$transcodeKey]['codec'];
				if( ! self::isTranscodeSmallerThanSource( $file,  $transcodeKey )
					&& 
					// Check if we need to process the $transcodeKey because we don't
					// have an ogg or webm source yet:
					! ( 
						( !$hasOggFlag && $codec == 'theora' )
						|| 
						( !$hasWebMFlag && $codec == 'vp8' )
					)
				){							
					continue;
				}
				// TranscodeKey not found ( check if the file is in progress ) ( tmp transcode location ) 
				//if( is_file( self::getEncodeTargetFilePath( $file, $transcodeKey ) ){
					// file in progress / in queue
					// XXX Note we could check date and flag as failure somewhere
				//} else {
					// no in-progress file add to job queue and touch the target
					$job = new WebVideoTranscodeJob( $file->getTitle(), array(
						'transcodeMode' => 'derivative',
						'transcodeKey' => $transcodeKey,
					) );					
					$jobId = $job->insert();
					if( $jobId ){
						// If the job was inserted: 
						touch( self::getTargetEncodePath( $file, $transcodeKey ) ); 
					}
				//}
			}
		}
		return $sources;
	}
	/**
	 * Test if a given transcode target is smaller than the source file
	 * 
	 * @param $transcodeKey The static transcode key
	 * @param $file {Object} File object
	 */
	public static function isTranscodeSmallerThanSource( $file, $transcodeKey){
		return ( self::$derivativeSettings[$transcodeKey]['maxSize'] < $file->getWidth() 
					&&
				self::$derivativeSettings[$transcodeKey]['maxSize'] < $file->getHeight()
			);
	}
}

