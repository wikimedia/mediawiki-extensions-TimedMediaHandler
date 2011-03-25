/**
* TimedText loader.
*/
// Scope everything in "mw" ( keeps the global namespace clean )
( function( mw, $ ) {
	
	/**
	* Check if the video tags in the page support timed text
	* this way we can add our timed text libraries to the player
	* library request.
	*/

	// Update the player loader request with timedText library if the embedPlayer
	// includes timedText tracks.
	$( mw ).bind( 'EmbedPlayerUpdateDependencies', function( event, playerElement, classRequest ) {
		if( mw.isTimedTextSupported( playerElement ) ) {
			classRequest = $.merge( classRequest, ['mw.TimedText'] );
		}
	} );		
	
	/**
	 * Check if we should load the timedText interface or not.
	 */
	mw.isTimedTextSupported = function( embedPlayer ) {
		if( mw.getConfig( 'TimedText.ShowInterface' ) == 'always' ) {
			return true;
		}
		// Do a module check for timed Text support ( note this module check is synchronous )
		var supportsTimedText = false;
		$( embedPlayer ).trigger('SupportsTimedText', function( moduleSupportsTimedText) {
			if( moduleSupportsTimedText ){
				supportsTimedText = true;
			}
		});
		
		if( supportsTimedText ){
			return true;
		}
		// Check for standard 'track' attribute: 
		if ( $( embedPlayer ).find( 'track' ).length != 0 ) {
			return true;
		} else {
			return false;
		}
	};	

} )( window.mediaWiki, window.jQuery );