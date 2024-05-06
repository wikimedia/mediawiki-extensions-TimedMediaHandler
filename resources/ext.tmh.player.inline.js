'use strict';
const OgvJsSupport = require( 'ext.tmh.OgvJsSupport' );

/**
 * All JS for loading an actual video.js player for MediaWiki
 *
 * No matter how the player is loaded, it should eventually go through this class
 * to make sure our customizations are properly applied.
 * This should not have any loaders/hooks itself.
 */
class InlinePlayer {

	/**
	 * @param {HTMLMediaElement} element
	 * @param {videojs.Options} options for the video.js player
	 */
	constructor( element, options ) {
		/**
		 * The eventual videojs configuration used when loading the videojs player
		 *
		 * @type {videojs.Options}
		 * @private
		 */
		this.playerConfig = Object.assign( {}, options );
		/**
		 * The original audio/video element
		 *
		 * @type {HTMLMediaElement}
		 * @private
		 */
		this.videoplayer = element;
		/**
		 * The original audio/video element as a jquery collection
		 *
		 * @type {jQuery}
		 * @private
		 */
		this.$videoplayer = $( element );
		this.$videoplayer.attr( 'playsinline', '' );
		/**
		 * Whether his player is audio only
		 *
		 * @type {boolean}
		 * @private
		 */
		this.isAudio = element.tagName.toLowerCase() === 'audio';
		/**
		 * The video.js instance associated with this inline player.
		 * NOTE: Only available after infusion
		 *
		 * @type {videojs.Player}
		 * @private
		 */
		this.videojsPlayer = null;
	}

	/**
	 * Lazy load resources once for this class
	 *
	 * Should do this as early as you know for sure you will load a player
	 * External users may call this early to prefetch resources
	 *
	 * @private
	 */
	static lazyInit() {
		if ( InlinePlayer.initialized ) {
			return;
		}
		require( './mw-info-button/mw-info-button-plugin.js' );
		require( './videojs-resolution-switcher/videojs-resolution-switcher.js' );
		require( './mw-subtitles-button/mw-subtitles-create.js' );
		require( './mw-subtitles-button/mw-subtitles-button.js' );
		require( './mw-endcard/mw-endcard.js' );

		// Add translations for the plugins
		// video.js translations don't have region postfixes (yet)
		videojs.addLanguage( mw.config.get( 'wgUserLanguage' ).split( '-' )[ 0 ], {
			'More information': mw.msg( 'videojs-more-information' ),
			Quality: mw.msg( 'videojs-quality' ),
			'Create captions': mw.msg( 'videojs-captions-create' ),
			'Create subtitles': mw.msg( 'videojs-subtitles-create' )
		} );

		InlinePlayer.initialized = true;
	}

