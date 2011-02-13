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
		global $wgParserOutputHooks, $wgHooks, $wgJobClasses, $wgJobExplitRequestTypes, $wgMediaHandlers;

		// Setup media Handlers: 
		$wgMediaHandlers['application/ogg'] = 'OggHandler';
		$wgMediaHandlers['video/webm'] = 'WebMHandler';
		
		// Parser hook for TimedMediaHandler output
		$wgParserOutputHooks['TimedMediaHandler'] = array( 'TimedMediaHandler', 'outputHook' );

		// Setup a hook for iframe embed handling:  
		$wgHooks['ArticleFromTitle'][] = 'TimedMediaIframeOutput::iframeHook';
		
		// Add transcode job class:
		$wgJobClasses+= array(
			'webVideoTranscode' => 'WebVideoTranscodeJob'
		);
		// Transcode jobs must be explicitly requested from the job queue: 
		$wgJobExplitRequestTypes+= array(
			'webVideoTranscode'
		);
				
		/**
		 * Add support for the "TimedText" NameSpace
		 */
		global $wgExtraNamespaces;
		$timedTextNS = null;
	
		// Make sure $wgExtraNamespaces in an array (set to NULL by default) :
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
		
		// If not found add Add a custom timedText NS
		if( !$timedTextNS ){
			$timedTextNS = ( $maxNS + 1 );
			$wgExtraNamespaces[	$timedTextNS ] = 'TimedText';
			$wgExtraNamespaces[ $timedTextNS +1 ] =  'TimedText_talk';
		}	
		define( "NS_TIMEDTEXT", $timedTextNS);		
		define( "NS_TIMEDTEXT_TALK", $timedTextNS +1);
	}
	
}