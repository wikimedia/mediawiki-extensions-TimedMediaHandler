<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the TimedMediaHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

// Set up the timed media handler dir: 
$timedMediaDir = dirname(__FILE__);

if ( !in_array( 'ogg', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogg';
}
if ( !in_array( 'ogv', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogv';
}
if ( !in_array( 'oga', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'oga';
}
if ( !in_array( 'webm', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'webm';
}




// Timed Media Handler AutoLoad Classes:  
$wgAutoloadClasses['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler_body.php";
$wgAutoloadClasses['TimedMediaHandlerHooks'] = "$timedMediaDir/TimedMediaHandler.hooks.php";
$wgAutoloadClasses['TimedMediaTransformOutput'] = "$timedMediaDir/TimedMediaTransformOutput.php";
$wgAutoloadClasses['TimedMediaIframeOutput'] = "$timedMediaDir/TimedMediaIframeOutput.php";
$wgAutoloadClasses['TimedMediaThumbnail'] = "$timedMediaDir/TimedMediaThumbnail.php";

// Ogg Handler
$wgAutoloadClasses['OggHandler']  = "$timedMediaDir/OggHandler/OggHandler.php";
ini_set( 'include_path',
	"$timedMediaDir/OggHandler/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );

// WebM Handler
$wgAutoloadClasses['WebMHandler'] = "$timedMediaDir/WebMHandler/WebMHandler.php";
$wgAutoloadClasses['getID3' ] = "$timedMediaDir/WebMHandler/getid3.php"; 

$wgAutoloadClasses['WebVideoTranscode'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscode.php";
$wgAutoloadClasses['WebVideoTranscodeJob'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscodeJob.php";

// Register the Timed Media Handler javascript resources ( mwEmbed modules )  
MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/resources/EmbedPlayer' );
MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/resources/TimedText' );

// Localization 
$wgExtensionMessagesFiles['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler.i18n.php";
$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] = "$timedMediaDir/TimedMediaHandler.i18n.magic.php";

// Setup globals

/**
 * Setup a metadata cache :(
 * 
 * Its very costly to generate metadata! I am not sure who or why the file repos don't get
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

// Set the supported ogg codecs:
$wgMediaVideoTypes = array( 'Theora', 'Vp8' );
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
 * -If set to false no derivatives will be used
 * -These transcodes are *in addition to* the source file. 
 * -Only derivatives with smaller width than the source asset size will be created
 * -Derivative jobs are added to the mediaWiki JobQueue the first time the asset is displayed
 * -Derivative keys encode settings are defined in WebVideoTranscode.php
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

