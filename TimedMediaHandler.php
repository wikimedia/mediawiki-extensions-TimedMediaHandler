<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the TimedMediaHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

if( !class_exists( 'MwEmbedResourceManager' ) ){
	echo "TimedMediaHandler requires the MwEmbedSupport extension.\n";
	exit( 1 );
}

// Set up the timed media handler dir:
$timedMediaDir = dirname(__FILE__);
// Include WebVideoTranscode ( prior to config so that its defined transcode keys can be used in configuration  )  
$wgAutoloadClasses['WebVideoTranscode'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscode.php";


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

// The location of ffmpeg2theora ( transcoding )
$wgFFmpeg2theoraLocation = '/usr/bin/ffmpeg2theora';

// Location of the FFmpeg binary ( used to encode WebM and for thumbnails ) 
$wgFFmpegLocation = '/usr/bin/ffmpeg';

// The NS for TimedText ( registered on mediawiki.org )
// http://www.mediawiki.org/wiki/Extension_namespace_registration
// Note commons pre-dates TimedMediaHandler and should set $wgTimedTextNS = 102 in localSettings.php
$wgTimedTextNS = 700;

/** 
 * Default enabled transcodes 
 * 
 * -If set to empty array, no derivatives will be created
 * -Derivative keys encode settings are defined in WebVideoTranscode.php
 * 
 * -These transcodes are *in addition to* the source file. 
 * -Only derivatives with smaller width than the source asset size will be created
 * -Regardless of source size at least one WebM and Ogg source will be created from the $wgEnabledTranscodeSet 
 * -Derivative jobs are added to the MediaWiki JobQueue the first time the asset is displayed
 * -Derivative should be listed min to max
 */
$wgEnabledTranscodeSet = array(
	// Cover accessibility for low bandwidth / low resources clients: 
	WebVideoTranscode::ENC_OGV_2MBS,
	
	// A high end web streamable ogg video 
	WebVideoTranscode::ENC_OGV_9MBS,
	
	// High quality 720P ogg video: 
	WebVideoTranscode::ENC_OGV_HQ_VBR,
	
	// A web streamable WebM video	
	WebVideoTranscode::ENC_WEBM_9MBS,
	
	// A high quality WebM stream 
	WebVideoTranscode::ENC_WEBM_HQ_VBR,
);
/******************* CONFIGURATION ENDS HERE **********************/



// List of extensions handled by Timed Media Handler since its referenced in a few places. 
// you should not modify this variable 
$tmhFileExtensions = array( 'ogg', 'ogv', 'oga', 'webm');

$wgFileExtensions = array_merge($wgFileExtensions, $tmhFileExtensions);

// Timed Media Handler AutoLoad Classes:  
$wgAutoloadClasses['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler_body.php";
$wgAutoloadClasses['TimedMediaHandlerHooks'] = "$timedMediaDir/TimedMediaHandler.hooks.php";
$wgAutoloadClasses['TimedMediaTransformOutput'] = "$timedMediaDir/TimedMediaTransformOutput.php";
$wgAutoloadClasses['TimedMediaIframeOutput'] = "$timedMediaDir/TimedMediaIframeOutput.php";
$wgAutoloadClasses['TimedMediaThumbnail'] = "$timedMediaDir/TimedMediaThumbnail.php";
// Testing: 
$wgAutoloadClasses['ApiTestCaseVideoUpload'] = "$timedMediaDir/tests/phpunit/ApiTestCaseVideoUpload.php";

// Ogg Handler
$wgAutoloadClasses['OggHandler']  = "$timedMediaDir/handlers/OggHandler/OggHandler.php";
ini_set( 'include_path',
	"$timedMediaDir/handlers/OggHandler/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );

// WebM Handler
$wgAutoloadClasses['WebMHandler'] = "$timedMediaDir/handlers/WebMHandler/WebMHandler.php";
$wgAutoloadClasses['getID3' ] = "$timedMediaDir/handlers/WebMHandler/getid3/getid3.php"; 

// Text handler 
$wgAutoloadClasses['TextHandler'] = "$timedMediaDir/handlers/TextHandler/TextHandler.php";
$wgAutoloadClasses['TimedTextPage'] = "$timedMediaDir/TimedTextPage.php";

// Transcode support
$wgAutoloadClasses['WebVideoTranscodeJob'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscodeJob.php";
$wgAutoloadClasses['ApiQueryVideoInfo'] = "$timedMediaDir/ApiQueryVideoInfo.php";

// Localization 
$wgExtensionMessagesFiles['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler.i18n.php";
$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] = "$timedMediaDir/TimedMediaHandler.i18n.magic.php";

// Register all Timed Media Handler hooks right after the cache check.
// This way if you set a variable like $wgTimedTextNS in LocalSettings.php after you include TimedMediaHandler  
// we can still read the variable values   
$wgHooks['SetupAfterCache'][] = 'TimedMediaHandlerHooks::register';

// Extension Credits
$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'TimedMediaHandler',
	'author'         => array( 'Michael Dale', 'Tim Starling' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:TimedMediaHandler',
	'descriptionmsg' => 'timedmedia-desc',
	'version'		 => '0.2',
);