	/**
	 * Takes the HTMLMediaElement of the InlinePlayer
	 * and infuses it with JS (videoJS) to enrich the element.
	 *
	 * @return {jQuery.Promise<videojs.Player>|undefined}
	 */
	infuse() {
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

		if ( !OgvJsSupport.isMediaNativelySupported( this.videoplayer ) ) {
			this.playerConfig.ogvjs = {
				base: OgvJsSupport.basePath(),
				audioContext: OgvJsSupport.initAudioContext()
			};
			this.playerConfig.techOrder.push( 'ogvjs' );
			// ogvjs tech does not support picture in picture
			this.playerConfig.controlBar.pictureInPictureToggle = false;
		}

		// Future interactions go faster if we've preloaded a little
		this.$videoplayer.attr( {
			preload: 'metadata'
		} );

		/** @type {HTMLSourceElement[]} */
		const nonNativeSources = [];
		let resolutions = [];
		let defaultRes;
		if ( this.isAudio ) {
			// Audio: manipulate source elements to preferred order.
			// This means preferring native-playback over ogv.js-playback
			// so we don't go loading it when we don't need it.
			this.$videoplayer.find( 'source' ).each( ( _index, source ) => {
				if ( !this.videoplayer.canPlayType( source.type ) ) {
					nonNativeSources.push( source );
				}
			} );

			nonNativeSources.forEach( ( source ) => {
				$( source ).detach().appendTo( this.$videoplayer );
			} );
		} else {
			resolutions = this.extractResolutions();

			// Do not autoselect above 1080p due to bandwidth requirements.
			// Also, note a fake res of 99999 is used to mark original files.
			// Never auto-select an original unless it is the only file.
			const maxRes = 1080;

			// Pick the first resolution at least the size of the player,
			// unless they're all too small.
			let playerHeight = Math.min(
				maxRes,
				// Account for screen density
				this.$videoplayer.height() * window.devicePixelRatio
			);
			if ( !OgvJsSupport.canPlayNatively() ) {
				// Don't pick high-res versions on ogv.js which may be slow.
				playerHeight = Math.min( playerHeight, 480 );
			}
			resolutions.sort( ( a, b ) => a - b );
			for ( let i = 0, l = resolutions.length; i < l; i++ ) {
				if ( resolutions[ i ] <= maxRes ) {
					defaultRes = resolutions[ i ];
					if ( defaultRes >= playerHeight ) {
						break;
					}
				}
			}
			if ( !this.isAudio && defaultRes ) {
				this.playerConfig.plugins.videoJsResolutionSwitcher.default = defaultRes;
			}
			if ( playerHeight >= 120 ) { // 5em === 65px
				// We place the progressbar on top of the other controls
				this.$videoplayer.addClass( 'vjs-high-controls' );
				this.playerConfig.controlBar.volumePanel.inline = true;
				this.playerConfig.controlBar.volumePanel.vertical = false;
			}
		}
		// We remove SRT subtitles tracks as we can't handle them
		this.$videoplayer.find( 'track[type="text/x-srt"]' ).remove();
		this.$videoplayer.addClass( 'video-js' ).removeClass( 'mw-file-element' );

		if ( this.playerConfig.fill ) {
			// In fill mode, remove any inline width/height
			// from the inline player
			this.videoplayer.style.removeProperty( 'width' );
			this.videoplayer.style.removeProperty( 'height' );
		}

		const NS_FILE = mw.config.get( 'wgNamespaceIds' ).file;
		const resource = this.videoplayer.getAttribute( 'resource' );
		const resourceTitle = resource ?
			decodeURIComponent( resource.slice( resource.lastIndexOf( '/' ) + 1 ) ) :
			this.$videoplayer.data( 'mwtitle' );
		if ( resourceTitle ) {
			this.playerConfig.mwTitle = mw.Title.newFromText(
				resourceTitle, NS_FILE
			);
		} else {
			mw.log.warn( 'Unable to detect resource Title for this media element' );
		}

		// Launch the player
		return OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs', this.videoplayer )
			.then( () => {
				const d = $.Deferred();
				this.videojsPlayer = videojs( this.videoplayer, this.playerConfig );
				// Do not use the ready callback of the videojs function
				// The texttracks are not done initializing in that ready callback (T309414)
				this.videojsPlayer.ready( () => {
					InlinePlayer.activePlayers.push( this.videojsPlayer );
					this.selectDefaultTrack();
					/* More custom stuff goes here */
					d.resolve( this.videojsPlayer );
					mw.hook( 'tmh.player.loaded' ).fire( this.videojsPlayer );
				} );
				return d.promise();
			} );
	}

