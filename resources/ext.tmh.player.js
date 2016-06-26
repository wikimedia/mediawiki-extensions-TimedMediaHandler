
( function ( $, mw, videojs ) {
	var globalConfig, audioConfig, playerConfig, $source;

	globalConfig = {
		language: mw.config.get( 'wgUserLanguage' ),
		controlBar: {
			liveDisplay: false,
			volumeMenuButton: {
				vertical: true,
				inline: false
			}
		},
		techOrder: [ 'html5', 'ogvjs' ],
		plugins: {
			videoJsResolutionSwitcher: {
				sourceOrder: true
			},
			responsiveLayout: {
				layoutMap: [
					{ layoutClassName: 'vjs-layout-tiny', width: 3 },
					{ layoutClassName: 'vjs-layout-x-small', width: 4 },
					{ layoutClassName: 'vjs-layout-small', width: 5 },
					{ layoutClassName: 'defaults', width: 6 }
				]
			},
			replayButton: {},
			infoButton: {}
		},
		ogvjs: {
			base: mw.OgvJsSupport.basePath()
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
		var videoplayer, $videoplayer;

		this.each( function ( index ) {
			videoplayer = this;
			$videoplayer = $( this );
			playerConfig = $.extend( {}, globalConfig );
			if ( videoplayer.tagName.toLowerCase() === 'audio' ) {
				// We hide the big play button, show the controlbar with CSS
				// We remove the fullscreen button
				playerConfig = $.extend( true, {}, playerConfig, audioConfig );
			}
			$( videoplayer ).attr( {
				/* Don't preload on pages with many videos, like Category pages */
				preload: ( index < 10 ) ? 'auto' : 'metadata'
			} ).find( 'source' ).each( function () {
				// FIXME would be better if we can configure the plugin to make use of our preferred attributes
				$source = $( this );
				$source.attr( 'res', $source.data( 'height' ) );
				$source.attr( 'label', $source.data( 'shorttitle' ) );
			} );
			$videoplayer.parent( '.thumbinner' ).addClass( 'mw-overflow' );

			// Launch the player
			videojs( videoplayer, playerConfig ).ready( function () {
				/* More custom stuff goes here */
			} );
		} );
	}

	$.fn.loadVideoPlayer = loadVideoPlayer;

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		$content.find( '.video-js' ).loadVideoPlayer();
	} );
	$( function () {
		// The iframe mode
		$( '#bgimage' ).remove();
		if ( $( '.video-js[data-player="fillwindow"]' ).length > 0 ) {
			$( '.video-js[data-player="fillwindow"]' ).loadVideoPlayer();
		}
	} );

} )( jQuery, mediaWiki, videojs );
