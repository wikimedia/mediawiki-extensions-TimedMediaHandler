/* eslint-disable no-implicit-globals */
/**
 * All JS for loading an actual videoJS player for MediaWiki
 *
 * No matter how the player is loaded, it should eventually go through this class
 * to make sure our customizations are properly applied.
 * This should not have any loaders/hooks itself.
 *
 * @class InlinePlayer
 *
 * @constructor
 * @param {HTMLMediaElement} element
 * @param {Object} options for the videoJS player
 */
function InlinePlayer( element, options ) {
	this.playerConfig = $.extend( {}, options );
	this.videoplayer = element;
	this.$videoplayer = $( element );
	this.isAudio = element.tagName.toLowerCase() === 'audio';
	/**
	 * The videojs instance associated with this inline player.
	 * NOTE: Only available after infusion
	 *
	 * @member {videojs.Player} [videojsPlayer]
	 */
	this.videojsPlayer = null;
}

/**
 * videoJS options for the videojs Html5 Tech plugin.
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {Object} with videoJS options for {videojs.Html5} Tech component
 */
InlinePlayer.html5techOpt = {
	preloadTextTracks: false,
	nativeTextTracks: false
};
/**
 * videoJS player options shared by all uses
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {Object} with videoJS options to initiate a videoJS player with
 */
InlinePlayer.globalConfig = {
	// controls are initially hidden inside the dialog
	// to avoid a flash of the native controls
	controls: true,
	responsive: true,
	language: mw.config.get( 'wgUserLanguage' ),
	controlBar: {
		volumePanel: {
			vertical: true,
			inline: false
		}
	},
	userActions: {
		// https://docs.videojs.com/tutorial-options.html#useractions.hotkeys
		hotkeys: true
	},
	techOrder: [ 'html5' ],
	plugins: {
		infoButton: {}
	},
	html5: InlinePlayer.html5techOpt
};

/**
 * videoJS player options specific to videos
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {Object} with videoJS options to initiate a videoJS player with
 */
InlinePlayer.videoConfig = {
	// Video interace breakpoints
	// Encourage play/pause, fullscreen (to reach all controls) and info
	// Subtitles are too small to read upto 400px or so anyway
	// Resolution is already matched to current size
	breakpoints: {
		// most controls are 40px wide
		// play and fullscreen
		tiny: 159,
		// from 160: play, volume, space, fullscreen [,info]
		xsmall: 199,
		// from 200: play, volume, position, fullscreen [,info]
		small: 239,
		// from 240: play, volume, position, resolution, fullscreen [,info]
		medium: 299,
		// from 300: play, volume, position, time remaining, resolution, fullscreen [,info]
		large: 339,
		// from 340: play, volume, position, time remaining, [CC,] resolution, fullscreen [,info]
		xlarge: 1000,
		huge: 2000
	},
	plugins: {
		videoJsResolutionSwitcher: {
			sourceOrder: true
		}
	}
};

/**
 * videoJS player options specific to audio playback
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type  {Object} with videoJS options to initiate a videoJS player with
 */
InlinePlayer.audioConfig = {
	bigPlayButton: false,
	controlBar: {
		fullscreenToggle: false,
		pictureInPictureToggle: false
	},
	// Audio interface breakpoints
	// play, volume, info and CC are most important here
	breakpoints: {
		// from 40: play only
		tiny: 79,
		// from 80: play and volume
		xsmall: 119,
		// from 120: play, volume [,info]
		small: 199,
		// from 200: play, volume, position [,CC] [,info]
		medium: 259,
		// from 260: play, volume, position, remaining [,CC] [,info]
		large: 339,
		// from 340: play, volume, position, remaining [,CC] [,info]
		xlarge: 1000,
		huge: 2000
	}
};

/**
 * Set to true if the class has been initialized
 *
 * @static
 * @type {boolean}
 */
InlinePlayer.initialized = false;

/**
 * Array of all videojs players that have been created
 *
 * This is filled automatically, and cleaned by
 * the jQuery plugin $.fn.disposeDetachedPlayers
 *
 * @static
 * @type {Array<videojs.Player>}
 */
InlinePlayer.activePlayers = [];

/**
 * Lazy load resources once for this class
 *
 * Should do this as early as you know for sure you will load a player
 * External users may call this early to prefetch resources
 *
 * @static
 */
