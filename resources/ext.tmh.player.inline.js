/* global videojs */
( function () {
	var globalConfig, videoConfig, audioConfig, playerConfig, activePlayers = [], techOpt;
	techOpt = {
		preloadTextTracks: false,
		nativeTextTracks: false
	};
	globalConfig = {
		// controls are intially hidden inside the dialog
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
	 * Select a default text track to enable
	 * based on user language with fallback to content language
	 *
	 * @param {videojs.Player} player
	 */
	function selectDefaultTrack( player ) {
		var track,
			tl,
			userLanguageTrack,
			contentLanguageTrack,
			tracks = player.textTracks();

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
	}

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
	 *
	 * @return {jQuery.Deferred}
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

				// Pick the first resolution at least the size of the player,
				// unless they're all too small.
				playerHeight = $( videoplayer ).height();
				if ( !mw.OgvJsSupport.canPlayNatively() ) {
					// Don't pick high-res versions on ogv.js which may be slow.
					playerHeight = Math.min( playerHeight, 480 );
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

			$videoplayer.closest( '.thumbinner' ).addClass( 'mw-overflow' );

			// Launch the player
			$videoplayer.addClass( 'video-js' );
			vjs = videojs( videoplayer, playerConfig );
			vjs.ready( function () {
				activePlayers.push( this );
				selectDefaultTrack( this );
				/* More custom stuff goes here */
			} );
			return vjs;
		}
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
			globalConfig.ogvjs = $.extend( techOpt, {
				base: mw.OgvJsSupport.basePath(),
				audioContext: mw.OgvJsSupport.initAudioContext()
			} );
			globalConfig.techOrder.push( 'ogvjs' );
			// ogvjs tech does not support picture in picture
			globalConfig.controlBar.pictureInPictureToggle = false;
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
