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
		$wgExtensionAssetsPath, $wgMwEmbedModuleConfig, $timedMediaDir, $wgCortadoJarFile,
		$wgEnableLocalTimedText, $wgTmhFileExtensions;

		// Remove mp4 if not enabled:
		if( $wgTmhEnableMp4Uploads === false ){
			$index = array_search( 'mp4', $wgFileExtensions );
			if ( $index !== false ) {
				array_splice( $wgFileExtensions, $index, 1 );
			}
		}

		if( !class_exists( 'MwEmbedResourceManager' ) ) {
			echo "TimedMediaHandler requires the MwEmbedSupport extension.\n";
			exit( 1 );
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
		$wgMediaHandlers['audio/webm'] = 'WebMHandler';
		$wgMediaHandlers['video/webm'] = 'WebMHandler';
		$wgMediaHandlers['video/mp4'] = 'Mp4Handler';
		$wgMediaHandlers['audio/x-flac'] = 'FLACHandler';
		$wgMediaHandlers['audio/flac'] = 'FLACHandler';
		$wgMediaHandlers['audio/wav'] = 'WAVHandler';

		// Add transcode job class:
		$wgJobClasses['webVideoTranscode'] = 'WebVideoTranscodeJob';

		// Transcode jobs must be explicitly requested from the job queue:
		$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';

		$baseExtensionResource = array(
			'localBasePath' => __DIR__,
			'remoteExtPath' => 'TimedMediaHandler',
		);

		// Add the PopUpMediaTransform module ( specific to timedMedia handler ( no support in mwEmbed modules )
		$wgResourceModules+= array(
			'mw.PopUpMediaTransform' => $baseExtensionResource + array(
				'scripts' => 'resources/mw.PopUpThumbVideo.js',
				'styles' => 'resources/PopUpThumbVideo.css',
				'dependencies' => array( 'mw.MwEmbedSupport', 'mediawiki.Title' ),
			),
			'mw.TMHGalleryHook.js' => $baseExtensionResource + array(
				'scripts' => 'resources/mw.TMHGalleryHook.js',
				// position top needed as it needs to load before mediawiki.page.gallery
				'position' => 'top',
			),
			'embedPlayerIframeStyle'=> $baseExtensionResource + array(
				'styles' => 'resources/embedPlayerIframe.css',
			),
			'ext.tmh.transcodetable' => $baseExtensionResource + array(
				'scripts' => 'resources/ext.tmh.transcodetable.js',
				'styles' => 'resources/transcodeTable.css',
				'dependencies' => array(
					'mediawiki.api.edit',
					'mw.MwEmbedSupport',
				),
				'messages'=> array(
					'mwe-ok',
					'mwe-cancel',
					'timedmedia-reset-error',
					'timedmedia-reset',
					'timedmedia-reset-confirm'
				)
			),
			"mw.MediaWikiPlayerSupport" =>  $baseExtensionResource + array(
				'scripts' => 'resources/mw.MediaWikiPlayerSupport.js',
				'dependencies'=> 'mw.Api',
			),
			// adds support MediaWikiPlayerSupport player bindings
			"mw.MediaWikiPlayer.loader" =>  $baseExtensionResource + array(
				'loaderScripts' => 'resources/mw.MediaWikiPlayer.loader.js',
			),
		);
		// Setup a hook for iframe embed handling:
		$wgHooks['ArticleFromTitle'][] = 'TimedMediaIframeOutput::iframeHook';

		// When an upload completes ( check clear any existing transcodes )
		$wgHooks['UploadComplete'][] = 'TimedMediaHandlerHooks::checkUploadComplete';

		// When an image page is moved:
		$wgHooks['TitleMove'][] = 'TimedMediaHandlerHooks::checkTitleMove';

		// When image page is deleted so that we remove transcode settings / files.
		$wgHooks['FileDeleteComplete'][] = 'TimedMediaHandlerHooks::onFileDeleteComplete';

		// Add parser hook
		$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );

		// Use a BeforePageDisplay hook to load the styles in pages that pull in media dynamically.
		// (Special:Upload, for example, when there is an "existing file" warning.)
		$wgHooks['BeforePageDisplay'][] = 'TimedMediaHandlerHooks::pageOutputHook';

		// Make sure modules are loaded on image pages that don't have a media file in the wikitext.
		$wgHooks['ImageOpenShowImageInlineBefore'][] = 'TimedMediaHandlerHooks::onImageOpenShowImageInlineBefore';

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
		if ( $wgEnableLocalTimedText ) {
			define( "NS_TIMEDTEXT", $wgTimedTextNS );
			define( "NS_TIMEDTEXT_TALK", $wgTimedTextNS +1 );

			$wgExtraNamespaces[NS_TIMEDTEXT] = "TimedText";
			$wgExtraNamespaces[NS_TIMEDTEXT_TALK] = "TimedText_talk";

			// Check for timed text page:
			$wgHooks[ 'ArticleFromTitle' ][] = 'TimedMediaHandlerHooks::checkForTimedTextPage';
			$wgHooks[ 'ArticleContentOnDiff' ][] = 'TimedMediaHandlerHooks::checkForTimedTextDiff';
		} else {
			$wgTimedTextNS = false;
			// overwrite TimedText.ShowInterface for video with mw-provider=local
			$wgMwEmbedModuleConfig['TimedText.ShowInterface.local'] = 'off';
		}

		// Add transcode status to video asset pages:
		$wgHooks[ 'ImagePageAfterImageLinks' ][] = 'TimedMediaHandlerHooks::checkForTranscodeStatus';
		$wgHooks[ 'NewRevisionFromEditComplete' ][] = 'TimedMediaHandlerHooks::onNewRevisionFromEditComplete';

		$wgHooks['LoadExtensionSchemaUpdates'][] = 'TimedMediaHandlerHooks::checkSchemaUpdates';
		$wgHooks['wgQueryPages'][] = 'TimedMediaHandlerHooks::onwgQueryPages';
		return true;
	}

	/**
	 * @param $imagePage ImagePage
	 * @param $wgOut OutputPage
	 * @return bool
	 */
	public static function onImageOpenShowImageInlineBefore( $imagePage, $out ) {
		$handler = $imagePage->getDisplayedFile()->getHandler();
		if ( $handler !== false && $handler instanceof TimedMediaHandler ) {
			TimedMediaHandler::outputHook( $out, null, null );
		}
		return true;
	}

	/**
	 * @param $title Title
	 * @param $article Article
	 * @return bool
	 */
	public static function checkForTimedTextPage( &$title, &$article ){
		global $wgTimedTextNS;
		if ( $title->getNamespace() === $wgTimedTextNS ) {
			$article = new TimedTextPage( $title );
		}
		return true;
	}

	/**
	 * @param $diffEngine DifferenceEngine
	 * @param $output OutputPage
	 * @return bool
	 */
	public static function checkForTimedTextDiff( $diffEngine, $output ) {
		global $wgTimedTextNS;
		if ( $output->getTitle()->getNamespace() === $wgTimedTextNS ) {
			$article = new TimedTextPage( $output->getTitle() );
			$article->renderOutput( $output );
			return false;
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
		global $wgEnableTranscode, $wgEnabledAudioTranscodeSet;

		// don't show the transcode table if transcode is disabled
		if( !$wgEnableTranscode && !$wgEnabledAudioTranscodeSet ){
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

		$handler = $file->getHandler();
		// Not able to transcode files without handler
		if( !$handler ) {
			return false;
		}
		$mediaType = $handler->getMetadataType( $file );
		// If ogg or webm format and not audio we can "transcode" this file
		$isAudio = $handler instanceof TimedMediaHandler && $handler->isAudio( $file );
		if( ( $mediaType == 'webm' || $mediaType == 'ogg' || $mediaType =='mp4' )
			&& !$isAudio
		){
			return true;
		}
		if( $isAudio && count( $wgEnabledAudioTranscodeSet ) ) {
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
			$html .= TranscodeStatusTable::getHTML( $file );
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
	 * @return bool
	 */
	public static function checkTitleMove( $title, $newTitle, $user ){
		if( self::isTranscodableTitle( $title ) ){
			// Remove all the transcode files and db states for this asset
			// ( will be re-added the first time the asset is displayed with its new title )
			$file = wfFindFile( $title );
			WebVideoTranscode::removeTranscodes( $file );
		}
		return true;
	}

	/**
	 * Hook to FileDeleteComplete
	 * remove transcodes on delete
	 * @param $file File
	 * @param $oldimage
	 * @param $article Article
	 * @param $user User
	 * @param $reason string
	 * @return bool
	 */
	public static function onFileDeleteComplete( $file, $oldimage, $article, $user, $reason ) {
		if ( !$oldimage ) {
			if( self::isTranscodableFile( $file ) ){
				WebVideoTranscode::removeTranscodes( $file );
			}
		}
		return true;
	}

	/*
	 * If file gets reverted to a previous version, remove transcodes.
	 */
	public static function onNewRevisionFromEditComplete( $article, Revision $rev, $baseID, User $user ) {
		if ( $baseID !== false ) {
			// Check if the article is a file and remove transcode files:
			if( $article->getTitle()->getNamespace() == NS_FILE ) {
				$file = wfFindFile( $article->getTitle() );
				if( self::isTranscodableFile( $file ) ){
					WebVideoTranscode::removeTranscodes( $file );
				}
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
		$updater->addExtensionTable( 'transcode', __DIR__ . '/TimedMediaHandler.sql' );
		return true;
	}

	/**
	 * Hook to add list of PHPUnit test cases.
	 * @param $files Array of files
	 * @return bool
	 */
	public static function registerUnitTests( array &$files ) {
		$testDir = __DIR__ . '/tests/phpunit/';
		$testFiles = array(
			'TestTimeParsing.php',
			'TestApiUploadVideo.php',
			'TestVideoThumbnail.php',
			'TestVideoTranscode.php',
			'TestOggHandler.php',
			'TestTimedMediaTransformOutput.php',
			'TestTimedMediaHandler.php'
		);
		foreach( $testFiles as $fileName ){
			$files[] = $testDir . $fileName;
		}
		return true;
	}

	/**
	 * Add JavaScript and CSS for special pages that may include timed media
	 * but which will not fire the parser hook.
	 *
	 * FIXME: There ought to be a better interface for determining whether the
	 * page is liable to contain timed media.
	 *
	 * @param $out OutputPage
	 * @param $sk
	 * @return bool
	 */
	static function pageOutputHook(  &$out, &$sk ){
		global $wgTimedTextNS;

		$title = $out->getTitle();
		$namespace = $title->getNamespace();
		$addModules = false;

		if ( $namespace === NS_CATEGORY || $namespace === $wgTimedTextNS ) {
			$addModules = true;
		}

		if ( $title->isSpecialPage() ) {
			list( $name, /* subpage */ ) = SpecialPageFactory::resolveAlias( $title->getDBkey() );
			if ( stripos( $name, 'file' ) !== false || stripos( $name, 'image' ) !== false
				|| $name === 'Search' || $name === 'GlobalUsage' ) {
					$addModules = true;
			}
		}

		if ( $addModules ) {
			$out->addModuleScripts( 'mw.PopUpMediaTransform' );
			$out->addModuleStyles( 'mw.PopUpMediaTransform' );
		}

		return true;
	}

	public static function checkSchemaUpdates( DatabaseUpdater $updater ) {
		$base = __DIR__ ;

		switch ( $updater->getDB()->getType() ) {
		case 'mysql':
		case 'sqlite':
			$updater->addExtensionTable( 'transcode', "$base/TimedMediaHandler.sql" ); // Initial install tables
			$updater->addExtensionUpdate( array( 'addIndex', 'transcode', 'transcode_name_key',
				"$base/archives/transcode_name_key.sql", true ) );
			break;
		case 'postgres':
			//TODO
			break;
		}
		return true;
	}

	public static function onwgQueryPages( $qp ) {
		$qp[] = array( 'SpecialOrphanedTimedText', 'OrphanedTimedText' );
		return true;
	}
}