InlinePlayer.lazyInit = function () {
	if ( InlinePlayer.initialized ) {
		return;
	}
	// Preload the ogv.js module if we're going to need it...
	mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' );
	require( './mw-info-button/mw-info-button.js' );
	require( './videojs-resolution-switcher/videojs-resolution-switcher.js' );

	if ( videojs.browser.IS_SAFARI ) {
		// Html5 on Safari has a broken canPlayType
		var Html5 = videojs.getTech( 'Html5' );
		var originalCanPlayType = Html5.nativeSourceHandler.canPlayType;
		Html5.nativeSourceHandler.canPlayType = function ( mediaType ) {
			switch ( mediaType ) {
				case 'video/webm; codecs="vp9, opus"':
					return ( typeof MediaSource !== 'undefined' && MediaSource.isTypeSupported( 'video/webm; codecs="vp9, opus"' ) ) ? 'probably' : '';
				case 'video/webm; codecs="vp8, vorbis"':
					return ( typeof MediaSource !== 'undefined' && MediaSource.isTypeSupported( 'video/webm; codecs="vp8, vorbis"' ) ) ? 'probably' : '';
			}

			return originalCanPlayType( mediaType );
		};
	}

	if ( mw.OgvJsSupport.isNeeded() ) {
		InlinePlayer.globalConfig.ogvjs = {
			base: mw.OgvJsSupport.basePath(),
			audioContext: mw.OgvJsSupport.initAudioContext()
		};
		InlinePlayer.globalConfig.techOrder.push( 'ogvjs' );
		// ogvjs tech does not support picture in picture
		InlinePlayer.globalConfig.controlBar.pictureInPictureToggle = false;
	}

	// Add translations for the plugins
	// video.js translations don't have region postfixes (yet)
	videojs.addLanguage( mw.config.get( 'wgUserLanguage' ).split( '-' )[ 0 ], {
		'More information': mw.msg( 'videojs-more-information' ),
		Quality: mw.msg( 'videojs-quality' )
	} );

	InlinePlayer.initialized = true;
};

/**
 * Takes the HTMLMediaElement of the InlinePlayer
 * and infuses it with JS (videoJS) to enrich the element.
 */
InlinePlayer.prototype.infuse = function () {
	var inlinePlayer = this;

	if ( this.$videoplayer.closest( '.video-js' ).length ) {
		// This player has already been transformed.
		return;
	}

	InlinePlayer.lazyInit();
	this.playerConfig = $.extend(
		true, // deep
		{},
		InlinePlayer.globalConfig,
		this.isAudio ? InlinePlayer.audioConfig : InlinePlayer.videoConfig,
		this.playerConfig
	);

	// Future interactions go faster if we've preloaded a little
	this.$videoplayer.attr( {
		preload: 'metadata'
	} );

	var nonNativeSources = [];
	var resolutions = [];
	var defaultRes;
	if ( this.isAudio ) {
		// Audio: manipulate source elements to preferred order.
		// This means preferring native-playback over ogv.js-playback
		// so we don't go loading it when we don't need it.
		this.$videoplayer.find( 'source' ).each( function () {
			if ( !inlinePlayer.videoplayer.canPlayType( this.type ) ) {
				nonNativeSources.push( this );
			}
		} );

		nonNativeSources.forEach( function ( source ) {
			$( source ).detach().appendTo( inlinePlayer.$videoplayer );
		} );
	} else {
		resolutions = this.extractResolutions();

		// Pick the first resolution at least the size of the player,
		// unless they're all too small.
		var playerHeight = Math.min(
			// Do not autoselect above 1080p due to bandwidth requirements
			1080,
			// Account for screen density
			this.$videoplayer.height() * window.devicePixelRatio
		);
		if ( !mw.OgvJsSupport.canPlayNatively() ) {
			// Don't pick high-res versions on ogv.js which may be slow.
			playerHeight = Math.min( playerHeight, 480 );
		}
		resolutions.sort( function ( a, b ) {
			return a - b;
		} );
		for ( var i = 0, l = resolutions.length; i < l; i++ ) {
			defaultRes = resolutions[ i ];
			if ( defaultRes >= playerHeight ) {
				break;
			}
		}
		if ( !this.isAudio && defaultRes ) {
			this.playerConfig.plugins.videoJsResolutionSwitcher.default = defaultRes;
		}
	}
	// We remove SRT subtitles tracks as we can't handle them
	this.$videoplayer.find( 'track[type="text/x-srt"]' ).remove();
	// Make sure the menu's can overflow thumbnail frames
	this.$videoplayer.closest( '.thumbinner' ).addClass( 'mw-overflow' );
	this.$videoplayer.addClass( 'video-js' );

	// Launch the player
	this.videojsPlayer = videojs( this.videoplayer, this.playerConfig );
	this.videojsPlayer.ready( function () {
		var videojsPlayer = this;
		InlinePlayer.activePlayers.push( videojsPlayer );
		inlinePlayer.selectDefaultTrack();
		/* More custom stuff goes here */
	} );
};

