<?php

/**
 * Hooks for TimedMediaHandler extension
 *
 * @file
 * @ingroup Extensions
 */

class TimedMediaHandlerHooks {
	// Register TimedMediaHandler Hooks
	static function register(){
		global $wgParserOutputHooks, $wgHooks, $wgJobClasses, $wgJobTypesExcludedFromDefaultQueue,
		$wgMediaHandlers, $wgResourceModules, $wgExcludeFromThumbnailPurge, $wgExtraNamespaces,
		$wgTmhFileExtensions, $wgParserOutputHooks, $wgOut, $wgAPIPropModules, $wgTimedTextNS;
		
		// Register the Timed Media Handler javascript resources ( MwEmbed modules ) 
		MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/EmbedPlayer' );
		MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/TimedText' );
		
		// Setup media Handlers:
		$wgMediaHandlers['application/ogg'] = 'OggHandler';
		$wgMediaHandlers['video/webm'] = 'WebMHandler';

		// Add transcode job class:
		$wgJobClasses+= array(
			'webVideoTranscode' => 'WebVideoTranscodeJob'
		);
		
		// Transcode jobs must be explicitly requested from the job queue:
		$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';

		$baseExtensionResource = array(
			'localBasePath' => dirname( __FILE__ ),
		 	'remoteExtPath' => 'TimedMediaHandler',
		);

		// Add the PopUpMediaTransform module ( specific to timedMedia handler ( no support in mwEmbed modules )
		$wgResourceModules+= array(
			'mw.PopUpMediaTransform' => array_merge( $baseExtensionResource, array(
				'scripts' => 'resources/mw.PopUpThumbVideo.js',
				'styles' => 'resources/PopUpThumbVideo.css',
				'dependencies' => array( 'jquery.ui.dialog' ),
			) ),
			'embedPlayerIframeStyle'=> array_merge( $baseExtensionResource, array(
				'styles' => 'resources/embedPlayerIframe.css',
			) ),
			'ext.tmh.transcodetable' => array_merge($baseExtensionResource, array(
				'scripts' => 'resources/ext.tmh.transcodetable.js',
				'styles' => 'resources/transcodeTable.css',
				'messages'=> array(
					'mwe-ok',
					'mwe-cancel',
					'timedmedia-reset-error',
					'timedmedia-reset',
					'timedmedia-reset-confirm'
				)
			) )
		);
		// Setup a hook for iframe embed handling:
		$wgHooks['ArticleFromTitle'][] = 'TimedMediaIframeOutput::iframeHook';
		
		// When an upload completes ( check clear any existing transcodes )
		$wgHooks['UploadComplete'][] = 'TimedMediaHandlerHooks::checkUploadComplete';

		// Add parser hook
		$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );
		
		// We should probably move this script output to a parser function but not working correctly in
		// dynamic contexts ( for example in special upload, when there is an "existing file" warning. )
		$wgHooks['BeforePageDisplay'][] = 'TimedMediaHandlerHooks::pageOutputHook';

		// Add a hook for article deletion so that we remove transcode settings / files. 
		$wgHooks['ArticleDeleteComplete'][] = 'TimedMediaHandlerHooks::checkArticleDeleteComplete';
		
		// Exclude transcoded assets from normal thumbnail purging
		// ( a maintenance script could handle transcode asset purging)
		$wgExcludeFromThumbnailPurge = array_merge( $wgExcludeFromThumbnailPurge, $wgTmhFileExtensions );

		// Also add the .log file ( used in two pass encoding )
		// ( probably should move in-progress encodes out of web accessible directory )
		$wgExcludeFromThumbnailPurge[] = 'log';
		
		$wgHooks['LoadExtensionSchemaUpdates'][] = 'TimedMediaHandlerHooks::loadExtensionSchemaUpdates';
		
		// Add unit tests
		$wgHooks['UnitTestsList'][] = 'TimedMediaHandlerHooks::registerUnitTests';
		