	/**
	 * Select a default text track to enable
	 * based on user language with fallback to content language
	 */
	selectDefaultTrack() {
		let userLanguageTrack;
		let contentLanguageTrack;
		const tracks = this.videojsPlayer.textTracks();

		// video.js's TrackList is array-like, but not iterable
		for ( const track of Array.from( tracks ) ) {
			// For now we only support subtitles
			// Also does not deal with language fallbacks
			if ( track.kind === 'subtitles' ) {
				const tl = track.language.toLowerCase();
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
	}

	/**
	 * Extract the list of resolutions from the HTMLSourceElements
	 * contained within the HTMLMediaElement.
	 *
	 * Also sets translated labels and the extracted res
	 * as attributes 'label' and 'res' on the HTMLSourceElements,
	 * for use by the resolution switcher plugin
	 *
	 * @return {number[]}
	 */
	extractResolutions() {
		// Video: extract the relevant resolutions from source elements
		// and pass them into the videoJsResolutionSwitcher plugin in
		// our preferred order and labeling.
		const resolutions = [];
		this.$videoplayer.find( 'source' ).each( ( _index, source ) => {
			// FIXME would be better if we can configure the plugin
			// to make use of our preferred attributes
			const $source = $( source );
			const transcodeKey = $source.data( 'transcodekey' );
			let res = parseInt( $source.data( 'height' ), 10 );
			let label = mw.msg( 'timedmedia-source-original' );

			if ( transcodeKey ) {
				const matches = transcodeKey.match( /^(\d+)p\./ );
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
					label = mw.msg( 'timedmedia-resolution-' + res );
				}
			} else {
				// Original source; sort to top and never auto-select.
				res = 99999;
			}
			$source.attr( 'res', res );
			$source.attr( 'label', label );
			resolutions.push( res );
		} );
		return resolutions;
	}
}

/**
 * videoJS options for the videojs Html5 Tech plugin.
 * These options are merged into the final config for the player during initialize()
 *
 * Text tracks are not preloaded because we may have a large number of subtitles
 * which never get used, and it's expensive to load them all.
 *
 * More consistent subtitle rendering is available due to disabling native text tracks.
 *
 * Disabling native audio tracks avoids accidentally exposing the Opus audio tracks
 * in HTTP Live Streaming playlists on Safari, which can't actually play them and
 * breaks things if you switch to them
 *
 * @static
 * @type {Object}
 */
InlinePlayer.html5techOpt = {
	preloadTextTracks: false,
	nativeTextTracks: false,
	nativeAudioTracks: false
};

/**
 * videoJS player options shared by all uses
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {videojs.Options}
 */
InlinePlayer.globalConfig = {
	// controls are initially hidden inside the dialog
	// to avoid a flash of the native controls
	controls: true,
	responsive: true,
	fill: false,
	iframe: false,
	language: mw.config.get( 'wgUserLanguage' ),
	controlBar: {
		// iOS does not allow using the fullscreen, so no point in adding that control
		// eslint-disable-next-line compat/compat
		fullscreenToggle: !!document.fullscreenEnabled || !!document.webkitFullscreenEnabled,
		volumePanel: {
			vertical: true,
			inline: false
		}
	},
	enableDocumentPictureInPicture: true,
	userActions: {
		// https://docs.videojs.com/tutorial-options.html#useractions.hotkeys
		hotkeys: true
	},
	techOrder: [ 'html5' ],
	plugins: {
		infoButton: {},
		endCard: {}
	},
	html5: InlinePlayer.html5techOpt
};

/**
 * videoJS player options specific to audio playback
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {videojs.Options}
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
 * videoJS player options specific to videos
 * These options are merged into the final config for the player during initialize()
 *
 * @static
 * @type {videojs.Options}
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
		// from 340: play, volume, position, time remaining,
		// [CC,] resolution, fullscreen [,info]
		xlarge: 1000,
		huge: 2000
	},
	controlBar: {
		currentTimeDisplay: true,
		timeDivider: true,
		durationDisplay: true,
		remainingTimeDisplay: false
	},
	plugins: {
		videoJsResolutionSwitcher: {
			sourceOrder: true
		}
	}
};

/**
 * Set to true if the class has been initialized
 *
 * @static
 * @type {boolean}
 * @private
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
 * @private
 */
InlinePlayer.activePlayers = [];

/**
 * Remove any detached players from previous live previews etc
 *
 * @private
 * @return {jQuery}
 * @chainable
 */
function disposeDetachedPlayers() {
	InlinePlayer.activePlayers = InlinePlayer.activePlayers.filter( ( player ) => {
		if ( !player.el().ownerDocument.body.contains( player.el() ) ) {
			player.dispose();
			return false;
		}
		return true;
	} );
	return this;
}

/**
 * jQuery plugin to cleanup all resources of
 * a player which is no longer in the document
 *
 * @return {jQuery}
 * @chainable
 */
$.disposeDetachedPlayers = disposeDetachedPlayers;

module.exports = InlinePlayer;
