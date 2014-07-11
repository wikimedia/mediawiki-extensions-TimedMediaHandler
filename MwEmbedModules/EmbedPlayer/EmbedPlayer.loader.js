/**
* EmbedPlayer loader
*/
( function( mw, $ ) {
	/**
	* Add a DOM ready check for player tags
	*/
	$( function() {
		var $selected = $( mw.config.get( 'EmbedPlayer.RewriteSelector' ) );
		if ( $selected.length ) {
			var inx = 0;
			var checkSetDone = function() {
				if ( inx < $selected.length ) {
					// put in timeout to avoid browser lockup, and function stack
					$selected.slice( inx, inx + 1 ).embedPlayer( function() {
						setTimeout( function() {
							checkSetDone();
						}, 5 );
					} );
				}
				inx++;
			};

			checkSetDone();
		}
	} );

	/**
	* Add the mwEmbed jQuery loader wrapper
	*/
	$.fn.embedPlayer = function( readyCallback ) {
		var playerSet = this;
		mw.log( 'jQuery.fn.embedPlayer :: ' + $( playerSet ).length );

		// Set up the embed video player class request: (include the skin js as well)
		var dependencySet = [
			'mw.EmbedPlayer'
		];

		mw.loader.using( [ 'jquery.client', 'jquery.mwEmbedUtil', 'mw.MwEmbedSupport' ], function() {
			$( playerSet ).each( function( inx, playerElement ) {
				// we have javascript ( disable controls )
				$( playerElement ).removeAttr( 'controls' );
				// Add an overlay loader ( firefox has its own native loading spinner )

				if ( $.client.profile().name !== 'firefox' ) {
					$( playerElement )
						.parent()
						.getAbsoluteOverlaySpinner()
						.attr('id', 'loadingSpinner_' + $( playerElement ).attr('id') );
				}
				// Allow other modules update the dependencies
				$( mw ).trigger( 'EmbedPlayerUpdateDependencies',
						[ playerElement, dependencySet ] );
			} );

			// Remove any duplicates in the dependencySet:
			dependencySet = $.uniqueArray( dependencySet );

			// Do the request and process the playerElements with updated dependency set
			mw.loader.using( dependencySet, function() {
				// Setup the enhanced language:
				mw.processEmbedPlayers( playerSet, readyCallback );
			}, function( e ) {
				throw new Error( 'Error loading EmbedPlayer dependency set: ' + e.message );
			} );
		} );
	};
} )( window.mediaWiki, window.jQuery );