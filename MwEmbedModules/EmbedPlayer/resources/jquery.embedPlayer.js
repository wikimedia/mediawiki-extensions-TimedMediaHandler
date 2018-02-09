/**
* $.fn.embedPlayer
*/
( function ( mw, $ ) {
	/**
	* Add the mwEmbed jQuery loader wrapper
	*/
	$.fn.embedPlayer = function ( readyCallback ) {
		var playerSet = this;
		mw.log( 'jQuery.fn.embedPlayer :: ' + $( playerSet ).length );

		// Set up the embed video player class request: (include the skin js as well)
		var dependencySet = [
			'mw.EmbedPlayer'
		];

		mw.loader.using( [ 'jquery.client', 'jquery.mwEmbedUtil', 'mw.MwEmbedSupport' ], function () {
			$( playerSet ).each( function ( inx, playerElement ) {
				// we have javascript ( disable controls )
				$( playerElement ).prop( 'controls', false );
				// Add an overlay loader ( firefox has its own native loading spinner )

				if ( $.client.profile().name !== 'firefox' ) {
					$( playerElement )
						.parent()
						.getAbsoluteOverlaySpinner()
						.attr( 'id', 'loadingSpinner_' + $( playerElement ).attr( 'id' ) );
				}
				// Allow other modules update the dependencies
				$( mw ).trigger( 'EmbedPlayerUpdateDependencies',
					[ playerElement, dependencySet ] );
			} );

			// Remove any duplicates in the dependencySet:
			dependencySet = $.uniqueArray( dependencySet );

			// Do the request and process the playerElements with updated dependency set
			mw.loader.using( dependencySet, function () {
				// Delay actual player setup to the next exectution run, because
				// wikipage.content can fire before the content is attached, and that
				// breaks something deep inside the player setup.
				setTimeout( function () {
					mw.processEmbedPlayers( playerSet, readyCallback );
				} );
			}, function ( e ) {
				throw new Error( 'Error loading EmbedPlayer dependency set: ' + e.message );
			} );
		} );
	};
}( mediaWiki, jQuery ) );
