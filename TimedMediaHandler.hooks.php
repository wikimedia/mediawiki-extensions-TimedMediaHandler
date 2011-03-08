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
		global $wgParserOutputHooks, $wgHooks, $wgJobClasses, $wgJobExplitRequestTypes, 
			$wgMediaHandlers, $wgResourceModules, $wgExcludeFromThumbnailPurge, 
			$wgTimedMediaHandlerFileExtensions, $wgParserOutputHooks, $wgOut, $wgAPIPropModules;

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
		$wgJobExplitRequestTypes[] = 'webVideoTranscode';
		
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
		

		// Exclude transcoded assets from normal thumbnail purging 
		// ( a mantaince script could handle transcode asset purging) 
		$wgExcludeFromThumbnailPurge = array_merge( $wgExcludeFromThumbnailPurge, $wgTimedMediaHandlerFileExtensions );
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
		global $wgExtraNamespaces;
		$timedTextNS = null;
	
		// Make sure $wgExtraNamespaces in an array ( set to NULL by default ) :
		if ( !is_array( $wgExtraNamespaces ) ) {
			$wgExtraNamespaces = array();
		}
		// Check for "TimedText" NS in $wgExtraNamespaces 
		$maxNS = 101; // content pages need "even" namespaces
		foreach($wgExtraNamespaces as $ns => $nsTitle ){
			if( $nsTitle == 'TimedText' ){
				$timedTextNS = $ns;
			}
			if( $ns > $maxNS ){
				$maxNs = $ns;
			}
		}
		
		// @@TODO maybe we should fire a warning here? 
		// Custom namespae management in mediawiki sucks :( 
		//
		// Since other extension use hacks like this as well.. it difficult to guarantee consistency 
		// of the timed text namespace if LocalSettings.php $wgExtraNamespaces is modified or another
		// extension that includes namespaces is added. 
		// ( obviously its best if set in LocalSetting.php )
		if( !$timedTextNS ){
			// Make sure that timedText is on an "even" page namespace: 
			$timedTextNS = ( ($maxNS + 1)&1 )? $maxNS + 1 : $maxNS + 2;
			$wgExtraNamespaces[	$timedTextNS ] = 'TimedText';
			$wgExtraNamespaces[ $timedTextNS +1 ] =  'TimedText_talk';
		}	
		define( "NS_TIMEDTEXT", $timedTextNS);		
		define( "NS_TIMEDTEXT_TALK", $timedTextNS +1);
	}
	static function pageOutputHook(  &$out, &$sk ){
		// FIXME we should only need to add this via parser output hook 	
		$out->addModules( 'PopUpMediaTransform' );
		$out->addModuleStyles( 'PopUpMediaTransform' );
		return true;
	}
	
}