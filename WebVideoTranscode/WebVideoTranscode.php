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
		WebVideoTranscode::ENC_WEB_2MBS =>
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
				'bufDelay'			=> '128'
			),
	   WebVideoTranscode::ENC_OGV_4MBS =>
			array(
				'maxSize'			=> '360',
				'videoBitrate'		=> '368',
				'audioBitrate'		=> '48',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256'
			),
		WebVideoTranscode::ENC_OGV_6MBS =>
			array(
				'maxSize'			=> '480',
				'videoBitrate'		=> '512',
				'audioBitrate'		=> '96',
				'noUpscaling'		=> 'true',
				'twopass'			=> 'true',
				'keyframeInterval'	=> '128',
				'bufDelay'			=> '256'
			),
			
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
		WebVideoTranscode::ENC_OGV_HQ_VBR =>
			 array(
				'maxSize'			=> '720',
				'videoQuality'		=> 7,
				'audioQuality'		=> 3,
				'noUpscaling'		=> 'true'
			)
		);
	 /**
	 * Mapping between firefogg api and ffmpeg2theora command line
	 *
	 * This lets us share a common api between firefogg and WebVideoTranscode
	 * also see: http://firefogg.org/dev/index.html
	 */
	 public static $foggMap = array(
		// video
		'width'			=> "--width",
		'height'		=> "--height",
		'maxSize'		=> "--max_size",
		'noUpscaling'	=> "--no-upscaling",
		'videoQuality'=> "-v",
		'videoBitrate'	=> "-V",
		'twopass'		=> "--two-pass",
		'framerate'		=> "-F",
		'aspect'		=> "--aspect",
		'starttime'		=> "--starttime",
		'endtime'		=> "--endtime",
		'cropTop'		=> "--croptop",
		'cropBottom'	=> "--cropbottom",
		'cropLeft'		=> "--cropleft",
		'cropRight'		=> "--cropright",
		'keyframeInterval'=> "--key",
		'denoise'		=> array("--pp", "de"),
		'novideo'		=> array("--novideo", "--no-skeleton"),
		'bufDelay'		=> "--buf-delay",
		 // audio
		'audioQuality'	=> "-a",
		'audioBitrate'	=> "-A",
		'samplerate'	=> "-H",
		'channels'		=> "-c",
		'noaudio'		=> "--noaudio",
		 // metadata
		'artist'		=> "--artist",
		'title'			=> "--title",
		'date'			=> "--date",
		'location'		=> "--location",
		'organization'	=> "--organization",
		'copyright'		=> "--copyright",
		'license'		=> "--license",
		'contact'		=> "--contact"
	);
	
	/** 
	 * Static function to get the set of video assets 
	 * 
	 * Based on the $wgEnabledTranscodeSet set of enabled derivatives 
	 * 
	 * In progress assets have .tmp extension and we don't add jobQueue for them. 
	 * 	This lets us do relatively cheap stat calls and avoid costly jobQueue sql queries
	 * 	
	 * If no transcode is in progress or ready add the job to the jobQueue
	 * 
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
		$thumbPath = $file->getThumbPath( $thumbName );
		$thumbDir = dirname( $thumbPath );
		$thumbUrl = $file->getThumbUrl( $thumbName );
		$thumbUrlDir = dirname( $thumbUrl );
				
		foreach($wgEnabledTranscodeSet as $transcodeKey){
			$derivativeFile = $thumbPath . '/' . $fileName . '.' . $transcodeKey ;
			if( is_file( $derivativeFile ) ){
				$sources[] = array( 
					'src' => $thumbUrlDir . '/' .$fileName . '.' . $transcodeKey
				);
			} else {
				// TranscodeKey not found ( check if the file is in progress ) ( tmp transcode location ) 
				if( is_file( $derivativeFile . '.tmp' ) ){
					// file in progress / in queue
					// XXX Note we could check date and flag as failure somewhere
				} else {
					// no in-progress file add to job queue and touch the target
					//touch( $derivativeFile . '.tmp' ); 
				}
			}
		}
		return $sources;
	}
}

