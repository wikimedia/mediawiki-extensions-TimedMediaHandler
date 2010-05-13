/**
* EmbedPlayer loader
*/

/**
* Default player module configuration 
*/

mw.setDefaultConfig( {
	// If the Timed Text interface should be displayed: 
	// 'always' Displays link and call to contribute always
	// 'auto' Looks for child timed text elements or "apiTitleKey" & load interface
	// 'off' Does not display the timed text interface	
	"textInterface" : "auto",
	
	// If the player controls should be overlaid on top of the video ( if supported by playback method)
	// can be set to false per embed player via overlayControls attribute 
	'overlayControls' : true,
	
	// A default apiProvider ( ie where to lookup subtitles, video properties etc )
	// NOTE: Each player instance can also specify a specific provider  
	"apiProvider" : "commons",
	
	// What tags will be re-written to video player by default
	// Set to empty string or null to avoid automatic video tag rewrites to embedPlayer 	
	"rewritePlayerTags" : "video,audio,playlist",

	// Default video size ( if no size provided )	
	"videoSize" : "400x300",

	// If the video player should attribute kaltura	
	"kalturaAttribution" : true,
	 
	 // Set the browser player warning flag to true by default ( applies to all players so its not part of attribute defaults above ) 
	"showNativePlayerWarning" : true,
	
	// If fullscreen is global enabled. 
	"enableFullscreen" : true,
	
	// If mwEmbed should use the Native player controls
	// this will prevent video tag rewriting and skinning
	// useful for devices such as iPad / iPod that
	// don't fully support DOM overlays or don't expose full-screen 
	// functionality to javascript  
	"nativePlayerControls" : false,
	
	// If mwembed should use native controls on mobile safari
	"nativePlayerControlsMobileSafari" : true,
	
	
	// The z-index given to the player interface during full screen ( high z-index )  
	"fullScreenIndex" : 999998,
	
	// The default share embed mode ( can be "object" or "videojs" )
	//
	// "object" will provide a <object tag pointing to mwEmbedFrame.php
	// 		Object embedding should be much more compatible with sites that
	//		let users embed flash applets
	// "videojs" will include the source javascript and video tag to
	//	 	rewrite the player on the remote page DOM  
	//		Video tag embedding is much more mash-up friendly but exposes
	//		the remote site to the mwEmbed js. 
	"shareEmbedMode" : 'object',
	
	// Default player skin name
	"playerSkinName" : "mvpcf"	
} );

// Add class file paths 
mw.addClassFilePaths( {
	"mw.EmbedPlayer"	: "mw.EmbedPlayer.js",
	"flowplayerEmbed"	: "flowplayerEmbed.js",
	"kplayerEmbed"		: "kplayerEmbed.js",
	"genericEmbed"		: "genericEmbed.js",
	"htmlEmbed"			: "htmlEmbed.js",
	"javaEmbed"			: "javaEmbed.js",
	"nativeEmbed"		: "nativeEmbed.js",
	"quicktimeEmbed"	: "quicktimeEmbed.js",
	"vlcEmbed"			: "vlcEmbed.js",
	
	"ctrlBuilder"		: "skins/ctrlBuilder.js",
	
	"mw.style.kskin" 	: "skins/kskin/EmbedPlayer.css",	
	"kskinConfig"		: "skins/kskin/kskinConfig.js",
	
	"mvpcfConfig"		: "skins/mvpcf/mvpcfConfig.js",
	"mw.style.mvpcf" 	: "skins/mvpcf/EmbedPlayer.css"	
} );

/**
* Check the current DOM for any tags in "rewritePlayerTags"
* 
* NOTE: this function can be part of setup can run prior to jQuery being ready
*/
mw.documentHasPlayerTags = function() {
	var rewriteTags = mw.getConfig( 'rewritePlayerTags' );				
	if( rewriteTags ) {
		var jtags = rewriteTags.split( ',' );
		for ( var i = 0; i < jtags.length; i++ ) { 
			if( document.getElementsByTagName( jtags[i] )[0] ) {				
				return true;
			}
		}
	}
	return false;
};

