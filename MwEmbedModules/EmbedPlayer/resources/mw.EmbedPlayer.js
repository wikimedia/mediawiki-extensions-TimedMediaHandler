/**
* embedPlayer is the base class for html5 video tag javascript abstraction library
* embedPlayer include a few subclasses:
*
* mediaPlayer Media player embed system ie: java, vlc or native.
* mediaElement Represents source media elements
* mw.PlayerControlBuilder Handles skinning of the player controls
*/

( function( mw, $ ) {
/**
 * The base source attribute checks also see:
 * http://dev.w3.org/html5/spec/Overview.html#the-source-element
 */
mw.mergeConfig( 'EmbedPlayer.SourceAttributes', [
	// source id
	'id',

	// media url
	'src',

	// Title string for the source asset
	'title',

	// boolean if we support temporal url requests on the source media
	'URLTimeEncoding',

	// Media has a startOffset ( used for plugins that
	// display ogg page time rather than presentation time
	'data-startoffset',

	// A hint to the duration of the media file so that duration
	// can be displayed in the player without loading the media file
	'data-durationhint',
	
	// Source stream qualities ( will eventually be adaptive streaming )
	'data-shorttitle', // short title for stream ( usefull for stream switching control bar item) 
	'data-width', // the width of the stream
	'data-height', // the height of the stream
	'data-bandwith', // the overall bitrate of the stream
	'data-framerate', // the framereate of the stream
	
	// Media start time
	'start',

	// Media end time
	'end',

	// If the source is the default source
	'default'
] );

/** 
 * Merge in the default video attributes supported by embedPlayer:
 */
mw.mergeConfig('EmbedPlayer.Attributes', {
	/*
	 * Base html element attributes:
	 */

	// id: Auto-populated if unset
	"id" : null,

	// Width: alternate to "style" to set player width
	"width" : null,

	// Height: alternative to "style" to set player height
	"height" : null,

	/*
	 * Base html5 video element attributes / states also see:
	 * http://www.whatwg.org/specs/web-apps/current-work/multipage/video.html
	 */

	// Media src URI, can be relative or absolute URI
	"src" : null,

	// Poster attribute for displaying a place holder image before loading
	// or playing the video
	"poster" : null,

	// Autoplay if the media should start playing
	"autoplay" : false,

	// Loop attribute if the media should repeat on complete
	"loop" : false,

	// If the player controls should be displayed
	"controls" : true,

	// Video starts "paused"
	"paused" : true,

	// ReadyState an attribute informs clients of video loading state:
	// see: http://www.whatwg.org/specs/web-apps/current-work/#readystate
	"readyState" : 0,

	// Loading state of the video element
	"networkState" : 0,

	// Current playback position
	"currentTime" : 0,

	// Previous player set time
	// Lets javascript use $('#videoId').get(0).currentTime = newTime;
	"previousTime" : 0,

	// Previous player set volume
	// Lets javascript use $('#videoId').get(0).volume = newVolume;
	"previousVolume" : 1,

	// Initial player volume:
	"volume" : 0.75,

	// Caches the volume before a mute toggle
	"preMuteVolume" : 0.75,

	// Media duration: Value is populated via
	// custom data-durationhint attribute or via the media file once its played
	"duration" : null,

	// Mute state
	"muted" : false,

	/**
	 * Custom attributes for embedPlayer player: (not part of the html5
	 * video spec)
	 */

	// Default video aspect ratio
	'videoAspect' : '4:3',

	// Start time of the clip
	"start" : 0,

	// End time of the clip
	"end" : null,

	// If the player controls should be overlaid
	// ( Global default via config EmbedPlayer.OverlayControls in module
	// loader.js)
	"overlaycontrols" : true,

	// Attribute to use 'native' controls
	"usenativecontrols" : false,

	// If the player should include an attribution button:
	'attributionbutton' : true,

	// If serving an ogg_chop segment use this to offset the presentation time
	// ( for some plugins that use ogg page time rather than presentation time )
	"startOffset" : 0,
	
	// If the download link should be shown
	"download_link" : true,

	// Content type of the media
	"type" : null
} );


/**
 * Selector based embedPlayer processing
 * 
 * @param {Function=}
 *      callback Optional Function to be called once video interfaces
 *      are ready
 *
 */
mw.processEmbedPlayers = function( playerSelect, callback ) {
	mw.log( 'EmbedPlayer:: processEmbedPlayers' );
	
		
	/**
	 * Adds a player element for the embedPlayer to rewrite
	 *
	 * uses embedPlayer interface on audio / video elements uses mvPlayList
	 * interface on playlist elements
	 *
	 * Once a player interface is established the following chain of functions
	 * are called;
	 *
	 * _this.checkPlayerSources() 
	 * _this.setupSourcePlayer() 
	 * _this.inheritEmbedPlayer()
	 * _this.selectedPlayer.load() 
	 * _this.showPlayer()
	 *
	 * @param {Element}
	 *      playerElement DOM element to be swapped
	 * @param {Object}
	 *      [Optional] attributes Extra attributes to apply to the player
	 *      interface
	 */
	var addPlayerElement = function( playerElement ) {
		var _this = this;
		mw.log('EmbedPlayer:: addElement:: ' + playerElement.id );

		var waitForMeta = true;

		// Be sure to "stop" the target ( Firefox 3x keeps playing
		// the video even though its been removed from the DOM )
		if( playerElement.pause ){
			playerElement.pause();
		}
		
		// Allow modules to override the wait for metadata flag:
		$( mw ).trigger( 'checkPlayerWaitForMetaData', playerElement );

		// Update the waitForMeta object if set to boolean false: 
		waitForMeta = ( playerElement.waitForMeta === false )? false : true;


		// Confirm we want to wait for meta data ( if not already set to false by module )
		if( waitForMeta ){
			waitForMeta = waitForMetaCheck( playerElement );
		}
		
		var ranPlayerSwapFlag = false;

		// Local callback to runPlayer swap once playerElement has metadata
		function runPlayerSwap() {
			// Don't run player swap twice
			if( ranPlayerSwapFlag ){
				return ;
			}
			ranPlayerSwapFlag = true;
			mw.log("EmbedPlayer::runPlayerSwap::" + $( playerElement ).attr('id') );

			var playerInterface = new mw.EmbedPlayer( playerElement );
			var swapPlayer = swapEmbedPlayerElement( playerElement, playerInterface );			

			// Trigger the EmbedPlayerNewPlayer for embedPlayer interface
			mw.log("EmbedPlayer::EmbedPlayerNewPlayer:trigger " + playerInterface.id );
			$( mw ).trigger ( 'EmbedPlayerNewPlayer', $( '#' + playerInterface.id ).get(0) );

			// Issue the checkPlayerSources call to the new player
			// interface: make sure to use the element that is in the DOM:
			$( '#' + playerInterface.id ).get(0).checkPlayerSources();
		}

		if( waitForMeta && mw.getConfig('EmbedPlayer.WaitForMeta' ) ) {
			mw.log('EmbedPlayer::WaitForMeta ( video missing height (' +
					$( playerElement ).attr('height') + '), width (' +
					$( playerElement ).attr('width') + ') or duration: ' +
					$( playerElement ).attr('duration')
			);
			$( playerElement ).bind("loadedmetadata", runPlayerSwap );

			// Time-out of 5 seconds ( maybe still playable but no timely
			// metadata )
			setTimeout( runPlayerSwap, 5000 );
			return ;
		} else {
			runPlayerSwap();
			return ;
		}
	};

	/**
	 * Check if we should wait for metadata.
	 *
	 * @return 	true if the size is "likely" to be updated by waiting for metadata 
	 * 			false if the size has been set via an attribute or is already loaded
	 */
	var waitForMetaCheck = function( playerElement ){
		var waitForMeta = false;
			
		// Don't wait for metadata for non html5 media elements
		if( !playerElement ){
			return false;
		}
		if( !playerElement.tagName || ( playerElement.tagName.toLowerCase() != 'audio'  && playerElement.tagName.toLowerCase() != 'video' ) ){
			return false;
		}
		// If we don't have a native player don't wait for metadata
		if( !mw.EmbedTypes.getMediaPlayers().isSupportedPlayer( 'oggNative') &&
			!mw.EmbedTypes.getMediaPlayers().isSupportedPlayer( 'webmNative') &&
			!mw.EmbedTypes.getMediaPlayers().isSupportedPlayer( 'h264Native' ) )
		{
			return false;
		}


		var width = $( playerElement ).css( 'width' );
		var height = $( playerElement ).css( 'height' );

		// Css video defaults ( firefox ) 
		if( $( playerElement ).css( 'width' ) == '300px' &&
				$( playerElement ).css( 'height' ) == '150px' 			
		){
			waitForMeta = true;
		} else {
			// Check if we should wait for duration:
			if( $( playerElement ).attr( 'duration') ||
				$( playerElement ).attr('data-durationhint')
			){
				// height, width and duration set; do not wait for meta data:
				return false;
			} else {
				waitForMeta = true;
			}
		}

		// Firefox ~ sometimes~ gives -1 for unloaded media
		if ( $(playerElement).attr( 'width' ) == -1 || $(playerElement).attr( 'height' ) == -1 ) {
			waitForMeta = true;
		}

		// Google Chrome / safari gives 0 width height for unloaded media
		if( $(playerElement).attr( 'width' ) === 0 ||
			$(playerElement).attr( 'height' ) === 0
		) {
			waitForMeta = true;
		}

		// Firefox default width height is ~sometimes~ 150 / 300
		if( this.height == 150 && this.width == 300 ){
			waitForMeta = true;
		}

		// Make sure we have a src attribute or source child
		// ( i.e not a video tag to be dynamically populated or looked up from
		// xml resource description )
		if( waitForMeta &&
			(
				$( playerElement ).attr('src') ||
				$( playerElement ).find("source[src]").length !== 0
			)
		) {
			// Detect src type ( if no type set )
			return true;
		} else {
			// playerElement is not likely to update its meta data ( no src )
			return false;
		}
	};

	/**
	 * swapEmbedPlayerElement
	 *
	 * Takes a video element as input and swaps it out with an embed player interface
	 *
	 * @param {Element}
	 *      targetElement Element to be swapped
	 * @param {Object}
	 *      playerInterface Interface to swap into the target element
	 */
	var swapEmbedPlayerElement =  function( targetElement, playerInterface ) {
		mw.log( 'EmbedPlayer::swapEmbedPlayerElement: ' + targetElement.id );
		// Create a new element to swap the player interface into
		var swapPlayerElement = document.createElement('div');

		// Add a class that identifies all embedPlayers: 
		$( swapPlayerElement ).addClass( 'mwEmbedPlayer' );
		
		// Get properties / methods from playerInterface:
		for ( var method in playerInterface ) {
			if ( method != 'readyState' ) { // readyState crashes IE ( don't include )
				swapPlayerElement[ method ] = playerInterface[ method ];
			}
		}
		// Check if we are using native controls or Persistent player ( should keep the video embed around )
		if( playerInterface.useNativePlayerControls() || playerInterface.isPersistentNativePlayer() ) {
			$( targetElement )
			.attr( 'id', playerInterface.pid )
			.addClass( 'nativeEmbedPlayerPid' )
			.show()
			.after(
				$( swapPlayerElement ).css( 'display', 'none' )
			);

		} else {
			$( targetElement ).replaceWith( swapPlayerElement );
		}


		// Set swapPlayerElement has height / width set and set to loading:
		$( swapPlayerElement ).css( {
			'width' : playerInterface.width + 'px',
			'height' : playerInterface.height + 'px'
		} );

		// If we don't already have a loadSpiner add one:
		if( $('#loadingSpinner_' + playerInterface.id ).length == 0 ){
			if( playerInterface.useNativePlayerControls() || playerInterface.isPersistentNativePlayer() ) {
				var $spinner = $( targetElement )
					.getAbsoluteOverlaySpinner();
			}else{
				var $spinner = $( swapPlayerElement ).getAbsoluteOverlaySpinner();
			}
			$spinner.attr('id', 'loadingSpinner_' + playerInterface.id );
		}
		return swapPlayerElement;
	};

	// Add a loader for <div> embed player rewrites: 
	$( playerSelect ).each( function( index, playerElement) {
		
		// Make sure the playerElement has an id:
		if( !$( playerElement ).attr('id') ){
			$( playerElement ).attr( "id", 'mwe_v' + ( index ) );
		}

		// If we are dynamically embedding on a "div" check if we can
		// add a poster image behind the loader:
		if( playerElement.nodeName.toLowerCase() == 'div'
			&& ( attributes.poster || $(playerElement).attr( 'poster' ) ) ){
			var posterSrc = ( attributes.poster ) ? attributes.poster : $(playerElement).attr( 'poster' );

			// Set image size:
			var width = $( playerElement ).width();
			var height = $( playerElement ).height();
			if( !width ){
				var width = ( attributes.width ) ? attributes.width : '100%';
			}
			if( !height ){
				var height = ( attributes.height ) ? attributes.height : '100%';
			}

			mw.log('EmbedPlayer:: set loading background: ' + posterSrc);
			$( playerElement ).append(
				$( '<img />' )
				.attr( 'src', posterSrc)
				.css({
					'position' : 'absolute',
					'width' : width,
					'height' : height
				})
			);
		}
	});

	// Create the Global Embed Player Manager ( if not already created )
	// legacy EmbedPlayerManagerReady event ( should remove )
	$( mw ).trigger( 'EmbedPlayerManagerReady' );
	
	// Make sure we have user preference setup for setting preferences on video selection 
	var addedToPlayerManager = false;
	mw.log("EmbedPlayer:: do: " + $( playerSelect ).length + ' players ');
	
	// Add each selected element to the player manager:
	$( playerSelect ).each( function( index, playerElement) {
		// Make sure the video tag was not generated by our library:
		if( $( playerElement ).hasClass( 'nativeEmbedPlayerPid' ) ){
			$('#loadingSpinner_' + $( playerElement ).attr('id') ).remove();
			mw.log( 'EmbedPlayer::$j.embedPlayer skip embedPlayer gennerated video: ' + playerElement );
		} else {
			addedToPlayerManager = true;										
			// Add the player
			addPlayerElement( playerElement );
		}
	});
	if( addedToPlayerManager ){
		if( callback ){
			$( mw ).bind( "playersReadyEvent", callback );
		}
	} else {
		// Run the callback directly if no players were added
		if( callback ){
			callback();
		}
	}
};


/**
 * Base embedPlayer object
 *
 * @param {Element}
 *      element, the element used for initialization.
 * @constructor
 */
mw.EmbedPlayer = function( element ) {
	return this.init( element );
};

mw.EmbedPlayer.prototype = {

	// The mediaElement object containing all mediaSource objects
	'mediaElement' : null,

	// Object that describes the supported feature set of the underling plugin /
	// Support list is described in PlayerControlBuilder components
	'supports': { },

	// Preview mode flag,
	// some plugins don't seek accurately but in preview mode we need
	// accurate seeks so we do tricks like hide the image until its ready
	'previewMode' : false,

	// Ready to play
	// NOTE: we should switch over to setting the html5 video ready state
	'readyToPlay' : false,

	// Stores the loading errors
	'loadError' : false,

	// Thumbnail updating flag ( to avoid rewriting an thumbnail thats already
	// being updated)
	'thumbnail_updating' : false,

	// Poster display flag
	'posterDisplayed' : true,

	// Local variable to hold CMML meeta data about the current clip
	// for more on CMML see: http://wiki.xiph.org/CMML
	'cmmlData': null,

	// Stores the seek time request, Updated by the doSeek function
	'serverSeekTime' : 0,

	// If the embedPlayer is current 'seeking'
	'seeking' : false,

	// Percent of the clip buffered:
	'bufferedPercent' : 0,

	// Holds the timer interval function
	'monitorTimerId' : null,

	// Buffer flags
	'bufferStartFlag' : false,
	'bufferEndFlag' : false,
	
	// For supporting media fragments stores the play end time 
	'pauseTime' : null,
	
	// On done playing
	'donePlayingCount' : 0
	,
	// if player events should be Propagated
	'_propagateEvents': true,
	
	// If the onDone interface should be displayed
	'onDoneInterfaceFlag': true,


	/**
	 * embedPlayer 
	 * 
	 * @constructor
	 *
	 * @param {Element}
	 *      element DOM element that we are building the player interface for.
	 */
	init: function( element ) {
		var _this = this;
		mw.log('EmbedPlayer: initEmbedPlayer: ' + $(element).width() );
		
		var playerAttributes = mw.getConfig( 'EmbedPlayer.Attributes' );
		
		// Setup the player Interface from supported attributes:
		for ( var attr in playerAttributes ) {
			// We can't use $(element).attr( attr ) because we have to check for boolean attributes: 
			if ( element.getAttribute( attr ) != null ) {
				// boolean attributes
				if( element.getAttribute( attr ) == '' ){
					this[ attr ] = true;
				} else {
					this[ attr ] = element.getAttribute( attr );
				}
			} else {
				this[attr] = playerAttributes[attr];
			}
			// string -> boolean
			if( this[ attr ] == "false" ) this[attr] = false;
			if( this[ attr ] == "true" ) this[attr] = true;
		}
		// Hide "controls" if using native player controls:
		if( this.useNativePlayerControls() ){
			_this.controls = false;
		}
		if ( $( element ).attr( 'poster' ) ) {
			_this.poster = $( element ).attr( 'poster' );
		}

		// Set the skin name from the class
		var	sn = $(element).attr( 'class' );

		if ( sn && sn != '' ) {
			var skinList = mw.getConfig('EmbedPlayer.SkinList');
			for ( var n = 0; n < skinList.length; n++ ) {
				if ( sn.indexOf( skinList[n].toLowerCase() ) !== -1 ) {
					this.skinName = skinList[ n ];
				}
			}
		}

		// Set the default skin if unset:
		if ( !this.skinName ) {
			this.skinName = mw.getConfig( 'EmbedPlayer.DefaultSkin' );
		}
		if( !this.monitorRate ){
			this.monitorRate = mw.getConfig( 'EmbedPlayer.MonitorRate' );
		}

		// Make sure startOffset is cast as an float:
		if ( this.startOffset && this.startOffset.split( ':' ).length >= 2 ) {
			this.startOffset = parseFloat( mw.npt2seconds( this.startOffset ) );
		}

		// Make sure offset is in float:
		this.startOffset = parseFloat( this.startOffset );

		// Set the source duration ( if provided in the element metaData or
		// data-durationhint )
		if ( $( element ).attr( 'duration' ) ) {
			_this.duration = $( element ).attr( 'duration' );
		}

		if ( !_this.duration && $( element ).attr( 'data-durationhint' ) ) {
			_this['data-durationhint'] = $( element ).attr( 'data-durationhint' );
			// Convert duration hint if needed:
			_this.duration = mw.npt2seconds( _this['data-durationhint'] );
		}

		// Make sure duration is a float:
		this.duration = parseFloat( this.duration );
		mw.log( 'EmbedPlayer::mediaElement:' + this.id + " duration is: " + this.duration );

		// Set the player size attributes based loaded video element:
		this.loadPlayerSize( element );
		// Set the plugin id
		this.pid = 'pid_' + this.id;

		// Grab any innerHTML and set it to missing_plugin_html
		// NOTE: we should strip "source" tags instead of checking and skipping
		if ( element.innerHTML != '' && element.getElementsByTagName( 'source' ).length == 0 ) {
			// mw.log( 'innerHTML: ' + element.innerHTML );
			this.user_missing_plugin_html = element.innerHTML;
		}
		// Add the mediaElement object with the elements sources:
		this.mediaElement = new mediaElement( element );
	},
	/**
	 * Stop events from Propagation and blocks interface updates and trigger events.
	 * @return
	 */
	stopEventPropagation: function(){
		this.stopMonitor();
		this._propagateEvents = false;
	},
	/**
	 * Restores event propagation 
	 * @return
	 */
	restoreEventPropagation: function(){
		this._propagateEvents = true;
		this.startMonitor();
	},
	
	enableSeekBar: function(){
		this.controlBuilder.enableSeekBar();
		$( this ).trigger( 'onEnableSeekBar');
	},
	disableSeekBar: function(){
		this.controlBuilder.disableSeekBar();
		$( this ).trigger( 'ondisableSeekBar');
	},
	
	/**
	 * For plugin-players to update supported features
	 */
	updateFeatureSupport: function(){
		$( this ).trigger('updateFeatureSupportEvent', this.supports );
		return ;
	},

	/**
	 * Set the width & height from css style attribute, element attribute, or by
	 * default value if no css or attribute is provided set a callback to
	 * resize.
	 *
	 * Updates this.width & this.height
	 *
	 * @param {Element}
	 *      element Source element to grab size from
	 */
	loadPlayerSize: function( element ) {
		this.height = $(element).css( 'height' );
		this.width = $(element).css( 'width' );
		// Special check for chrome 100% with re-mapping to 32px 
		// ( hopefully no one embeds video at 32x32 )
		if( this.height == '32px' || this.height =='32px' ){
			this.width = '100%';
			this.height = '100%';
		}
		mw.log('EmbedPlayer::loadPlayerSize: css size:' + this.width + ' h: '  + this.height);
		
		// Set to parent size ( resize events will cause player size updates)
		if( this.height.indexOf('100%') != -1 || this.width.indexOf('100%') != -1 ){
			$relativeParent = $(element).parents().filter(function() {
				 // reduce to only relative position or "body" elements
				 return $(this).is('body') || $(this).css('position') == 'relative';
			}).slice(0,1); // grab only the "first"
			this.width = $relativeParent.width();
			this.height = $relativeParent.height();
		}
		// Make sure height and width are a number
		this.height = parseInt( this.height );
		this.width = parseInt( this.width );

		// Set via attribute if CSS is zero or NaN and we have an attribute value:
		this.height = ( this.height==0 || isNaN( this.height )
				&& $(element).attr( 'height' ) ) ?
						parseInt( $(element).attr( 'height' ) ): this.height;
		this.width = ( this.width == 0 || isNaN( this.width )
				&& $(element).attr( 'width' ) )?
						parseInt( $(element).attr( 'width' ) ): this.width;


		// Special case for audio
		// Firefox sets audio height to "0px" while webkit uses 32px .. force
		// zero:
		if( element.tagName.toLowerCase() == 'audio' && this.height == '32' ) {
			this.height = 0;
		}

		// Use default aspect ration to get height or width ( if rewriting a
		// non-audio player )
		if( element.tagName.toLowerCase() != 'audio' && this.videoAspect ) {
			var aspect = this.videoAspect.split( ':' );
			if( this.height && !this.width ) {
				this.width = parseInt( this.height * ( aspect[0] / aspect[1] ) );
			}
			if( this.width && !this.height ) {
				var apectRatio = ( aspect[1] / aspect[0] );
				this.height = parseInt( this.width * ( aspect[1] / aspect[0] ) );
			}
		}

		// On load sometimes attr is temporally -1 as we don't have video metadata yet.
		// or in IE we get NaN for width height
		//
		// NOTE: browsers that do support height width should set "waitForMeta" flag in addElement
		if( ( isNaN( this.height )|| isNaN( this.width ) ) ||
			( this.height == -1 || this.width == -1 ) ||
				// Check for firefox defaults
				// Note: ideally firefox would not do random guesses at css
				// values
				( (this.height == 150 || this.height == 64 ) && this.width == 300 )
			) {
			var defaultSize = mw.getConfig( 'EmbedPlayer.DefaultSize' ).split( 'x' );
			if( isNaN( this.width ) ){
				this.width = defaultSize[0];
			}

			// Special height default for audio tag ( if not set )
			if( element.tagName.toLowerCase() == 'audio' ) {
				this.height = 0;
			}else{
				this.height = defaultSize[1];
			}
		}		
	},
	/**
	 * Resize the player to a new size preserving aspect ratio Wraps the
	 * controlBuilder.resizePlayer function
	 */
	resizePlayer: function( size , animate, callback){
		mw.log("EmbedPlayer::resizePlayer:" + size.width + ' x ' + size.height );

		// Check if we are native display then resize the playerElement directly
		if( this.useNativePlayerControls() ){
			if( animate ){
				$( this.getPlayerElement() ).animate( size , callback);
			} else {
				$( this.getPlayerElement() ).css( size );
				if( callback ) {
					callback();
				}
			}
		} else {
			this.controlBuilder.resizePlayer( size, animate, callback);
		}
		$( this ).trigger( 'onResizePlayer', [size, animate] );
	},

	/**
	 * Get the player pixel width not including controls
	 *
	 * @return {Number} pixel height of the video
	 */
	getPlayerWidth: function() {
		return $( this ).width();
	},

	/**
	 * Get the player pixel height not including controls
	 *
	 * @return {Number} pixel height of the video
	 */
	getPlayerHeight: function() {
		return $( this ).height();
	},

	/**
	 * Check player for sources. If we need to get media sources form an
	 * external file that request is issued here
	 */
	checkPlayerSources: function() {
		mw.log( 'EmbedPlayer::checkPlayerSources: ' + this.id );
		var _this = this;

		// Allow plugins to block on sources lookup ( cases where we just have an api key for example )
		$( _this ).triggerQueueCallback( 'CheckPlayerSourcesEvent', function(){
			_this.setupSourcePlayer();
		});
	},
	
	/**
	 * Empty the player sources
	 */
	emptySources: function(){
		if( this.mediaElement ){
			this.mediaElement.sources = [];
			this.mediaElement.selectedSource = null;
		}
	},

	/**
	 * Insert and play a video source ( useful for ads or bumper videos )
	 *
	 * Only works while video is in active play back. Only tested with native
	 * playback atm.
	 */
	switchPlaySrc: function( source ){
		mw.log("Error: only native playback supports insertAndPlaySource right now");
	},

	/**
	 * Set up the select source player
	 *
	 * issues autoSelectSource call
	 *
	 * Sets load error if no source is playable
	 */
	setupSourcePlayer: function() {
		mw.log("EmbedPlayer::setupSourcePlayer: " + this.id + ' sources: ' + this.mediaElement.sources.length );
		
		// Autoseletct the media source
		this.mediaElement.autoSelectSource();

		// Auto select player based on default order
		if ( !this.mediaElement.selectedSource ) {
			mw.log( 'setupSourcePlayer:: no sources, type:' + this.type );
		} else {
			this.selectedPlayer = mw.EmbedTypes.getMediaPlayers().defaultPlayer( this.mediaElement.selectedSource.mimeType );
		}

		if ( this.selectedPlayer ) {
			// Inherit the playback system of the selected player:
			this.inheritEmbedPlayer();
		} else {
			this.showPluginMissingHTML();
		}
	},

	/**
	 * Load and inherit methods from the selected player interface
	 *
	 * @param {Function}
	 *      callback Function to be called once playback-system has been
	 *      inherited
	 */
	inheritEmbedPlayer: function( callback ) {
		mw.log( "EmbedPlayer::inheritEmbedPlayer:duration is: " + this.getDuration() + ' p: ' + this.id );

		// Clear out any non-base embedObj methods:
		if ( this.instanceOf ) {
			eval( 'var tmpObj = mw.EmbedPlayer' + this.instanceOf );
			for ( var i in tmpObj ) { // for in loop oky for object
				if ( this[ 'parent_' + i ] ) {
					this[i] = this[ 'parent_' + i];
				} else {
					this[i] = null;
				}
			}
		}

		// Set up the new embedObj
		mw.log( 'EmbedPlayer::inheritEmbedPlayer: embedding with ' + this.selectedPlayer.library );
		var _this = this;

		// Load the selected player
		this.selectedPlayer.load( function() {
			mw.log( 'EmbedPlayer::inheritEmbedPlayer ' + _this.selectedPlayer.library + " player loaded for " + _this.id );

			// Get embed library player Interface
			var playerInterface = mw[ 'EmbedPlayer' + _this.selectedPlayer.library ];

			for ( var method in playerInterface ) {
				if ( _this[method] && !_this['parent_' + method] ) {
					_this['parent_' + method] = _this[method];
				}
				_this[ method ] = playerInterface[method];
			}

			// Update feature support
			_this.updateFeatureSupport();

			_this.getDuration();
			
			// Run player display with timeout to avoid function stacking 
			setTimeout(function(){
				_this.showPlayer();
	
				// Run the callback if provided
				if ( typeof callback == 'function' ){
					callback();
				}
			},0);
			
		} );
	},

	/**
	 * Select a player playback system
	 *
	 * @param {Object}
	 *      player Player playback system to be selected player playback
	 *      system include vlc, native, java etc.
	 */
	selectPlayer: function( player ) {
		var _this = this;
		if ( this.selectedPlayer.id != player.id ) {
			this.selectedPlayer = player;
			this.inheritEmbedPlayer( function(){
				// Hide / remove track container
				_this.$interface.find( '.track' ).remove();
				// We have to re-bind hoverIntent ( has to happen in this scope
				// )
				if( _this.controls && _this.controlBuilder.checkOverlayControls() ){
					_this.controlBuilder.showControlBar();
					_this.$interface.hoverIntent({
						'sensitivity': 4,
						'timeout' : 2000,
						'over' : function(){
							_this.controlBuilder.showControlBar();
						},
						'out' : function(){
							_this.controlBuilder.hideControlBar();
						}
					});
				}
			});
		}
	},

	/**
	 * Get a time range from the media start and end time
	 *
	 * @return start_npt and end_npt time if present
	 */
	getTimeRange: function() {
		var end_time = (this.controlBuilder.longTimeDisp)? '/' + mw.seconds2npt( this.getDuration() ) : '';
		var default_time_range = '0:00' + end_time;
		if ( !this.mediaElement )
			return default_time_range;
		if ( !this.mediaElement.selectedSource )
			return default_time_range;
		if ( !this.mediaElement.selectedSource.end_npt )
			return default_time_range;
		return this.mediaElement.selectedSource.start_npt + this.mediaElement.selectedSource.end_npt;
	},

	/**
	 * Get the duration of the embed player
	 */
	getDuration: function() {
		return this.duration;
	},

	/**
	 * Get the player height
	 */
	getHeight: function() {
		return this.height;
	},

	/**
	 * Get the player width
	 */
	getWidth: function(){
		return this.width;
	},

	/**
	 * Check if the selected source is an audio element:
	 */
	isAudio: function(){
		return ( this.mediaElement.selectedSource.mimeType.indexOf('audio/') !== -1 );
	},

	/**
	 * Get the plugin embed html ( should be implemented by embed player
	 * interface )
	 */
	doEmbedHTML: function() {
		return 'Error: function doEmbedHTML should be implemented by embed player interface ';
	},

	/**
	 * Seek function ( should be implemented by embedPlayer interface
	 * playerNative, playerKplayer etc. ) embedPlayer doSeek only handles URL
	 * time seeks
	 */
	doSeek: function( percent ) {
		var _this = this;

		this.seeking = true;

		// See if we should do a server side seek ( player independent )
		if ( this.supportsURLTimeEncoding() ) {
			mw.log( 'EmbedPlayer::doSeek:: updated serverSeekTime: ' + mw.seconds2npt ( this.serverSeekTime ) +
					' currentTime: ' + _this.currentTime );
			// make sure we need to seek: 
			if( _this.currentTime == _this.serverSeekTime ){
				return ;
			}
			
			this.stop();
			this.didSeekJump = true;
			// Make sure this.serverSeekTime is up-to-date:
			this.serverSeekTime = mw.npt2seconds( this.start_npt ) + parseFloat( percent * this.getDuration() );
			// Update the slider
			this.updatePlayHead( percent );
		}

		// Do play request in 100ms ( give the dom time to swap out the embed player )
		setTimeout( function() {
			_this.seeking = false;
			_this.play();
			_this.monitor();
		}, 100 );

		// Run the onSeeking interface update
		// NOTE controlBuilder should really bind to html5 events rather
		// than explicitly calling it or inheriting stuff.
		this.controlBuilder.onSeek();
	},

	/**
	 * Seeks to the requested time and issues a callback when ready (should be
	 * overwritten by client that supports frame serving)
	 */
	setCurrentTime: function( time, callback ) {
		mw.log( 'Error: setCurrentTime not overriden' );
	},

	/**
	 * On clip done action. Called once a clip is done playing
	 */
	onClipDone: function() {		
		var _this = this;
		// don't run onclipdone if _propagateEvents is off
		if( !_this._propagateEvents ){
			return ;
		}
		mw.log( 'EmbedPlayer::onClipDone:' + this.id + ' doneCount:' + this.donePlayingCount + ' stop state:' +this.isStopped() );
		// Only run stopped once:
		if( !this.isStopped() ){
			// Stop the monitor and event propagation
			this.stopEventPropagation();

			// Show the control bar:
			this.controlBuilder.showControlBar();

			// Update the clip done playing count:
			this.donePlayingCount ++;

			// Run the ended trigger ( allow the ended object to prevent default actions )
			mw.log("EmbedPlayer::onClipDone:Trigger ended");
			
			// TOOD we should improve the end event flow
			$( this ).trigger( 'ended' );
			
			// if the ended event did not trigger more timeline actions run the actual stop:
			if( this.onDoneInterfaceFlag ){
				this.stop();
				// restore event propagation 
				this.restoreEventPropagation();
				// Check if we have the "loop" property set
				if( this.loop ) {
					this.play();
					return;
				}

				// Stop the clip (load the thumbnail etc)
				this.serverSeekTime = 0;
				this.updatePlayHead( 0 );

				// Make sure we are not in preview mode( no end clip actions in
				// preview mode)
				if ( this.previewMode ) {
					return ;
				}

				// Do the controlBuilder onClip done interface
				this.controlBuilder.onClipDone();
			}
		}
	},


	/**
	 * Shows the video Thumbnail, updates pause state
	 */
	showThumbnail: function() {
		var _this = this;
		mw.log( 'EmbedPlayer::showThumbnail' + this.posterDisplayed );

		// Close Menu Overlay:
		this.controlBuilder.closeMenuOverlay();

		// update the thumbnail html:
		this.updatePosterHTML();

		this.paused = true;
		this.posterDisplayed = true;
		// Make sure the controlBuilder bindings are up-to-date
		this.controlBuilder.addControlBindings();

		// Once the thumbnail is shown run the mediaReady trigger (if not using native controls)
		// Native controls has a native mediaLoaded event
		if( !this.useNativePlayerControls() ){
			mw.log("mediaLoaded");
			$( this ).trigger( 'mediaLoaded' );
		}
	},

	/**
	 * Show the player
	 */
	showPlayer: function () {
		mw.log( 'EmbedPlayer:: Show player: ' + this.id + ' interace: w:' + this.width + ' h:' + this.height );
		var _this = this;
		// Set-up the local controlBuilder instance:
		this.controlBuilder = new mw.PlayerControlBuilder( this );
		var _this = this;
		
		// Remove loader
		$('#loadingSpinner_' + this.id ).remove();	
		
		// Make sure we have mwplayer_interface
		if( $( this ).parent( '.mwplayer_interface' ).length == 0 ) {
			// Select "player"
			$( this ).wrap(
				$('<div>')
				.addClass( 'mwplayer_interface ' + this.controlBuilder.playerClass )
				.css({
					'width' : this.width + 'px',
					'height' : this.height + 'px',
					'position' : 'relative'
				})
			)
			// position the "player" absolute inside the relative interface
			// parent:
			.css('position', 'absolute');
		}
		
		// Set up local jQuery object reference to "mwplayer_interface"
		this.$interface = $( this ).parent( '.mwplayer_interface' );
		// If a isPersistentNativePlayer ( overlay the controls )
		if( !this.useNativePlayerControls() && this.isPersistentNativePlayer() ){
			this.$interface.css({
				'position' : 'absolute',
				'top' : '0px',
				'left' : '0px',
				'background': null
			});
		
			if( this.isPersistentNativePlayer() && !_this.controlBuilder.checkOverlayControls() ){
				// if Persistent native player always give it the player height
				$('#' + this.pid ).css('height', this.height - _this.controlBuilder.height );
			}
			$( this ).show();
			this.controls = true;
		};
		
		if(  !this.useNativePlayerControls() && !this.isPersistentNativePlayer() && !_this.controlBuilder.checkOverlayControls() ){
			// Update the video size per available control space.
			$(this).css('height', this.height - _this.controlBuilder.height );
		}
		
		// Update Thumbnail for the "player"
		this.updatePosterHTML();
		
		// Add controls if enabled:
		if ( this.controls ) {
			this.controlBuilder.addControls();
		} else {
			// Need to think about this some more...
			// Interface is hidden if controls are "off"
			this.$interface.hide();
		}

		// Update temporal url if present
		this.updateTemporalUrl();
		
		
		if ( this.autoplay ) {
			mw.log( 'EmbedPlayer::showPlayer::activating autoplay' );
			// Issue a non-blocking play request
			setTimeout(function(){
				_this.play();
			},1);
		}

	},
	/**
	 * Media fragments handler based on: 
	 * http://www.w3.org/2008/WebVideo/Fragments/WD-media-fragments-spec/#fragment-dimensions
	 * 
	 * We support seconds and npt ( normal play time ) 
	 * 
	 * Updates the player per fragment url info if present
	 * 
	 */
	updateTemporalUrl: function(){
		var sourceHash = /[^\#]+$/.exec( this.getSrc() ).toString();
		if( sourceHash.indexOf('t=') === 0 ){
			// parse the times 
			var times = sourceHash.substr(2).split(',');
			if( times[0] ){
				// update the current time
				this.currentTime = mw.npt2seconds( times[0].toString() );
			}
			if( times[1] ){
				this.pauseTime = mw.npt2seconds( times[1].toString() );
				// ignore invalid ranges: 
				if( this.pauseTime < this.currentTime ){
					this.pauseTime = null;
				}
			}
			// Update the play head
			this.updatePlayHead( this.currentTime / this.duration );
			// Update status: 
			this.controlBuilder.setStatus( mw.seconds2npt( this.currentTime ) );
		}
	},
	/**
	 * Get missing plugin html (check for user included code)
	 *
	 * @param {String}
	 *      [misssingType] missing type mime
	 */
	showPluginMissingHTML: function( ) {
		mw.log("EmbedPlayer::showPluginMissingHTML");
		// Control builder ( for play button )
		this.controlBuilder = new mw.PlayerControlBuilder( this );					
		
		this.controlBuilder.doWarningBindinng( 'EmbedPlayer.DirectFileLinkWarning',
			gM( 'mwe-embedplayer-download-warn', mw.getConfig('EmbedPlayer.FirefoxLink') )
		);
		// Hide loader
		$('#loadingSpinner_' + this.id ).remove();
		$(this).css('position', 'relative');
		
		// Get mime type for un-supported formats:
		this.updatePosterHTML();		
		
		// Set the play button to the first available source: 
		$(this).find('.play-btn-large')
		.unbind('click')
		.wrap(
			$('<a />').attr( {
				'href': this.mediaElement.sources[0].getSrc(),
				'title' : gM('mwe-embedplayer-play_clip')
			} )
		);
	},

	/**
	 * Update the video time request via a time request string
	 *
	 * @param {String}
	 *      time_req
	 */
	updateVideoTimeReq: function( time_req ) {
		mw.log( 'EmbedPlayer::updateVideoTimeReq:' + time_req );
		var time_parts = time_req.split( '/' );
		this.updateVideoTime( time_parts[0], time_parts[1] );
	},

	/**
	 * Update Video time from provided start_npt and end_npt values
	 *
	 * @param {String}
	 *      start_npt the new start time in npt format
	 * @pamra {String} end_npt the new end time in npt format
	 */
	updateVideoTime: function( start_npt, end_npt ) {
		// update media
		this.mediaElement.updateSourceTimes( start_npt, end_npt );

		// update mv_time
		this.controlBuilder.setStatus( start_npt + '/' + end_npt );

		// reset slider
		this.updatePlayHead( 0 );

		// reset seek_offset:
		if ( this.mediaElement.selectedSource.URLTimeEncoding ) {
			this.serverSeekTime = 0;
		} else {
			this.serverSeekTime = mw.npt2seconds( start_npt );
		}
	},


	/**
	 * Update Thumb time with npt formated time
	 *
	 * @param {String}
	 *      time NPT formated time to update thumbnail
	 */
	updateThumbTimeNPT: function( time ) {
		this.updateThumbTime( mw.npt2seconds( time ) - parseInt( this.startOffset ) );
	},

	/**
	 * Update the thumb with a new time
	 *
	 * @param {Float}
	 *      floatSeconds Time to update the thumb to
	 */
	updateThumbTime:function( floatSeconds ) {
		// mw.log('updateThumbTime:'+floatSeconds);
		var _this = this;
		if ( typeof this.org_thum_src == 'undefined' ) {
			this.org_thum_src = this.poster;
		}
		if ( this.org_thum_src.indexOf( 't=' ) !== -1 ) {
			this.last_thumb_url = mw.replaceUrlParams( this.org_thum_src,
				{
					't' : mw.seconds2npt( floatSeconds + parseInt( this.startOffset ) )
				}
			);
			if ( !this.thumbnail_updating ) {
				this.updatePoster( this.last_thumb_url , false );
				this.last_thumb_url = null;
			}
		}
	},

	/**
	 * Updates the displayed thumbnail via percent of the stream
	 *
	 * @param {Float}
	 *      percent Percent of duration to update thumb
	 */
	updateThumbPerc:function( percent ) {
		return this.updateThumbTime( ( this.getDuration() * percent ) );
	},

	/**
	 * Updates the thumbnail if the thumbnail is being displayed
	 *
	 * @param {String}
	 *      src New src of thumbnail
	 * @param {Boolean}
	 *      quick_switch true switch happens instantly false / undefined
	 *      animated cross fade
	 */
	updatePosterSrc: function( src, quick_switch ) {
		// make sure we don't go to the same url if we are not already updating:
		if ( !this.thumbnail_updating && $( '#img_thumb_' + this.id ).attr( 'src' ) == src )
			return false;
		// if we are already updating don't issue a new update:
		if ( this.thumbnail_updating && $( '#new_img_thumb_' + this.id ).attr( 'src' ) == src )
			return false;

		mw.log( 'update thumb: ' + src );

		if ( quick_switch ) {
			$( '#img_thumb_' + this.id ).attr( 'src', src );
		} else {
			var _this = this;
			// if still animating remove new_img_thumb_
			if ( this.thumbnail_updating == true )
				$( '#new_img_thumb_' + this.id ).stop().remove();

			if ( this.posterDisplayed ) {
				mw.log( 'set to thumb:' + src );
				this.thumbnail_updating = true;
				$( this ).append(
					$('<img />')
					.attr({
						'src' : src,
						'id' : 'new_img_thumb_' + this.id,
						'width' : this.width,
						'height': this.height
					})
					.css( {
						'display' : 'none',
						'position' : 'absolute',
						'z-index' : 2,
						'top' : '0px',
						'left' : '0px'
					})
				);
				// mw.log('appended: new_img_thumb_');
				$( '#new_img_thumb_' + this.id ).fadeIn( "slow", function() {
						// once faded in remove org and rename new:
						$( '#img_thumb_' + _this.id ).remove();
						$( '#new_img_thumb_' + _this.id ).attr( 'id', 'img_thumb_' + _this.id );
						$( '#img_thumb_' + _this.id ).css( 'z-index', '1' );
						_this.thumbnail_updating = false;
						// mw.log("done fadding in "+
						// $('#img_thumb_'+_this.id).attr("src"));

						// if we have a thumb queued update to that
						if ( _this.last_thumb_url ) {
							var src_url = _this.last_thumb_url;
							_this.last_thumb_url = null;
							_this.updatePosterSrc( src_url );
						}
				} );
			}
		}
	},
	// update the video poster: 
	updatePosterSrc: function( posterSrc ){
		this.poster = posterSrc;
	},
	/**
	 * Returns the HTML code for the video when it is in thumbnail mode.
	 * playing, configuring the player, inline cmml display, HTML linkback,
	 * download, and embed code.
	 */
	updatePosterHTML: function () {
		mw.log( 'EmbedPlayer:updatePosterHTML::' + this.id );
		var thumb_html = '';
		var class_atr = '';
		var style_atr = '';
		
		if( this.useNativePlayerControls() ){
			this.showNativePlayer();
			return ;
		}
		
		// Set by default thumb value if not found
		var posterSrc = ( this.poster ) ? this.poster :
						mw.getConfig( 'imagesPath' ) + 'vid_default_thumb.jpg';

		// Update PersistentNativePlayer poster:
		if( this.isPersistentNativePlayer() ){
			$( '#' + this.pid ).attr('poster', posterSrc);		
		} else {		
			// Poster support is not very consistent in browsers
			// use a jpg poster image:
			$( this ).html(
				$( '<img />' )
				.css({
					'position' : 'relative',
					'width' : '100%',
					'height' : '100%'
				})
				.attr({
					'id' : 'img_thumb_' + this.id,
					'src' : posterSrc
				})
				.addClass( 'playerPoster' )
			);
		}
		if ( this.controls && this.controlBuilder
			&& this.height > this.controlBuilder.getComponentHeight( 'playButtonLarge' )
		) {
			$( this ).append(
				this.controlBuilder.getComponent( 'playButtonLarge' )
			);
		}
	},

	/**
	 * Checks if native controls should be used
	 *
	 * @param [player]
	 *      Object Optional player object to check controls attribute
	 * @returns boolean true if the mwEmbed player interface should be used
	 *     false if the mwEmbed player interface should not be used
	 */
	useNativePlayerControls: function() {

		if( this.usenativecontrols === true ){
			return true;
		}				
		if( mw.getConfig('EmbedPlayer.NativeControls') === true ) {
			return true;
		}
		
		// Do some device detection devices that don't support overlays 
		// and go into full screen once play is clicked: 
		if( mw.isAndroid2() || mw.isIpod()  || mw.isIphone() ){
			return true;
		} 
		// iPad can use html controls if its a persistantPlayer in the dom before loading )
		// else it needs to use native controls: 
		if( mw.isIpad() ){
			if( this.isPersistentNativePlayer() && mw.getConfig('EmbedPlayer.EnableIpadHTMLControls') ){
				return false;
			} else {
				// Set warning that your trying to do iPad controls without persistent native player: 
				if( mw.getConfig('EmbedPlayer.EnableIpadHTMLControls') ){
					mw.log("Error:: Trying to set EnableIpadHTMLControls without Persistent Native Player");
				}
				return true;
			}
		}
		return false;
	},
	
	isPersistentNativePlayer: function(){
		return $('#' + this.pid ).hasClass('persistentNativePlayer');		
	},


	/**
	 * Show the native player embed code
	 *
	 * This is for cases where the main library needs to "get out of the way"
	 * since the device only supports a limited subset of the html5 and won't
	 * work with an html javascirpt interface
	 */
	showNativePlayer: function(){
		var _this = this;
		// Empty the player of any child nodes
		$(this).empty();		

		// Remove the player loader spinner if it exists
		$('#loadingSpinner_' + this.id ).remove();

		// Setup the source
		var source = this.mediaElement.getSources( 'video/h264' )[0];
		// Support fake user agent 
		if( !source || !source.src ){
			mw.log( 'Warning: Your probably fakeing the iPhone userAgent ( no h.264 source )' );
			source = this.mediaElement.getSources( 'video/ogg' )[0];
		}
		
		// Setup videoAttribues	
		var videoAttribues = {
			'poster': _this.poster,
			'src' : source.src,
			'controls' : 'true'
		};
		if( this.loop ){
			videoAttribues[ 'loop' ] = 'true';
		}
		var cssStyle = {
			'width' : _this.width,
			'height' : _this.height
		};
		
		// If not a persistentNativePlayer swap the video tag 
		// completely instead of just updating properties:						
		$( '#' + this.pid ).replaceWith(
			_this.getNativePlayerHtml( videoAttribues, cssStyle )
		);
		
		// Bind native events:
		this.applyMediaElementBindings();	
		
		// Android only can play with a special play button ( no native controls
		// persistentNativePlayer has no controls: 		
		if( mw.isAndroid2() ){
			this.addPlayBtnLarge();
		}
		return ;
	},
	addPlayBtnLarge:function(){
		var _this = this;
		var $pid = $( '#' + _this.pid );
		$pid.siblings('.play-btn-large').remove();	 
		$playButton = this.controlBuilder.getComponent('playButtonLarge'); 
		$pid.after(
			$playButton
			.css({
				'left' : parseInt( $pid.position().left ) + parseInt( $playButton.css('left') ),
				'top' : parseInt( $pid.position().top ) +  parseInt( $playButton.css('top') )
			})
		);
	},
	/**
	 * Should be set via native embed support
	 */
	getNativePlayerHtml: function(){
		return $('<div />' )
			.css( 'width', this.getWidth() )
			.html( 'Error: Trying to get native html5 player without native support for codec' );
	},
	/**
	 * Should be set via native embed support
	 */
	applyMediaElementBindings: function(){
		return ;
	},

	/**
	 * Gets code to embed the player remotely for "share" this player links
	 */
	getEmbeddingHTML: function() {
		switch( mw.getConfig( 'EmbedPlayer.ShareEmbedMode' ) ){
			case 'iframe':
				return this.getShareIframeObject();
			break;
			case 'videojs':
				return this.getShareEmbedVideoJs();
			break;
		}
	},

	/**
	* Get the share embed object code
	*
	* NOTE this could probably share a bit more code with getShareEmbedVideoJs
	*/
	getShareIframeObject: function(){
		// allow modules to generate the iframe:
		var iframeEmbedCode ={};
		var iframeUrl = false
		$( this ).trigger( 'GetShareIframeSrc', function( localIframeSrc ){
			if( iframeUrl){
				mw.log("Error multiple modules binding GetShareIframeSrc" );
			}			
			iframeUrl = localIframeSrc;
		});
	
		if( !iframeUrl ){	
			iframeUrl = this.getIframeSourceUrl()
		}		
		
		// Set up embedFrame src path
		var embedCode = '&lt;iframe src=&quot;' + mw.html.escape( iframeUrl ) + '&quot; ';

		// Set width / height of embed object
		embedCode += 'width=&quot;' + this.getPlayerWidth() +'&quot; ';
		embedCode += 'height=&quot;' + this.getPlayerHeight() + '&quot; ';
		embedCode += 'frameborder=&quot;0&quot; ';

		// Close up the embedCode tag:
		embedCode+='&gt;&lt/iframe&gt;';

		// Return the embed code
		return embedCode;
	},
	
	getIframeSourceUrl: function(){
		// Point to raw files: 
		var iframeUrl = mw.getMwEmbedPath() + 'mwEmbedFrame.php?';
		var params = {'src[]':[]};

		
		// Output all the video sources:
		for( var i=0; i < this.mediaElement.sources.length; i++ ){
			var source = this.mediaElement.sources[i];
			if( source.src ) {
                params['src[]'].push(mw.absoluteUrl( source.src ));
			}
		}
		// Output the poster attr
		if( this.poster ){
			params.poster = this.poster;
		}
		

		// Set the skin if set to something other than default
		if( this.skinName ){
			params.skin = this.skinName;
		}

		if( this.duration ) {
			params['data-durationhint'] = parseFloat( this.duration );
		}
		iframeUrl += $j.param( params );
		return iframeUrl;
	},
	
	/**
	 * Get the share embed Video tag code
	 */
	getShareEmbedVideoJs: function(){

		// Set the embed tag type:
		var embedtag = ( this.isAudio() )? 'audio': 'video';

		// Set up the mwEmbed js include:
		var embedCode = '&lt;script type=&quot;text/javascript&quot; ' +
					'src=&quot;' +
					mw.html.escape(
						mw.absoluteUrl(
							mw.getMwEmbedSrc()
						)
					) + '&quot;&gt;&lt;/script&gt' +
					'&lt;' + embedtag + ' ';

		if( this.poster ) {
			embedCode += 'poster=&quot;' +
				mw.html.escape( mw.absoluteUrl( this.poster ) ) +
				'&quot; ';
		}

		// Set the skin if set to something other than default
		if( this.skinName ){
			embedCode += 'class=&quot;' +
				mw.html.escape( this.skinName ) +
				'&quot; ';
		}

		if( this.duration ) {
			embedCode +='data-durationhint=&quot;' + parseFloat( this.duration ) + '&quot; ';
		}

		if( this.width || this.height ){
			embedCode +='style=&quot;';
			embedCode += ( this.width )? 'width:' + this.width +'px;': '';
			embedCode += ( this.height )? 'height:' + this.height +'px;': '';
			embedCode += '&quot; ';
		}

		// TODO move to mediaWiki Support module
		if( this.apiTitleKey ) {
			embedCode += 'apiTitleKey=&quot;' + mw.html.escape( this.apiTitleKey ) + '&quot; ';
			if ( this.apiProvider ) {
				embedCode += 'apiProvider=&quot;' + mw.html.escape( this.apiProvider ) + '&quot; ';
			}
			// close the video tag
			embedCode += '&gt;&lt;/video&gt;';

		} else {
			// Close the video attr
			embedCode += '&gt;';

			// Output all the video sources:
			for( var i=0; i < this.mediaElement.sources.length; i++ ){
				var source = this.mediaElement.sources[i];
				if( source.src ) {
					embedCode +='&lt;source src=&quot;' +
						mw.absoluteUrl( source.src ) +
						'&quot; &gt;&lt;/source&gt;';
				}
			}
			// Close the video tag
			embedCode += '&lt;/video&gt;';
		}

		return embedCode;
	},

	/**
	 * Base Embed Controls
	 */

	/**
	 * The Play Action
	 *
	 * Handles play requests, updates relevant states: seeking =false paused =
	 * false Updates pause button Starts the "monitor"
	 */
	play: function() {
		var _this = this;
		mw.log( "EmbedPlayer:: play: " + this._propagateEvents );
		if( ! this._propagateEvents ){
			return ;
		}
		// Hide any overlay:
		this.controlBuilder.closeMenuOverlay();

		// Check if thumbnail is being displayed and embed html
		if ( this.posterDisplayed ) {
			if ( !this.selectedPlayer ) {
				this.showPluginMissingHTML();
				return;
			} else {
				this.posterDisplayed = false;
				// Hide any button if present: 
				this.$interface.find( '.play-btn-large' ).remove();			
				this.doEmbedHTML();
			}
		}
	
		if( this.paused === true ){
			this.paused = false;			
			// Check if we should Trigger the play event
			mw.log("EmbedPlayer:: trigger play even::" + !this.paused);
			if( ! this.doMethodsAutoTrigger() ) {
				$( this ).trigger( 'play' );
			}
		}
		
		// If we previously finished playing this clip run the "replay hook"
		if( this.donePlayingCount > 0 && !this.paused) {
			mw.log("replayEvent");
			$( this ).trigger( 'replayEvent' );
		}

		this.$interface.find('.play-btn span')
		.removeClass( 'ui-icon-play' )
		.addClass( 'ui-icon-pause' );

		this.$interface.find( '.play-btn' )
		.unbind()
		.buttonHover()
		.click( function( ) {
		 	_this.pause();
		 } )
		.attr( 'title', gM( 'mwe-embedplayer-pause_clip' ) );		
		
		// Start the monitor if not already started
		this.monitor();		
	},
	/**
	 * Base embed pause Updates the play/pause button state.
	 *
	 * There is no general way to pause the video must be overwritten by embed
	 * object to support this functionality.
	 */
	pause: function( event ) {
		var _this = this;		
		// Trigger the pause event if not already paused and using native
		// controls:
		if( this.paused === false ){
			this.paused = true;
			mw.log('EmbedPlayer:trigger pause:' + this.paused);
			if(  ! this.doMethodsAutoTrigger() ){
				$( this ).trigger( 'pause' );
			}
		}

		// update the ctrl "paused state"
		this.$interface.find('.play-btn span' )
		.removeClass( 'ui-icon-pause' )
		.addClass( 'ui-icon-play' );

		this.$interface.find('.play-btn' )
		.unbind('click')
		.buttonHover()
		.click( function() {
			_this.play();
		} )
		.attr( 'title', gM( 'mwe-embedplayer-play_clip' ) );
	},
	// special per browser check for autoTrigger events
	// ideally jQuery would not have this inconsistency. 
	doMethodsAutoTrigger: function(){
		if( $j.browser.mozilla && ! mw.versionIsAtLeast('2.0', $j.browser.version ) ){
			return true;
		}
		return false;
	},

	/**
	 * Maps the html5 load request. There is no general way to "load" clips so
	 * underling plugin-player libs should override.
	 */
	load: function() {
		// should be done by child (no base way to pre-buffer video)
		mw.log( 'baseEmbed:load call' );
	},

	
	/**
	 * Base embed stop
	 *
	 * Updates the player to the stop state shows Thumbnail resets Buffer resets
	 * Playhead slider resets Status
	 */
	stop: function() {
		var _this = this;
		mw.log( 'EmbedPlayer::stop:' + this.id );

		// no longer seeking:
		this.didSeekJump = false;

		// Reset current time and prev time and seek offset
		this.currentTime = this.previousTime = 	this.serverSeekTime = 0;

		this.stopMonitor();
		
		// Issue pause to update interface (only call this parent)
		if( !this.paused ){
			this.paused = true;
			// update the interface
			if ( this['parent_pause'] ) {
				this.parent_pause();
			} else {
				this.pause();
			}
		}
		// Native player controls: 
		if( this.useNativePlayerControls() ){
			this.getPlayerElement().currentTime = 0;
			this.getPlayerElement().pause();
			// If on android when we "stop" we re add the large play button
			if( mw.isAndroid2() ){
				this.addPlayBtnLarge();
			}
		} else {
			// Rewrite the html to thumbnail disp
			this.showThumbnail();
			this.bufferedPercent = 0; // reset buffer state
			this.controlBuilder.setStatus( this.getTimeRange() );
			
			// Reset the playhead
			mw.log("EmbedPlayer::Stop:: Reset play head");
			this.updatePlayHead( 0 );
					
		}
	},

	/**
	 * Base Embed mute
	 *
	 * Handles interface updates for toggling mute. Plug-in / player interface
	 * must handle the actual media player update
	 */
	toggleMute: function() {
		mw.log( 'f:toggleMute:: (old state:) ' + this.muted );
		if ( this.muted ) {
			this.muted = false;
			var percent = this.preMuteVolume;
		} else {
			this.muted = true;
			this.preMuteVolume = this.volume;
			var percent = 0;
		}
		this.setVolume( percent );
		// Update the interface
		this.setInterfaceVolume( percent );
	},

	/**
	 * Update volume function ( called from interface updates )
	 *
	 * @param {float}
	 *      percent Percent of full volume
	 */
	setVolume: function( percent ) {
		// ignore NaN percent:
		if( isNaN( percent ) ){
			return ;
		}
		// Set the local volume attribute
		this.previousVolume = this.volume = percent;

		// Un-mute if setting positive volume
		if( percent != 0 ){
			this.muted = false;
		}

		// Update the playerElement volume
		this.setPlayerElementVolume( percent );

		// mw.log(" setVolume:: " + percent + ' this.volume is: ' +
		// this.volume);
		$( this ).trigger('volumeChanged', percent );
	},

	/**
	 * Updates the interface volume 
	 * 
	 * TODO should move to controlBuilder
	 *
	 * @param {float}
	 *      percent Percentage volume to update interface
	 */
	setInterfaceVolume: function( percent ) {
		if( this.supports[ 'volumeControl' ] &&
			this.$interface.find( '.volume-slider' ).length
		) {
			this.$interface.find( '.volume-slider' ).slider( 'value', percent * 100 );
		}
	},

	/**
	 * Abstract Update volume Method must be override by plug-in / player
	 * interface
	 */
	setPlayerElementVolume: function( percent ) {
		mw.log('Error player does not support volume adjustment' );
	},

	/**
	 * Abstract get volume Method must be override by plug-in / player interface
	 * (if player does not override we return the abstract player value )
	 */
	getPlayerElementVolume: function(){
		// mw.log(' error player does not support getting volume property' );
		return this.volume;
	},

	/**
	 * Abstract get volume muted property must be overwritten by plug-in /
	 * player interface (if player does not override we return the abstract
	 * player value )
	 */
	getPlayerElementMuted: function(){
		// mw.log(' error player does not support getting mute property' );
		return this.muted;
	},

	/**
	 * Passes a fullscreen request to the controlBuilder interface
	 */
	fullscreen: function() {		
		this.controlBuilder.toggleFullscreen();
	},

	/**
	 * Abstract method to be run post embedding the player Generally should be
	 * overwritten by the plug-in / player
	 */
	postEmbedJS:function() {
		return ;
	},

	/**
	 * Checks the player state based on thumbnail display & paused state
	 *
	 * @return {Boolean} true if playing false if not playing
	 */
	isPlaying : function() {
		if ( this.posterDisplayed ) {
			// in stopped state
			return false;
		} else if ( this.paused ) {
			// paused state
			return false;
		} else {
			return true;
		}
	},

	/**
	 * Get paused state
	 *
	 * @return {Boolean} true if playing false if not playing
	 */
	isPaused: function() {
		return this.paused;
	},

	/**
	 * Get Stopped state
	 *
	 * @return {Boolean} true if stopped false if playing
	 */
	isStopped: function() {
		return this.posterDisplayed;
	},

	// TODO temporary hack we need a better stop monitor system
	stopMonitor: function(){
		clearInterval( this.monitorInterval );
		this.monitorInterval = 0;
	},
	// TODO temporary hack we need a better stop monitor system
	startMonitor: function(){
		this.monitor();
	},

	/**
	 * Checks if the currentTime was updated outside of the getPlayerElementTime
	 * function
	 */
	checkForCurrentTimeSeek: function(){
		var _this = this;
		// Check if a javascript currentTime change based seek has occurred
		if( _this.previousTime != _this.currentTime && !this.userSlide && !this.seeking){
			// If the time has been updated and is in range issue a seek
			if( _this.getDuration() && _this.currentTime <= _this.getDuration() ){
				var seekPercent = _this.currentTime / _this.getDuration();
				mw.log("checkForCurrentTimeSeek::" + _this.previousTime + ' != ' +
						 _this.currentTime + " javascript based currentTime update to " +
						 seekPercent + ' == ' + _this.currentTime );
				_this.previousTime = _this.currentTime;
				this.doSeek( seekPercent );
			}
		}
	},

	/**
	 * Monitor playback and update interface components. underling plugin
	 * objects are responsible for updating currentTime
	 */
	monitor: function() {
		var _this = this;
		
		// Check for current time update outside of embed player
		this.checkForCurrentTimeSeek();
		
		
		// Update currentTime via embedPlayer
		_this.currentTime = _this.getPlayerElementTime();		

		// Update any offsets from server seek
		if( _this.serverSeekTime && _this.supportsURLTimeEncoding() ){
			_this.currentTime = parseInt( _this.serverSeekTime ) + parseInt( _this.getPlayerElementTime() );
		}	

		// Update the previousTime ( so we can know if the user-javascript
		// changed currentTime )
		_this.previousTime = _this.currentTime;
		
		if( _this.pauseTime && _this.currentTime >  _this.pauseTime ){
			_this.pause();
			_this.pauseTime = null;
		}


		// Check if volume was set outside of embed player function
		// mw.log( ' this.volume: ' + _this.volume + ' prev Volume:: ' +
		// _this.previousVolume );
		if( Math.round( _this.volume * 100 ) != Math.round( _this.previousVolume * 100 ) ) {
			_this.setInterfaceVolume( _this.volume );
			if( _this._propagateEvents ){
				$( this ).trigger('volumeChanged', _this.volume );
			}
		}

		// Update the previous volume
		_this.previousVolume = _this.volume;

		// Update the volume from the player element
		_this.volume = this.getPlayerElementVolume();

		// update the mute state from the player element
		if( _this.muted != _this.getPlayerElementMuted() && ! _this.isStopped() ){
			mw.log( "EmbedPlayer::monitor: muted does not mach embed player" );
			_this.toggleMute();
			// Make sure they match:
			_this.muted = _this.getPlayerElementMuted();
		}

		//mw.log( 'Monitor:: ' + this.currentTime + ' duration: ' + ( parseInt(
		//		this.getDuration() ) + 1 ) + ' is seeking: ' + this.seeking );
		
		if ( this.currentTime >= 0 && this.duration ) {
			if ( !this.userSlide && !this.seeking ) {
				if ( parseInt( this.startOffset ) != 0 ) {
					// If start offset include that calculation
					this.updatePlayHead( ( this.currentTime - this.startOffset ) / this.duration );
					var et = ( this.controlBuilder.longTimeDisp ) ? '/' + mw.seconds2npt( parseFloat( this.startOffset ) + parseFloat( this.duration ) ) : '';
					this.controlBuilder.setStatus( mw.seconds2npt( this.currentTime ) + et );
				} else {
					this.updatePlayHead( this.currentTime / this.duration );
					// Only include the end time if longTimeDisp is enabled:
					var et = ( this.controlBuilder.longTimeDisp ) ? '/' + mw.seconds2npt( this.duration ) : '';
					this.controlBuilder.setStatus( mw.seconds2npt( this.currentTime ) + et );
				}
			}
			// Check if we are "done"
			var endPresentationTime = ( this.startOffset ) ? ( this.startOffset + this.duration ) : this.duration;
			if ( this.currentTime >= endPresentationTime ) {
				//mw.log( "mWEmbedPlayer::should run clip done :: " + this.currentTime + ' > ' + endPresentationTime );				
				this.onClipDone();				
			}
		} else {
			// Media lacks duration just show end time
			if ( this.isStopped() ) {
				this.controlBuilder.setStatus( this.getTimeRange() );
			} else if ( this.isPaused() ) {
				this.controlBuilder.setStatus( gM( 'mwe-embedplayer-paused' ) );
			} else if ( this.isPlaying() ) {
				if ( this.currentTime && ! this.duration )
					this.controlBuilder.setStatus( mw.seconds2npt( this.currentTime ) + ' /' );
				else
					this.controlBuilder.setStatus( " - - - " );
			} else {
				this.controlBuilder.setStatus( this.getTimeRange() );
			}
		}

		// Update buffer information
		this.updateBufferStatus();

		// run the "native" progress event on the virtual html5 object if set
		if( this.progressEventData ) {
			// mw.log("trigger:progress event on html5 proxy");
			if( _this._propagateEvents ){
				$( this ).trigger( 'progress', this.progressEventData );
			}
		}

		// Call monitor at 250ms interval. ( use setInterval to avoid stacking
		// monitor requests )
		if( ! this.isStopped() ) {
			if( !this.monitorInterval ){
				this.monitorInterval = setInterval( function(){
					if( _this.monitor )
						_this.monitor();
				}, this.monitorRate );
			}
		} else {
			// If stopped "stop" monitor:
			this.stopMonitor();
		}

		// mw.log('trigger:monitor:: ' + this.currentTime );
		if( _this._propagateEvents ){
			$( this ).trigger( 'monitorEvent' );
		}
	},

	/**
	 * Abstract getPlayerElementTime function
	 */
	getPlayerElementTime: function(){
		mw.log("Error: getPlayerElementTime should be implemented by embed library");
	},

	/**
	 * Update the Buffer status based on the local bufferedPercent var
	 */
	updateBufferStatus: function() {

		// Get the buffer target based for playlist vs clip
		$buffer = this.$interface.find( '.mw_buffer' );

		//mw.log(' set bufferd %:' + this.bufferedPercent );
		// Update the buffer progress bar (if available )
		if ( this.bufferedPercent != 0 ) {
			// mw.log('Update buffer css: ' + ( this.bufferedPercent * 100 ) +
			// '% ' + $buffer.length );
			if ( this.bufferedPercent > 1 ){
				this.bufferedPercent = 1;
			}
			$buffer.css({
				"width" : ( this.bufferedPercent * 100 ) + '%'
			});
			$( this ).trigger( 'updateBufferPercent', this.bufferedPercent );
		} else {
			$buffer.css( "width", '0px' );
		}

		// if we have not already run the buffer start hook
		if( this.bufferedPercent > 0 && !this.bufferStartFlag ) {
			this.bufferStartFlag = true;
			mw.log("bufferStart");
			$( this ).trigger( 'bufferStartEvent' );
		}

		// if we have not already run the buffer end hook
		if( this.bufferedPercent == 1 && !this.bufferEndFlag){
			this.bufferEndFlag = true;
			$( this ).trigger( 'bufferEndEvent' );
		}
	},

	/**
	 * Update the player playhead
	 *
	 * @param {Float}
	 *      perc Value between 0 and 1 for position of playhead
	 */
	updatePlayHead: function( perc ) {
		//mw.log( 'EmbedPlayer: updatePlayHead:  '+ perc);
		$playHead = this.$interface.find( '.play_head' );
		if ( this.controls && $playHead.length != 0 ) {
			var val = parseInt( perc * 1000 );
			$playHead.slider( 'value', val );
		}
		$( this ).trigger('updatePlayHeadPercent', perc);
	},


	/**
	 * Helper Functions for selected source
	 */

	/**
	 * Get the current selected media source or first source
	 *
	 * @param {Number} Requested time in seconds to be passed to the server if the server supports 
	 * 			supportsURLTimeEncoding
	 * @return src url
	 */
	getSrc: function( serverSeekTime ) {
		if( serverSeekTime ){
			this.serverSeekTime = serverSeekTime;
		}		
		if( this.currentTime && !this.serverSeekTime){
			this.serverSeekTime = this.currentTime;
		}
		
		// No media element we can't return src
		if( !this.mediaElement ){
			return false;
		}
		
		// If no source selected auto select the source: 
		if( !this.mediaElement.selectedSource ){
			this.mediaElement.autoSelectSource();
		};
		
		// Return selected source: 
		if( this.mediaElement.selectedSource ){
			// get the first source: 
			return this.mediaElement.selectedSource.getSrc( this.serverSeekTime );
		}
		// No selected source return false: 
		return false;
	},

	/**
	 * If the selected src supports URL time encoding
	 *
	 * @return {Boolean} true if the src supports url time requests false if the
	 *     src does not support url time requests
	 */
	supportsURLTimeEncoding: function() {
		var timeUrls = mw.getConfig('EmbedPlayer.EnableURLTimeEncoding') ;
		if( timeUrls == 'none' ){
			return false;
		} else if( timeUrls == 'always' ){
			return this.mediaElement.selectedSource.URLTimeEncoding;
		} else if( timeUrls == 'flash' ){
			if( this.mediaElement.selectedSource.URLTimeEncoding){
				// see if the current selected player is flash: 
				return ( this.instanceOf == 'Kplayer' );
			}
		} else {
			mw.log("Error:: invalid config value for EmbedPlayer.EnableURLTimeEncoding:: " + mw.getConfig('EmbedPlayer.EnableURLTimeEncoding') );
		}
		return false;
	}
};


} )( mediaWiki, jQuery );
