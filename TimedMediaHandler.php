<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the TimedMediaHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

// Set up the timed media handler dir: 
$timedMediaDir = dirname(__FILE__);

$wgTimedMediaHandlerFileExtensions = array( 'ogg', 'ogv', 'oga', 'webm');

foreach($wgTimedMediaHandlerFileExtensions as $ext ){
	if ( !in_array( $ext, $wgFileExtensions ) ) {
		$wgFileExtensions[] = $ext;
	}
}

// Timed Media Handler AutoLoad Classes:  
$wgAutoloadClasses['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler_body.php";
$wgAutoloadClasses['TimedMediaHandlerHooks'] = "$timedMediaDir/TimedMediaHandler.hooks.php";
$wgAutoloadClasses['TimedMediaTransformOutput'] = "$timedMediaDir/TimedMediaTransformOutput.php";
$wgAutoloadClasses['TimedMediaIframeOutput'] = "$timedMediaDir/TimedMediaIframeOutput.php";
$wgAutoloadClasses['TimedMediaThumbnail'] = "$timedMediaDir/TimedMediaThumbnail.php";

// Ogg Handler
$wgAutoloadClasses['OggHandler']  = "$timedMediaDir/handlers/OggHandler/OggHandler.php";
ini_set( 'include_path',
	"$timedMediaDir/handlers/OggHandler/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );

// WebM Handler
$wgAutoloadClasses['WebMHandler'] = "$timedMediaDir/handlers/WebMHandler/WebMHandler.php";
$wgAutoloadClasses['getID3' ] = "$timedMediaDir/handlers/WebMHandler/getid3/getid3.php"; 

$wgAutoloadClasses['WebVideoTranscode'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscode.php";
$wgAutoloadClasses['WebVideoTranscodeJob'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscodeJob.php";

// Register the Timed Media Handler javascript resources ( MwEmbed modules ) 
MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/EmbedPlayer' );
MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/TimedText' );

// Localization 
$wgExtensionMessagesFiles['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler.i18n.php";
$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] = "$timedMediaDir/TimedMediaHandler.i18n.magic.php";


/**
 * Setup a metadata cache :(
 * 
 * Its very costly to generate metadata! I am not sure why the file repos don't get
 * instantiated with a path, and then could lazy init things like other normal objects and 
 * have a local cache of their metadata! 
 */ 
$wgMediaHandlerMetadataCache = array();

// Register all Timed Media Handler hooks: 
TimedMediaHandlerHooks::register();

// Extension Credits
$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'TimedMediaHandler',
	'author'         => array( 'Michael Dale', 'Tim Starling' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:TimedMediaHandler',
	'descriptionmsg' => 'timedmedia-desc',
);


/******************* CONFIGURATION STARTS HERE **********************/

/*** MwEmbed module configuration: *********************************/
// Show a warning to the user if they are not using an html5 browser with high quality ogg support
$wgMwEmbedModuleConfig['EmbedPlayer.DirectFileLinkWarning'] = true; 

// The text interface should always be shown 
// ( even if there are no text tracks for that asset at render time )
$wgMwEmbedModuleConfig['TimedText.ShowInterface'] = 'always';

/*** end MwEmbed module configuration: ******************************/

// The minimum size for an embed video player:
$wgMinimumVideoPlayerSize = 200;

// Set the supported ogg codecs:
$wgMediaVideoTypes = array( 'Theora', 'VP8' );
$wgMediaAudioTypes = array( 'Vorbis', 'Speex', 'FLAC' );

// Default skin for mwEmbed player ( class attribute of video tag ) 
$wgVideoPlayerSkin = 'kskin';

// Support iframe for remote embedding 
$wgEnableIframeEmbed = true;

// Location of oggThumb binary ( used instead of ffmpeg )
$wgOggThumbLocation = '/usr/bin/oggThumb';

// The location of ffmpeg2theora ( for metadata and transcoding )
$wgffmpeg2theoraLocation = '/usr/bin/ffmpeg2theora';

// Location of the FFmpeg binary ( used to encode WebM and for thumbnails ) 
$wgFFmpegLocation = '/usr/bin/ffmpeg';

/** 
 * Default enabled transcodes 
 * 
 * -If set to empty array, no derivatives will be created
 * -Derivative keys encode settings are defined in WebVideoTranscode.php
 * 
 * -These transcodes are *in addition to* the source file. 
 * -Only derivatives with smaller width than the source asset size will be created
 * -At least one WebM and Ogg source will be created from the $wgEnabledTranscodeSet
 * -Derivative jobs are added to the mediaWiki JobQueue the first time the asset is displayed
 * -List Derivative from min to max
 */
$wgEnabledTranscodeSet = array(
	// Cover accessibility for low bandwidth / low resources clients: 
	WebVideoTranscode::ENC_OGV_2MBS,
	
	// A standard web streamable ogg video 
	WebVideoTranscode::ENC_OGV_6MBS,
	
	// A standard web streamable WebM video	
	WebVideoTranscode::ENC_WEBM_6MBS,	
	
	// A high quality WebM stream 
	WebVideoTranscode::ENC_WEBM_HQ_VBR,
);