		/**
		 * Add support for the "TimedText" NameSpace
		 */
		define( "NS_TIMEDTEXT", $wgTimedTextNS );
		define( "NS_TIMEDTEXT_TALK", $wgTimedTextNS +1 );

		$wgExtraNamespaces[NS_TIMEDTEXT] = "TimedText";
		$wgExtraNamespaces[NS_TIMEDTEXT_TALK] = "TimedText_talk";

		// Check for timed text page:
		$wgHooks[ 'ArticleFromTitle' ][] = 'TimedMediaHandlerHooks::checkForTimedTextPage';
		
		// Add transcode status to video asset pages:
		$wgHooks[ 'ImagePageAfterImageLinks' ][] = 'TimedMediaHandlerHooks::checkForTranscodeStatus';
		
		return true;
	}
	
	public static function checkForTimedTextPage( &$title, &$article ){
		if( $title->getNamespace() == NS_TIMEDTEXT ) {
			$article = new TimedTextPage( $title );
		}
		return true;			
	}
	
	/**
	 * Wraps the isTranscodableFile function
	 * @param $title Title
	 */
	public static function isTranscodableTitle( $title ){
		if( $title->getNamespace() != NS_FILE ){
			return false;
		}
		$file = wfFindFile( $title );
		return self::isTranscodableFile( $file );
	}
	
	/**
	 * Utility function to check if a given file can be "transcoded"
	 * @param $file File object
	 */
	public static function isTranscodableFile( & $file ){
		global $wgEnableTranscode;
		
		// don't show the transcode table if transcode is disabled 
		if( $wgEnableTranscode === false ){
			return false;
		}
		// Can't find file 
		if( !$file ){
			return false;
		}
		// We can only transcode local files
		if( !$file->isLocal() ){
			return false;
		}
		$mediaType = $file->getHandler()->getMetadataType( $image = '' ); 
		// If ogg or webm format and not audio we can "transcode" this file
		if( ( $mediaType == 'webm' || $mediaType == 'ogg' ) && ! $file->getHandler()->isAudio( $file ) ){
			return true;
		}
		return false;
	}
	
	public static function checkForTranscodeStatus( $article, &$html ){
		// load the file: 
		$file = wfFindFile( $article->getTitle() );
		if( self::isTranscodableFile( $file ) ){
			$html = TranscodeStatusTable::getHTML( $file );
		}
		return true;
	}
	public static function checkUploadComplete( &$image ){
		if( self::isTranscodableTitle( $image->getTitle() ) ){
			// clear transcode data:
		}
		return true;
	}
	public static function checkArticleDeleteComplete( &$article, &$user, $reason, $id  ){
		// Check if the article is a file and remove transcode jobs:
		if( $article->getTitle()->getNamespace() == NS_FILE ) {
			$file = wfFindFile( $article->getTitle() );
			if( self::isTranscodableFile( $file ) ){
				WebVideoTranscode::removeTranscodeJobs( $file );
			}
		} 
		return true;
	}
	
	/**
	 * Adds the transcode sql 
	 */
	public static function loadExtensionSchemaUpdates( ){
		global $wgExtNewTables;
	    $wgExtNewTables[] = array(
	        'transcode',
	        dirname( __FILE__ ) . '/WebVideoTranscode/transcodeTable.sql' );
	    return true;
	}
	
	/**
	 * Hook to add list of PHPUnit test cases.
	 * @param $files Array of files
	 */
	public static function registerUnitTests( array &$files ) {
		$testDir = dirname( __FILE__ ) . '/tests/phpunit/';
		$testFiles = array(
			'TestTimeParsing.php',
			'TestApiUploadVideo.php',
			'TestVideoThumbnail.php',
			'TestVideoTranscode.php'
		);
		foreach( $testFiles as $fileName ){
			$files[] = $testDir . $fileName;
		}
		return true;
	}

	static function pageOutputHook(  &$out, &$sk ){
		$out->addModules( 'mw.PopUpMediaTransform' );
		$out->addModuleStyles( 'mw.PopUpMediaTransform' );
		return true;
	}
}
