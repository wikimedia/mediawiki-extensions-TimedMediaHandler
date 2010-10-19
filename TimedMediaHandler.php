<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the TimedMediaHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}
// Set up the timed media handler dir: 
$timedMediaDir = dirname(__FILE__);
$wgAutoloadClasses['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler_body.php";

$wgMediaHandlers['application/ogg'] = 'TimedMediaHandler';
if ( !in_array( 'ogg', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogg';
}
if ( !in_array( 'ogv', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'ogv';
}
if ( !in_array( 'oga', $wgFileExtensions ) ) {
	$wgFileExtensions[] = 'oga';
}
ini_set( 'include_path',
	"$timedMediaDir/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );


$wgExtensionMessagesFiles['TimedMediaHandler'] = "$timedMediaDir/TimedMediaHandler.i18n.php";
$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] = "$timedMediaDir/TimedMediaHandler.i18n.magic.php";
$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );

// Load all the mwEmbed modules: 
MwEmbedResourceManager::registerModulePath( 'extensions/TimedMediaHandler/EmbedPlayer' );
MwEmbedResourceManager::registerModulePath( 'extensions/TimedMediaHandler/TimedText' );


// Setup a hook for iframe=true (will strip the interface and only output the player)
$wgHooks['ArticleFromTitle'][] = 'TimedMediaHandler::iframeOutputHook';

// AutoLoad Classes:
$wgAutoloadClasses['WebVideoTranscode'] = "$timedMediaDir/WebVideoTranscode/WebVideoTranscode.php";
$wgAutoloadClasses['TimedMediaHandlerHooks'] = "$timedMediaDir/TimedMediaHandler.hooks.php";

$wgHooks['LoadExtensionSchemaUpdates'][] = 'WebVideoTranscode::schema';

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'TimedMediaHandler',
	'author'         => array( 'Michael Dale', 'Tim Starling' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:TimedMediaHandler',
	'descriptionmsg' => 'timedmedia-desc',
);



/******************* CONFIGURATION STARTS HERE **********************/

// Set the supported ogg codecs:
$wgOggVideoTypes = array( 'Theora' );
$wgOggAudioTypes = array( 'Vorbis', 'Speex', 'FLAC' );

// Default skin for mwEmbed player
// Skins presently available:
// 	"kskin" kaltura skin
// 	"mvpcf" a jquery ui like skin
$wgVideoPlayerSkin = 'kskin';

// Support striped player iframe output for remote embedding
$wgEnableIframeEmbed = false;

// Inline timedText reference url output
if( ! isset( $wgEnableTimedText ) ){
	$wgEnableTimedText = false;
}

// Location of oggThumb binary ( used instead of ffmpeg )
$wgOggThumbLocation = '/usr/bin/oggThumb';

// The location of ffmpeg2theora ( for metadata and transcoding )
$wgffmpeg2theoraPath = '/usr/bin/ffmpeg2theora';

// Location of the FFmpeg binary ( used to encode WebM and for thumbnails ) 
$wgFFmpegLocation = '/usr/bin/ffmpeg';


// Enabled derivatives array
// If set to false no derivatives will be used
//
// Only derivatives with less width than the
// source asset size will be created
//
// Derivative jobs are added to the mediaWiki JobQueue the first time the asset is displayed
//
// Derivative keys encode settings are defined in WebVideoTranscode.php
//
if( !isset( $wgEnabledTranscodeSet )){
	$wgEnabledTranscodeSet = array(
	
		// Cover accessibility for low bandwidth / not running most up-to-date browsers environments: 
		WebVideoTranscode::ENC_OGV_2MBS,
		
		// A standard web streamable ogg video 
		WebVideoTranscode::ENC_OGV_6MBS,
		
		// A standard web streamable WebM video	
		WebVideoTranscode::ENC_WEBM_6MBS,	
		
		// A high quality WebM stream 
		WebVideoTranscode::ENC_WEBM_HQ_VBR,
		
		// If the source asset is in a free format it will also be made available to the players
	);
}


/******************* CONFIGURATION ENDS HERE **********************/

// NOTE: normally configuration based code would go into extension setup function
// These configuration variables setup hooks and autoloaders that need to happen at
// initial config time


// Enable timed text
if( $wgEnableTimedText ){
	/**
	 * Handle Adding of "timedText" NameSpace
	 */
	$wgTimedTextNS = null;

	// Make sure $wgExtraNamespaces in an array (set to NULL by default) :
	if ( !is_array( $wgExtraNamespaces ) ) {
		$wgExtraNamespaces = array();
	}
	// Check for "TimedText" NS
	$maxNS = 101; // content pages need "even" namespaces
	foreach($wgExtraNamespaces as $ns => $nsTitle ){
		if( $nsTitle == 'TimedText' ){
			$wgTimedTextNS = $ns;
		}
		if( $ns > $maxNS ){
			$maxNs = $ns;
		}
	}
	// If not found add Add a custom timedText NS
	if( !$wgTimedTextNS ){
		$wgTimedTextNS = ( $maxNS + 1 );
		$wgExtraNamespaces[	$wgTimedTextNS ] = 'TimedText';
		$wgExtraNamespaces[ $wgTimedTextNS +1 ] =  'TimedText_talk';
	}
	define( "NS_TIMEDTEXT", $wgTimedTextNS);
	// Assume $wgTimedTextNS +1 for talk
	define( "NS_TIMEDTEXT_TALK", $wgTimedTextNS +1);

} // end of handling timedText
