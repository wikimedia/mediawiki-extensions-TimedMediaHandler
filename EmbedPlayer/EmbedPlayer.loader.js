/**
* EmbedPlayer loader
*/

/**
* Default player module configuration 
*/
( function( mw ) {
	
	mw.setDefaultConfig( {
		// What tags will be re-written to video player by default
		// Set to empty string or null to avoid automatic video tag rewrites to embedPlayer 	
		"EmbedPlayer.RewriteTags" : "video,audio"		
	} );
	
	/**
	* Check the current DOM for any tags in "EmbedPlayer.RewriteTags"
	*/
	mw.documentHasPlayerTags = function() {
		var rewriteTags = mw.getConfig( 'EmbedPlayer.RewriteTags' );			
		if( $j( rewriteTags ).length != 0 ) {			
			return true;			
		}
		
		var tagCheckObject = { 'hasTags' : false };
		$j( mw ).trigger( 'LoaderEmbedPlayerDocumentHasPlayerTags', 
				[ tagCheckObject ]);
			 
		return tagCheckObject.hasTags;
	};

	/**
	* Add a DOM ready check for player tags 
	*
	* We use mw.addSetupHook instead of mw.ready so that
	* mwEmbed player is setup before any other mw.ready calls
	*/
	mw.addSetupHook( function( callback ) {
		mw.rewritePagePlayerTags();
		// Run the setupFlag to continue setup		
		callback();
	});
	
	mw.rewritePagePlayerTags = function() {
		mw.log( 'EmbedPlayer:: Document::' + mw.documentHasPlayerTags() );
		if( mw.documentHasPlayerTags() ) {
			var rewriteElementCount = 0;
			
			// Set each player to loading ( as early on as possible ) 
			$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).each( function( index, element ){
								
				// Assign an the element an ID ( if its missing one )			
				if ( $j( element ).attr( "id" ) == '' ) {
					$j( element ).attr( "id",  'v' + ( rewriteElementCount++ ) );
				}
				// Add an absolute positioned loader
				$j( element )
					.getAbsoluteOverlaySpinner()
					.attr('id', 'loadingSpinner_' + $j( element ).attr('id') )
					.addClass( 'playerLoadingSpinner' );
								
			});									
			// Load the embedPlayer module ( then run queued hooks )			
			mw.load( 'EmbedPlayer', function ( ) {		
				mw.log("EmbedPlayer:: do rewrite players:" + $j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).length );
				// Rewrite the EmbedPlayer.RewriteTags with the 
				$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).embedPlayer();				
			})
		}
	}

	/**
	* Add the module loader function:
	*/
	mediaWiki.loader.register( 'EmbedPlayer', function(){
		// Hide videonojs class
		$j( '.videonojs' ).hide();
		
		// Set up the embed video player class request: (include the skin js as well)
		var baseRequest = [								
			'mw.EmbedPlayer'
		
		 	'mw.PlayerControlBuilder',
			'$j.fn.hoverIntent',
			'mw.style.EmbedPlayer',
			'$j.cookie',
			
			// Add JSON lib if browsers does not define "JSON" natively
			'JSON',
			'$j.ui',
			'$j.widget'
					 	
			'$j.ui.mouse',
			'$j.fn.menu',			
			'mw.style.jquerymenu',
			'$j.ui.slider'			
		];

		// Pass every tag being rewritten through the update request function
		$j( mw.getConfig( 'EmbedPlayer.RewriteTags' ) ).each( function() {	
			var playerElement = this;		
			mw.embedPlayerUpdateLibraryRequest( playerElement,  dependencyRequest[ 1 ] )			
		} );
		
		// Add PNG fix code needed:
		if ( $j.browser.msie && $j.browser.version < 7 ) {
			dependencyRequest[0].push( '$j.fn.pngFix' );
		}
		
		// Do short detection, to avoid extra player library request in ~most~ cases. 
		//( If browser is firefox include native, if browser is IE include java ) 
		if( $j.browser.msie ) {
			dependencyRequest[0].push( 'mw.EmbedPlayerJava' )		
		}
				
		// Safari gets slower load since we have to detect ogg support 
		if( !!document.createElement('video').canPlayType &&  !$j.browser.safari  ) {		
			dependencyRequest[0].push( 'mw.EmbedPlayerNative' )
		}		
		
		// Return the set of libs to be loaded
		return dependencyRequest;			
	});
	mw.addModuleLoader( 'EmbedPlayer', function() {
		var _this = this;			
	} );
	
	/**
	 * Takes a embed player element and updates a request object with any 
	 * dependent libraries per that tags attributes.
	 * 
	 * For example a player skin class name could result in adding some 
	 *  css and javascirpt to the player library request. 
	 *    
	 * @param {Object} playerElement The tag to check for library dependent request classes.
	 * @param {Array} dependencyRequest The library request array
	 */
	mw.embedPlayerUpdateLibraryRequest = function(playerElement, dependencyRequest ){
		var skinName = $j( playerElement ).attr( 'class' );					
		// Set playerClassName to default if unset or not a valid skin	
		if( ! skinName || $j.inArray( skinName.toLowerCase(), mw.validSkins ) == -1 ){
			skinName = mw.getConfig( 'EmbedPlayer.SkinName' );
		}
		skinName = skinName.toLowerCase();		
		// Add the skin to the request 		
		var skinCaseName =  skinName.charAt(0).toUpperCase() + skinName.substr(1);
		
		// The skin js:		
		if( $j.inArray( 'mw.PlayerSkin' + skinCaseName, dependencyRequest ) == -1 ){
			dependencyRequest.push( 'mw.PlayerSkin' + skinCaseName );
		}
		
		// The skin css
		if( $j.inArray( 'mw.style.PlayerSkin' + skinCaseName, dependencyRequest ) == -1 ){
			dependencyRequest.push( 'mw.style.PlayerSkin' + skinCaseName );
		}
	
		// Allow extension to extend the request. 				
		$j( mw ).trigger( 'LoaderEmbedPlayerUpdateRequest', 
				[ playerElement, dependencyRequest ] );
	}

} )( window.mw );