/**
 * Select a default text track to enable
 * based on user language with fallback to content language
 */
InlinePlayer.prototype.selectDefaultTrack = function () {
	var track,
		tl,
		userLanguageTrack,
		contentLanguageTrack,
		tracks = this.videojsPlayer.textTracks();

	for ( var i = 0; i < tracks.length; i++ ) {
		track = tracks[ i ];

		// For now we only support subtitles
		// Also does not deal with language fallbacks
		if ( track.kind === 'subtitles' ) {
			tl = track.language.toLowerCase();
			if ( tl === mw.config.get( 'wgUserLanguage' ).toLowerCase() ) {
				userLanguageTrack = track;
				break;
			}
			if ( tl === mw.config.get( 'wgUserLanguage' ).toLowerCase().split( '-' )[ 0 ] ) {
				userLanguageTrack = track;
			}
			if ( tl === mw.config.get( 'wgContentLanguage' ).toLowerCase() ) {
				contentLanguageTrack = track;
			}
		}
	}
	if ( userLanguageTrack ) {
		userLanguageTrack.mode = 'showing';
	} else if ( contentLanguageTrack ) {
		contentLanguageTrack.mode = 'showing';
	}
};

/**
 * Extract the list of resolutions from the HTMLSourceElements
 * contained within the HTMLMediaElement.
 *
 * Also sets translated labels and the extracted res
 * as attributes 'label' and 'res' on the HTMLSourceElements,
 * for use by the resolution switcher plugin
 *
 * @private
 * @return {number[]}
 */
InlinePlayer.prototype.extractResolutions = function () {
	// Video: extract the relevant resolutions from source elements
	// and pass them into the videoJsResolutionSwitcher plugin in
	// our preferred order and labeling.
	var resolutions = [];
	this.$videoplayer.find( 'source' ).each( function () {
		// FIXME would be better if we can configure the plugin to make use of our preferred attributes
		var matches,
			$source = $( this ),
			transcodeKey = $source.data( 'transcodekey' ),
			res = parseInt( $source.data( 'height' ), 10 ),
			label = $source.data( 'shorttitle' );

		if ( transcodeKey ) {
			matches = transcodeKey.match( /^(\d+)p\./ );
			if ( matches ) {
				// Video derivative of fixed size.
				res = parseInt( matches[ 1 ], 10 );
				// Messages that can be used here:
				// * timedmedia-resolution-120
				// * timedmedia-resolution-160
				// * timedmedia-resolution-180
				// * timedmedia-resolution-240
				// * timedmedia-resolution-360
				// * timedmedia-resolution-480
				// * timedmedia-resolution-720
				// * timedmedia-resolution-1080
				// * timedmedia-resolution-1440
				// * timedmedia-resolution-2160
				label = mw.message( 'timedmedia-resolution-' + res ).text();
			}
		} else {
			// Original source; sort to top and never auto-select.
			res = 99999;
			label = $source.data( 'shorttitle' );
		}
		$source.attr( 'res', res );
		$source.attr( 'label', label );
		resolutions.push( res );
	} );
	return resolutions;
};

/**
 * @private
 * @param {Object} videoJsOptions Override videoJS defaults of the InlinePlayer
 * @return {jQuery.Promise}
 */
function transformVideoPlayer( videoJsOptions ) {
	var $collection = this;

	return $.Deferred( function ( deferred ) {
		mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' ).then( function () {
			deferred.resolve( $collection.map( function () {
				var inlinePlayer = new InlinePlayer( this, videoJsOptions );
				inlinePlayer.infuse();
				return inlinePlayer;
			} ) );
		} ).catch( function ( e ) {
			mw.log.error( 'Exception occurred: ' + e.message );
			deferred.reject();
		} );
	} ).promise();
}

/**
 * Remove any detached players from previous live previews etc
 *
 * @private
 * @return {jQuery}
 * @chainable
 */
function disposeDetachedPlayers() {
	InlinePlayer.activePlayers = InlinePlayer.activePlayers.filter( function ( player ) {
		if ( !player.el().ownerDocument.body.contains( player.el() ) ) {
			player.dispose();
			return false;
		}
		return true;
	} );
	return this;
}

/**
 * jQuery plugin to load our video player
 *
 * @return {jQuery.Promise}
 */
$.fn.transformVideoPlayer = transformVideoPlayer;

/**
 * jQuery plugin to cleanup all resources of
 * a player which is no longer in the document
 *
 * @return {jQuery}
 * @chainable
 */
$.disposeDetachedPlayers = disposeDetachedPlayers;

module.exports = InlinePlayer;
