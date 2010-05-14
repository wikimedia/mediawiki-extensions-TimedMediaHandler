<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the TimedMediaHandler extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

$oggDir = dirname(__FILE__);
$wgAutoloadClasses['TimedMediaHandler'] = "$oggDir/TimedMediaHandler_body.php";

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
	"$oggDir/PEAR/File_Ogg" .
	PATH_SEPARATOR .
	ini_get( 'include_path' ) );

// Bump this when updating OggPlayer.js to help update caches
$wgOggScriptVersion = '11';

$wgExtensionMessagesFiles['TimedMediaHandler'] = "$oggDir/TimedMediaHandler.i18n.php";
$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] = "$oggDir/TimedMediaHandler.i18n.magic.php";
$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );


// Setup a hook for iframe=true (will strip the interface and only output the player)
$wgHooks['ArticleFromTitle'][] = 'TimedMediaHandler::iframeOutputHook';

// OggTranscode setup
$wgAutoloadClasses['OggTranscode'] = "$oggDir/OggTranscode/OggTranscode.php";
$wgHooks['LoadExtensionSchemaUpdates'][] = 'OggTranscode::schema';

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'TimedMediaHandler',
	'author'         => array( 'Tim Starling', 'Michael Dale' ),
	'url'            => 'http://www.mediawiki.org/wiki/Extension:TimedMediaHandler',
	'description'    => 'Handler for Timed Media (video, audio, timedText) with transcoding to Ogg Theora/Vorbis',
	'descriptionmsg' => 'timedmedia-desc',
);


// Add the javascript loader for "EmbedPlayer module"
$wgExtensionJavascriptModules[ 'EmbedPlayer' ] = "extensions/TimedMediaHandler/EmbedPlayer";

// Add the javascript loader for "TimedText module"
$wgExtensionJavascriptModules[ 'TimedText' ] = "extensions/TimedMediaHandler/TimedText";

/******************* CONFIGURATION STARTS HERE **********************/

// Set the supported ogg codecs:
$wgOggVideoTypes = array( 'Theora' );
$wgOggAudioTypes = array( 'Vorbis', 'Speex', 'FLAC' );

// Defautl skin for mwEmbed player
// Skins presently available:
// 	"kskin" kaltura skin
// 	"mvpcf" a jquery ui like skin
$wgVideoPlayerSkin = 'kskin';

// Support striped player iframe output for remote embedding
$wgEnableIframeEmbed = false;

// Inline timedText reference url output
$wgEnableTimedText = false;

// Location of oggThumb binary ( used instead of ffmpeg )
$wgOggThumbLocation = '/usr/bin/oggThumb';

// The location of ffmpeg2theora ( for metadata and transcoding )
$wgffmpeg2theoraPath = '/usr/bin/ffmpeg2theora';

// Location of the FFmpeg binary
$wgFFmpegLocation = '/usr/bin/ffmpeg';

/**
 * enable oggz_chop support
 * if enabled the mwEmbed player will use temporal urls
 * for helping with seeking with some plugin types
 */
$wgEnableTemporalOggUrls = false;

// Enabled derivatives array
// If set to false no derivatives will be used
//
// Only derivatives with less width than the
// source asset size will be created
//
// Derivatives can be created by running OggTranscodeCron.php
// at regular intervals. The cron job
// cycles through every ogg file and encodes the following derivative set:
//
// Derivative keys encode settings are defined in OggTranscode.php
//
$wgEnabledDerivatives = array(
	OggTranscode::ENC_WEB_2MBS,
	OggTranscode::ENC_WEB_4MBS,
	OggTranscode::ENC_WEB_6MBS,
	OggTranscode::ENC_HQ_VBR
);

// If play requests should be tracked.
$wgEnablePlayTracking = true;

// One out of how many requests should be tracked:
$wgPlayTrackingRate = 10;


// Filename or URL path to the Cortado Java player applet.
//
// If no path is included, the path to this extension's
// directory will be used by default -- this should work
// on most local installations.
//
// You may need to include a full URL here if $wgUploadPath
// specifies a host different from where the wiki pages are
// served -- the applet .jar file must come from the same host
// as the uploaded media files or Java security rules will
// prevent the applet from loading them.
//
$wgCortadoJarFile = "cortado-ovt-stripped-0.5.1.jar";

/******************* CONFIGURATION ENDS HERE **********************/

// NOTE: normally configuration based code would go into extension setup function
// This config setups hooks and autoloaders that should happen at
// initial config time

// Alternatively we could have top level php files that include the
// following pieces of code.

// Enable play tracking
if( $wgEnablePlayTracking ){
	// Add the Api Play Tracking setup
	$wgAutoloadClasses['ApiPlayTracking'] = "$oggDir/ApiPlayTracking/ApiPlayTracking.php";
	$wgHooks['LoadExtensionSchemaUpdates'][] =  'ApiPlayTracking::schema';

	//Add the api entry point:
	$wgAPIModules[  'playtracking' ] = 'ApiPlayTracking';
}

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
