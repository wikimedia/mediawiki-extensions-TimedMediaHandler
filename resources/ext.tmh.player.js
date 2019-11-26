( function () {

	/**
	 * State to de-duplicate clicks if initial load takes time.
	 */
	var currentlyPlaying = false;

	/**
	 * Load video players for a jQuery collection
	 */
	function loadVideoPlayer() {
		var $collection = this;

		function loadSinglePlayer() {
			var videoplayer = this,
				$videoplayer = $( this ),
				isAudio = videoplayer.tagName.toLowerCase() === 'audio',
				videoLink,
				$interstitial;

			if ( $videoplayer.closest( '.mw-tmh-player' ).length ) {
				// This player has already been transformed.
				return;
			}

			// Do not translate audio players for now, which work natively
			// and don't require fancy custom features.
			if ( isAudio ) {
				return;
			}

			// Construct a file target link for middle-click / ctrl-click / right-click
			videoLink = ( mw.Title.makeTitle( mw.config.get( 'wgNamespaceIds' ).file, $videoplayer.data( 'mwtitle' ) ) ).getUrl();
			$( '<span>' )
				.addClass( 'mw-tmh-player' )
				.addClass( isAudio ? 'audio' : 'video' )
				.css( {
					width: $videoplayer.width() + 'px',
					height: $videoplayer.height() + 'px'
				} )
				.append( $videoplayer.clone()
					.attr( 'controls', false )
					.attr( 'disabled', true )
				)
				.append( $( '<a>' )
					.addClass( 'mw-tmh-play' )
					.attr( 'href', videoLink )
					.attr( 'title', mw.msg( 'timedmedia-play-media' ) )
					.on( 'click', function ( event ) {
						if ( !currentlyPlaying ) {
							$interstitial = $( '<div>' ).addClass( 'mw-tmh-player-interstitial' )
								.append( $( '<div>' ).addClass( 'mw-tmh-player-progress' )
									.append( $( '<div>' ).addClass( 'mw-tmh-player-progress-bar' ) ) )
								.appendTo( document.body );

							// If we're using ogv.js, we have to initialize the audio context
							// during a click event to work on Safari, especially for iOS.
							if ( !mw.OgvJsSupport.canPlayNatively() ) {
								mw.OgvJsSupport.initAudioContext();
							}

							currentlyPlaying = true;
							mw.loader.using( 'ext.tmh.player.dialog', function () {
								$interstitial.detach();
								$videoplayer.showVideoPlayerDialog().then( function () {
									currentlyPlaying = false;
								} );
							} );
						}
						// @todo: this eats middle-click, should fix that
						event.preventDefault();
					} )
				)
				.replaceAll( $videoplayer );
		}

		$collection.each( loadSinglePlayer );
	}

	$.fn.loadVideoPlayer = loadVideoPlayer;

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		$content.find( 'video' ).loadVideoPlayer();
	} );
	$( function () {
		// @fixme load and transform immediately for these?
		// The iframe mode
		// eslint-disable-next-line no-jquery/no-global-selector
		$( '#videoContainer video' ).loadVideoPlayer();
	} );

}() );