/**
* Add a DOM ready check for player tags 

* We use mw.addDOMReadyHook instead of mw.ready so that
* player interfaces are ready once mw.ready is called. 
*/
mw.addSetupHook( function( callback ) {
	if( mw.documentHasPlayerTags() ) {
		var  rewriteElementCount = 0;
		
		// Set each player to loading ( as early on as possible ) 
		$j( mw.getConfig( 'rewritePlayerTags' ) ).each( function( index, element ){
							
			// Assign an the element an ID (if its missing one)			
			if ( $j( element ).attr( "id" ) == '' ) {
				$j( element ).attr( "id",  'v' + ( rewriteElementCount++ ) );
			}
							
			// Add an absolute positioned loader
			var pos = $j( element ).offset();	
			var left = (  $j( element ).width() ) ? 
				parseInt( pos.left + ( .4 * $j( element ).width() ) ) : 
				pos.left + 30;
			var top = (  $j( element ).height() ) ? 
				parseInt( pos.top + ( .4 * $j( element ).height() ) ) : 
				pos.left + 30;								
			$j('body').append(
				$j('<div />')
				.loadingSpinner()
				.attr('id', 'loadSpiner_' + $j( element ).attr('id') )
				.css({
					'width' : 32,
					'height' : 32,
					'position': 'absolute',
					'top' : top,
					'left' : left
				})						
			)				
			//$j( element ).hide();
		});									
		// Load the embedPlayer module ( then run queued hooks )
		mw.load( 'EmbedPlayer', function ( ) {
			// Rewrite the rewritePlayerTags with the 
			$j( mw.getConfig( 'rewritePlayerTags' ) ).embedPlayer();				
			// Run the setup callback now that we have setup all the players
			callback();
		})
	} else {
		callback();
	}
});

/**
* Add the module loader function:
*/
mw.addModuleLoader( 'EmbedPlayer', function( callback ) {
	var _this = this;		
	// Set module specific class videonojs to loading:
	$j( '.videonojs' ).html( gM( 'mwe-embedplayer-loading_txt' ) );
	
	// Set up the embed video player class request: (include the skin js as well)
	var dependencyRequest = [
		[
			'$j.ui',			
			'mw.EmbedPlayer',
			'ctrlBuilder',
			'$j.cookie',
			// Add JSON lib if browsers does not define "JSON" natively 			
			'JSON'
		],
		[
			'$j.fn.menu',			
			'mw.style.jquerymenu',
			'$j.ui.slider'
		]
		
	];
	
	var playerSkins = {};
	// Get the class of all embed video elements 
	// to add the skin to the load request
	$j( mw.getConfig( 'rewritePlayerTags' ) ).each( function() {	
		var playerElement = this;		
		var playerClassName = $j( playerElement ).attr( 'class' );	
		// Set playerClassName to default	
		if( ! playerClassName ){
			playerClassName = mw.getConfig( 'playerSkinName' );
		}		
		for( var n=0; n < mw.valid_skins.length ; n++ ) {
			// Get any other skins that we need to load 
			// That way skin js can be part of the single script-loader request: 
			if( playerClassName.indexOf( mw.valid_skins[ n ] ) !== -1) {
				// Add skin name to playerSkins
				playerSkins[ mw.valid_skins[ n ] ] = true;
			}
		}
		mw.log("LoaderEmbedPlayerVisitTag" );
		$j( mw ).trigger( 'LoaderEmbedPlayerVisitTag', playerElement );
	} );
	
	// Add the player skins css and js to the load request:	
	for( var pSkin in playerSkins ) {
		// Add skin js
		dependencyRequest[0].push(  pSkin  + 'Config' );	
		// Add the skin css 
		dependencyRequest[0].push( 'mw.style.' + pSkin );
	}	
	
	// Add PNG fix code needed:
	if ( $j.browser.msie && $j.browser.version < 7 ) {
		dependencyRequest[0].push( '$j.fn.pngFix' );
	}
	
	// Do short detection, to avoid extra player library request in ~most~ cases. 
	//( If browser is firefox include native, if browser is IE include java ) 
	if( $j.browser.msie ) {
		dependencyRequest[0].push( 'javaEmbed' )		
	}
	
	// Safari gets slower load since we have to detect ogg support 
	if( typeof HTMLVideoElement == 'object' &&  !$j.browser.safari  ) {
		dependencyRequest[0].push( 'nativeEmbed' )
	}
		
	// Run the EmbedPlayer loader hook ( so that modules can add dependencies to the request ) 
	mw.log('LoaderEmbedPlayerUpdateRequest');
	$j( mw ).trigger( 'LoaderEmbedPlayerUpdateRequest', [ dependencyRequest[ 0 ] ] );
		
	
	// Load the video libs:
	mw.load( dependencyRequest, function() {
		// Setup userConfig 
		mw.setupUserConfig( function() {
			// Remove no video html elements:
			$j( '.videonojs' ).remove();
			
			// Detect supported players:  
			mw.EmbedTypes.init();		
			
			//mw.log(" run callback: " + callback );
						
			// Run the callback with name of the module  
			if( typeof callback == 'function' )	{
				callback( 'EmbedPlayer' );		
			}
			
		} ); // setupUserConfig
	} );
	
} );
