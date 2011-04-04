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
		$tmhFileExtensions, $wgParserOutputHooks, $wgOut, $wgAPIPropModules, $wgTimedTextNS;

		// Setup media Handlers:
		$wgMediaHandlers['application/ogg'] = 'OggHandler';
		$wgMediaHandlers['video/webm'] = 'WebMHandler';

		// Setup a hook for iframe embed handling:
		$wgHooks['ArticleFromTitle'][] = 'TimedMediaIframeOutput::iframeHook';

		// Add parser hook
		$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );

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
			'PopUpMediaTransform' => $baseExtensionResource + array(
				'scripts' => 'resources/PopUpThumbVideo.js',
				'styles' => 'resources/PopUpThumbVideo.css',
				'dependencies' => array( 'jquery.ui.dialog' ),
			),
			'embedPlayerIframeStyle'=> $baseExtensionResource + array(
				'styles' => 'resources/embedPlayerIframe.css',
			)
			);

			// We should probably move this to a parser function but not working correctly in
			// dynamic contexts ( for example in special upload, when there is an "existing file" warning. )
			$wgHooks['BeforePageDisplay'][] = 'TimedMediaHandlerHooks::pageOutputHook';


			// Add unit tests
			$wgHooks['UnitTestsList'][] = 'TimedMediaHandlerHooks::registerUnitTests';

			// Exclude transcoded assets from normal thumbnail purging
			// ( a maintenance script could handle transcode asset purging)
			$wgExcludeFromThumbnailPurge = array_merge( $wgExcludeFromThumbnailPurge, $tmhFileExtensions );
			// Also add the .log file ( used in two pass encoding )
			// ( probably should move in-progress encodes out of web accessible directory )
			$wgExcludeFromThumbnailPurge[] = 'log';

			// Api hooks for derivatives and query video derivatives
			$wgAPIPropModules += array(
			'videoinfo' => 'ApiQueryVideoInfo'
			);

			/**
			 * Add support for the "TimedText" NameSpace
			 */
			define( "NS_TIMEDTEXT", $wgTimedTextNS);
			define( "NS_TIMEDTEXT_TALK", $wgTimedTextNS +1);

			$wgExtraNamespaces[NS_TIMEDTEXT] = "TimedText";
			$wgExtraNamespaces[NS_TIMEDTEXT_TALK] = "TimedText_talk";

			return true;
	}

	/**
	 * Hook to add list of PHPUnit test cases.
	 *
	 * @since 0.7
	 *
	 * @param array $files
	 */
	public static function registerUnitTests( array &$files ) {
		$testDir = dirname( __FILE__ ) . '/tests/phpunit/';
		$files[] = $testDir . 'TestTimeParsing.php';
		$files[] = $testDir . 'TestApiUploadVideo.php';
		return true;
	}

	static function pageOutputHook(  &$out, &$sk ){
		// FIXME we should only need to add this via parser output hook
		$out->addModules( 'PopUpMediaTransform' );
		$out->addModuleStyles( 'PopUpMediaTransform' );
		return true;
	}

}