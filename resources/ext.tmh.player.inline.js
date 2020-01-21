/* global videojs */
( function () {
	var globalConfig, videoConfig, audioConfig, playerConfig, activePlayers = [], techOpt;
	techOpt = {
		preloadTextTracks: false,
		nativeTextTracks: false
	};
	globalConfig = {
		responsive: true,
		language: mw.config.get( 'wgUserLanguage' ),
		controlBar: {
			volumePanel: {
				vertical: true,
				inline: false
			}
		},
		techOrder: [ 'html5' ],
		plugins: {
			infoButton: {}
		},
		html5: techOpt
	};

	videoConfig = {
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

	audioConfig = {
		controlBar: {
			fullscreenToggle: false
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
	 * Remove any detached players from previous live previews etc
	 */
	function disposeDetachedPlayers() {
		activePlayers = activePlayers.filter( function ( player ) {
			if ( !player.el().ownerDocument.body.contains( player.el() ) ) {
				player.dispose();
				return false;
			}
			return true;
		} );
	}

	/**
	 * Load video players for a jQuery collection
	 */
	function loadVideoPlayer() {
		var $collection = this;

		function loadSinglePlayer() {
			var i, l, resolutions, playerHeight, defaultRes,
				videoplayer = this,
				$videoplayer = $( this ),
				isAudio = videoplayer.tagName.toLowerCase() === 'audio',
				nonNativeSources = [],
				vjs;

			if ( $videoplayer.closest( '.video-js' ).length ) {
				// This player has already been transformed.
				return;
			}

			playerConfig = $.extend( {}, globalConfig );
			playerConfig = $.extend( true, {}, playerConfig, isAudio ? audioConfig : videoConfig );

			resolutions = [];

			// Future interactions go faster if we've preloaded a little
			$videoplayer.attr( {
				preload: 'metadata'
			} );

			if ( isAudio ) {
				// Audio: manipulate source elements to preferred order.
				// This means preferring native-playback over ogv.js-playback
				// so we don't go loading it when we don't need it.
				$videoplayer.find( 'source' ).each( function () {
					if ( !videoplayer.canPlayType( this.type ) ) {
						nonNativeSources.push( this );
					}
				} );

				nonNativeSources.forEach( function ( source ) {
					$( source ).detach().appendTo( $videoplayer );
				} );
			} else {
				// Video: extract the relevant resolutions from source elements
				// and pass them into the videoJsResolutionSwitcher plugin in
				// our preferred order and labeling.
				$videoplayer.find( 'source' ).each( function () {
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

				// Pick the first resolution at least the size of the player,
				// unless they're all too small.
				playerHeight = $( videoplayer ).height();
				if ( !mw.OgvJsSupport.canPlayNatively() ) {
					// Don't pick high-res versions on ogv.js which may be slow.
					if ( typeof WebAssembly === 'object' && typeof WebAssembly.Module === 'function' ) {
						// We have WebAssembly, so it'll probably be fairly fast.
						// Default to full SD resolution if it fits.
						playerHeight = Math.min( playerHeight, 480 );
					} else {
						// No Wasm, must use the JS build of ogv.js which is slow.
						// Especially if this is IE, use lower resolutions.
						playerHeight = Math.min( playerHeight, 240 );
					}
				}
				resolutions.sort( function ( a, b ) {
					return a - b;
				} );
				for ( i = 0, l = resolutions.length; i < l; i++ ) {
					defaultRes = resolutions[ i ];
					if ( defaultRes >= playerHeight ) {
						break;
					}
				}
				if ( !isAudio && defaultRes ) {
					playerConfig.plugins.videoJsResolutionSwitcher.default = defaultRes;
				}
			}
			// We remove SRT subtitles tracks as we can't handle them
			$videoplayer.find( 'track[type="text/x-srt"]' ).remove();

			$videoplayer.parent( '.thumbinner' ).addClass( 'mw-overflow' );

			// Launch the player
			$videoplayer.addClass( 'video-js' );
			vjs = videojs( videoplayer, playerConfig );
			vjs.ready( function () {
				activePlayers.push( this );
				/* More custom stuff goes here */
			} );
			return vjs;
		}

		if ( !mw.OgvJsSupport.canPlayNatively() ) {
			globalConfig.ogvjs = $.extend( techOpt, {
				base: mw.OgvJsSupport.basePath(),
				audioContext: mw.OgvJsSupport.initAudioContext()
			} );
			globalConfig.techOrder.push( 'ogvjs' );
		}
		return $.Deferred( function ( deferred ) {
			mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' ).then( function () {
				deferred.resolve( $collection.map( loadSinglePlayer ) );
			} );
		} );
	}

	// Preload the ogv.js module if we're going to need it...
	mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' );

	$.fn.transformVideoPlayer = loadVideoPlayer;
	$.disposeDetachedPlayers = disposeDetachedPlayers;

	// Add translations for the plugins
	// video.js translations don't have region postfixes (yet)
	videojs.addLanguage( mw.config.get( 'wgUserLanguage' ).split( '-' )[ 0 ], {
		'More information': mw.msg( 'videojs-more-information' ),
		Quality: mw.msg( 'videojs-quality' )
	} );

}() );
