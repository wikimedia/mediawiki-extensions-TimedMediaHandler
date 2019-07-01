/* global videojs */
( function () {
	var globalConfig, videoConfig, audioConfig, playerConfig;

	globalConfig = {
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
		}
	};

	videoConfig = {
		plugins: {
			videoJsResolutionSwitcher: {
				sourceOrder: true,
				customSourcePicker: function ( player, sources/* , label */ ) {
					// Resolution switcher gets confused by preload=none on ogv.js
					if ( player.preload() === 'none' ) {
						player.preload( 'metadata' );
					}
					player.src( sources );
					return player;
				}
			}
		}
	};

	audioConfig = {
		controlBar: {
			fullscreenToggle: false
		}
	};

	/**
	 * Load video players for a jQuery collection
	 */
	function loadVideoPlayer() {
		var $collection = this;

		function loadSinglePlayer( index ) {
			var i, l, preload, resolutions, playerHeight, defaultRes,
				videoplayer = this,
				$videoplayer = $( this ),
				isAudio = videoplayer.tagName.toLowerCase() === 'audio',
				nonNativeSources = [];

			if ( $videoplayer.closest( '.video-js' ).length ) {
				// This player has already been transformed.
				return;
			}

			playerConfig = $.extend( {}, globalConfig );
			playerConfig = $.extend( true, {}, playerConfig, isAudio ? audioConfig : videoConfig );

			// Future interactions go faster if we've preloaded a little
			preload = 'metadata';
			if ( !mw.OgvJsSupport.canPlayNatively() ) {
				// ogv.js currently is expensive to start up:
				// https://github.com/brion/ogv.js/issues/438
				preload = 'none';
			}
			if ( index >= 10 ) {
				// On pages with many videos, like Category pages, don't preload em all
				preload = 'none';
			}

			resolutions = [];

			$videoplayer.attr( {
				preload: preload
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
			videojs( videoplayer, playerConfig ).ready( function () {
				/* More custom stuff goes here */
			} );
		}

		if ( !mw.OgvJsSupport.canPlayNatively() ) {
			globalConfig.ogvjs = {
				base: mw.OgvJsSupport.basePath()
			};
			globalConfig.techOrder.push( 'ogvjs' );
		}
		mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' ).then( function () {
			$collection.each( loadSinglePlayer );
		} );
	}

	// Preload the ogv.js module if we're going to need it...
	mw.OgvJsSupport.loadIfNeeded( 'ext.tmh.videojs-ogvjs' );

	$.fn.loadVideoPlayer = loadVideoPlayer;

	// Add translations for the plugins
	// video.js translations don't have region postfixes (yet)
	videojs.addLanguage( mw.config.get( 'wgUserLanguage' ).split( '-' )[ 0 ], {
		'More information': mw.msg( 'videojs-more-information' ),
		Quality: mw.msg( 'videojs-quality' )
	} );

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		$content.find( 'video,audio' ).loadVideoPlayer();
	} );
	$( function () {
		// The iframe mode
		// eslint-disable-next-line no-jquery/no-global-selector
		$( '#videoContainer video, #videoContainer audio' ).loadVideoPlayer();
	} );

}() );
