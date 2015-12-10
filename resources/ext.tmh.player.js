
( function ( $, mw, videojs ) {
	var globalConfig, audioConfig, playerConfig,
		start, end, $source;

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
			}
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

	/*
	function growPlayer() {
		// FIXME: fancy transform stuff should be added here :)
		// Possibly only do this when content renderer sets a certain class ?
		var targetWidth = 600,
			player = this,
			$playerDiv = $( player.el() );

		var width = player.width();
		var enlargeRatio = targetWidth / width;
		if ( width < targetWidth ) {
			$playerDiv
			.addClass( 'vjs-expanded' )
			.data( {
				'original-height': player.height(),
				'original-width': width,
				style: $playerDiv.attr( 'style' )
			} );
			player.height( Math.ceil( player.height() * enlargeRatio ) );
			player.width( targetWidth );
			var parentThumb = $playerDiv.closest( '.thumb' );
			if ( parentThumb.length ) {
				parentThumb.data( {
					style: parentThumb.attr( 'style' ),
					'class': parentThumb.attr( 'class' )
				} );
				parentThumb.removeClass( 'thumb tright tleft tnone' ).addClass( 'removedThumb' ).css( { clear: 'both' } );
				parentThumb.children( '.thumbinner' ).removeClass( 'thumbinner' ).addClass( 'removedInner' ).css( { width: 'auto', height: 'auto', 'margin-left': 'auto', 'margin-right': 'auto' } );
			}
			$playerDiv.css( { clear: 'both', 'margin-left': 'auto', 'margin-right': 'auto' } );
		}

	}

	function shrinkPlayer() {
		var player = this,
			$playerDiv = $( player.el() );
		if ( $playerDiv.hasClass( 'vjs-expanded' ) ) {
			player.width( $playerDiv.data( 'original-width' ) );
			player.height( $playerDiv.data( 'original-height' ) );
			$playerDiv.removeClass( 'vjs-expanded' ).attr( { style: $playerDiv.data( 'style' ) } );
			var parentThumb = $playerDiv.closest( '.removedThumb' );
			if ( parentThumb.length ) {
				parentThumb.attr( { style: parentThumb.data( 'style' ), 'class': parentThumb.data( 'class' ) } );
				parentThumb.children( '.removedInner' ).attr( 'class', 'thumbinner' );
			}
		}
	}
	*/

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
			// FIXME offset plugin  is currently disabled, not yet 5.* compatible
			start = $( videoplayer ).data( 'start' );
			end = $( videoplayer ).data( 'end' );

			// Launch the player
			videojs( videoplayer, playerConfig ).ready( function () {
				// TODO disabled the 'enlarging' for now. reconsidering alternative presentation modes
				// this.on( 'play', growPlayer );
				// this.on( 'ended', shrinkPlayer );
				/* More custom stuff goes here */
			} );
		} );
	}

	$.fn.loadVideoPlayer = loadVideoPlayer;

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		$content.find( '.video-js' ).loadVideoPlayer();
	} );

} )( jQuery, mediaWiki, videojs );
