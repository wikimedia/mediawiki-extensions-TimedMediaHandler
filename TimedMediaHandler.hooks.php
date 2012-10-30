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
		global $wgHooks, $wgJobClasses, $wgJobTypesExcludedFromDefaultQueue,
		$wgMediaHandlers, $wgResourceModules, $wgExcludeFromThumbnailPurge, $wgExtraNamespaces,
		$wgParserOutputHooks, $wgTimedTextNS, $wgFileExtensions, $wgTmhEnableMp4Uploads,
		$wgExtensionAssetsPath, $wgMwEmbedModuleConfig, $timedMediaDir, $wgCortadoJarFile;

		// Remove mp4 if not enabled:
		if( $wgTmhEnableMp4Uploads === false ){
			foreach( $wgFileExtensions as $inx => $val ) {
				if( $val == 'mp4' ){
					unset( $wgFileExtensions[$inx] );
				}
			}
		}


		// Register the Timed Media Handler javascript resources ( MwEmbed modules )
		MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/EmbedPlayer' );
		MwEmbedResourceManager::register( 'extensions/TimedMediaHandler/MwEmbedModules/TimedText' );

		// Set the default webPath for this embed player extension
		$wgMwEmbedModuleConfig['EmbedPlayer.WebPath'] = $wgExtensionAssetsPath .
			'/' . basename ( $timedMediaDir ) . '/MwEmbedModules/EmbedPlayer';

		// Register java cortado path config:
		$wgMwEmbedModuleConfig['wgCortadoJarFile'] = $wgCortadoJarFile;

		// Setup media Handlers:
		$wgMediaHandlers['application/ogg'] = 'OggHandlerTMH';
		$wgMediaHandlers['video/webm'] = 'WebMHandler';
		$wgMediaHandlers['video/mp4'] = 'Mp4Handler';

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
			'ext.tmh.transcodetable' => array_merge( $baseExtensionResource, array(
				'scripts' => 'resources/ext.tmh.transcodetable.js',
				'styles' => 'resources/transcodeTable.css',
				'messages'=> array(
					'mwe-ok',
					'mwe-cancel',
					'timedmedia-reset-error',
					'timedmedia-reset',
					'timedmedia-reset-confirm'
				)
			) ),
			"mw.MediaWikiPlayerSupport" =>  array_merge( $baseExtensionResource, array(
				'scripts' => 'resources/mw.MediaWikiPlayerSupport.js',
				'dependencies'=> array( 'mw.Api' )
			) ),
			// adds support MediaWikiPlayerSupport player bindings
			"mw.MediaWikiPlayer.loader" =>  array_merge( $baseExtensionResource, array(
				'loaderScripts' => 'resources/mw.MediaWikiPlayer.loader.js',
			) ),
		);
		// Setup a hook for iframe embed handling:
		$wgHooks['ArticleFromTitle'][] = 'TimedMediaIframeOutput::iframeHook';

		// When an upload completes ( check clear any existing transcodes )
		$wgHooks['UploadComplete'][] = 'TimedMediaHandlerHooks::checkUploadComplete';

		// When an image page is moved:
		$wgHooks['TitleMoveComplete'][] = 'TimedMediaHandlerHooks::checkTitleMoveComplete';

		// When image page is deleted so that we remove transcode settings / files.
		$wgHooks['ArticleDeleteComplete'][] = 'TimedMediaHandlerHooks::checkArticleDeleteComplete';

		// Add parser hook
		$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );

		// We should probably move this script output to a parser function but not working correctly in
		// dynamic contexts ( for example in special upload, when there is an "existing file" warning. )
		$wgHooks['BeforePageDisplay'][] = 'TimedMediaHandlerHooks::pageOutputHook';


		// Exclude transcoded assets from normal thumbnail purging
		// ( a maintenance script could handle transcode asset purging)
		if ( isset( $wgExcludeFromThumbnailPurge ) ) {
			$wgExcludeFromThumbnailPurge = array_merge( $wgExcludeFromThumbnailPurge, $wgTmhFileExtensions );
			// Also add the .log file ( used in two pass encoding )
			// ( probably should move in-progress encodes out of web accessible directory )
			$wgExcludeFromThumbnailPurge[] = 'log';
		}

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

	/**
	 * @param $title Title
	 * @param $article Article
	 * @return bool
	 */
	public static function checkForTimedTextPage( &$title, &$article ){
		if( $title->getNamespace() == NS_TIMEDTEXT ) {
			$article = new TimedTextPage( $title );
		}
		return true;
	}

	/**
	 * Wraps the isTranscodableFile function
	 * @param $title Title
	 * @return bool
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
	 * @return bool
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
		$mediaType = $file->getHandler()->getMetadataType( $file );
		// If ogg or webm format and not audio we can "transcode" this file
		if( ( $mediaType == 'webm' || $mediaType == 'ogg' || $mediaType =='mp4' ) && ! $file->getHandler()->isAudio( $file ) ){
			return true;
		}
		return false;
	}

	/**
	 * @param $article Article
	 * @param $html string
	 * @return bool
	 */
	public static function checkForTranscodeStatus( $article, &$html ){
		// load the file:
		$file = wfFindFile( $article->getTitle() );
		if( self::isTranscodableFile( $file ) ){
			$html = TranscodeStatusTable::getHTML( $file );
		}
		return true;
	}

	/**
	 * @param $image File
	 * @return bool
	 */
	public static function checkUploadComplete( &$image ){
		$title = $image->getTitle();
		// Check that the file is a transcodable asset:
		if( self::isTranscodableTitle( $title ) ){
			// Remove all the transcode files and db states for this asset ( will be re-added the first time the asset is displayed )
			$file = wfFindFile( $title );
			WebVideoTranscode::removeTranscodes( $file );
		}
		return true;
	}

	/**
	 * Handle moved titles
	 *
	 * For now we just remove all the derivatives for the oldTitle. In the future we could
	 * look at moving the files, but right now thumbs are not moved, so I don't want to be
	 * inconsistent.
	 * @param $title Title
	 * @param $newTitle Title
	 * @param $user User
	 * @param $oldid int
	 * @param $newid int
	 * @return bool
	 */
	public static function checkTitleMoveComplete( &$title, &$newTitle, &$user, $oldid, $newid ){
		if( self::isTranscodableTitle( $title ) ){
			// Remove all the transcode files and db states for this asset
			// ( will be re-added the first time the asset is displayed with its new title )
			$file = wfFindFile( $title );
			WebVideoTranscode::removeTranscodes( $file );
		}
		return true;
	}

	/**
	 * @param $article Article
	 * @param $user User
	 * @param $reason string
	 * @param $id
	 * @return bool
	 */
	public static function checkArticleDeleteComplete( &$article, &$user, $reason, $id  ){
		// Check if the article is a file and remove transcode files:
		if( $article->getTitle()->getNamespace() == NS_FILE ) {
			$file = wfFindFile( $article->getTitle() );
			if( self::isTranscodableFile( $file ) ){
				WebVideoTranscode::removeTranscodes( $file );
			}
		}
		return true;
	}

	/**
	 * Adds the transcode sql
	 * @param $updater DatabaseUpdater
	 * @return bool
	 */
	public static function loadExtensionSchemaUpdates( $updater ){
		$updater->addExtensionTable( 'transcode', dirname( __FILE__ ) . '/TimedMediaHandler.sql' );
		return true;
	}

	/**
	 * Hook to add list of PHPUnit test cases.
	 * @param $files Array of files
	 * @return bool
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

	/**
	 * @param $out OutputPage
	 * @param $sk
	 * @return bool
	 */
	static function pageOutputHook(  &$out, &$sk ){
		$out->addModules( 'mw.PopUpMediaTransform' );
		$out->addModuleStyles( 'mw.PopUpMediaTransform' );
		return true;
	}
}
