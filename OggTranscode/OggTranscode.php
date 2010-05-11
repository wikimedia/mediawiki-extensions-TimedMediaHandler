<?php

/*
 * OggTranscode provides:
 *  encode keys
 *  encode settings
 *  db schema
 *  and getWidthDerivativeURL function to grab url for a derivative
 */


/*
 * Main OggTranscode Class hold some constants and config values
 */
class OggTranscode {
	/**
	* Key constants for the derivatives,
	* this key is appended to the derivative file name
	*
	* If you update the wgDerivativeSettings for one of these keys
	* and want to re-generate the video you should also update the
	* key constant.
	*/
	const ENC_WEB_2MBS = '200_200kbs';
	const ENC_WEB_4MBS = '360_400kbs';
	const ENC_WEB_6MBS = '480_600kbs';
	const ENC_HQ_VBR = '720_VBR';

	/**
	* Encoding parameters are set via firefogg encode api
	*
	* For clarity and compatibility with passing down
	* client side encode settings at point of upload
	*
	* http://firefogg.org/dev/index.html
	*/
	public static $derivativeSettings = array(
	OggTranscode::ENC_WEB_2MBS =>
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
   OggTranscode::ENC_WEB_4MBS =>
		array(
			'maxSize'			=> '360',
			'videoBitrate'		=> '368',
			'audioBitrate'		=> '48',
			'noUpscaling'		=> 'true',
			'twopass'			=> 'true',
			'keyframeInterval'	=> '128',
			'bufDelay'			=> '256'
		),
	OggTranscode::ENC_WEB_6MBS =>
		array(
			'maxSize'			=> '480',
			'videoBitrate'		=> '512',
			'audioBitrate'		=> '96',
			'noUpscaling'		=> 'true',
			'twopass'			=> 'true',
			'keyframeInterval'	=> '128',
			'bufDelay'			=> '256'
		),
	OggTranscode::ENC_HQ_VBR =>
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
	 * This lets us share a common api between firefogg and oggTranscode
	 * also see: http://firefogg.org/dev/index.html
	 */
	 public static $foggMap = array(
		//video
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
		 //audio
		'audioQuality'	=> "-a",
		'audioBitrate'	=> "-A",
		'samplerate'	=> "-H",
		'channels'		=> "-c",
		'noaudio'		=> "--noaudio",
		 //metadata
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
	* Setup the OggTranscode tables
	*/
	public static function schema() {
		global $wgExtNewTables, $wgExtNewIndexes;

		$wgExtNewTables[] = array(
			'transcode_job',
			dirname( __FILE__ ) . '/OggTranscode.sql'
		);

		$wgExtNewIndexes[] = array(
			'image', 'image_media_type',
			dirname( __FILE__ ) . '/patch-image-index.sql'
		);

		return true;
	}

	/**
	* Get derivative url for given embed width
	*
	* @param {Object} $srcFile Source file object
	* @param {Number} $targetWidth Target output width
	* @return {String}
	* 	the url for a given embed width,
	* 	or the srcFile url if no derivative found
	*/
	static function getWidthDerivativeURL(  $srcFile, $targetWidth){
		global $wgEnabledDerivatives;
		$srcWidth = $srcFile->getWidth();
		$srcHeight = $srcFile->getHeight();

		// Return source file  url if targetdWidth greater than our source file. )
		// should cover audio as well

		// NOTE removed: && get_class( $srcFile->handler) != 'NonFreeVideoHandler')
		if( $targetWidth >= $srcWidth ){
			$srcFile->getURL();
		}

		// Get the available derivatives by difference of 'maxSize' to 'targetWidth'
		$derivativeSet = array();

		// include the source asset in the $derivativeSet distance comparison
		$derivativeSet[ 'SOURCE' ] =  abs( $targetWidth - $srcWidth );

		foreach( $wgEnabledDerivatives as $derivativeKey ){
			if( isset( self::$derivativeSettings[ $derivativeKey ] ) ){
				$derivativePath = $srcFile->getThumbPath( $derivativeKey );
				// Check for derivative file:
				if( is_file ( "{$derivativePath}.ogv" )){
					$maxSize =  self::$derivativeSettings[ $derivativeKey ]['maxSize'];
					$derivativeSet[ $derivativeKey ] =  abs( $targetWidth - $maxSize );
				}
			}
		}

		// No derivative found return the src url
		if( count( $derivativeSet ) == 0 ){
			return $srcFile->getURL();
		}
		// Sort the $derivativeSet
		asort( $derivativeSet );

		// Reset the pointer to start:
		reset( $derivativeSet );

		// Handle special case where source is closes to target:
		if( key( $derivativeSet ) == 'SOURCE' ){
			return $srcFile->getURL();
		}

		// Get the url for the closest derivative
		return $srcFile->getThumbUrl( key( $derivativeSet ) . '.ogv');
	 }
}



?>