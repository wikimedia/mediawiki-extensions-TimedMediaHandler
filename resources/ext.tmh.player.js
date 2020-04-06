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
				$interstitial,
				// eslint-disable-next-line no-jquery/no-class-state
				inline = $videoplayer.hasClass( 'mw-tmh-inline' ),
				$placeholder;

			if ( $videoplayer.closest( '.mw-tmh-player' ).length ) {
				// This player has already been transformed.
				return;
			}

			// Construct a file target link for middle-click / ctrl-click / right-click
			videoLink = ( mw.Title.makeTitle( mw.config.get( 'wgNamespaceIds' ).file, $videoplayer.data( 'mwtitle' ) ) ).getUrl();
			$placeholder = $( '<span>' )
				.addClass( 'mw-tmh-player' )
				.addClass( isAudio ? 'audio' : 'video' )
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

								// Safari autoplay breakage hack for native audio playback
								// Must force a play during the user gesture on the element
								// we will use.
								if ( isAudio ) {
									videoplayer.play();
									videoplayer.pause();
								}
							}

							currentlyPlaying = true;
							if ( inline ) {
								mw.loader.using( 'ext.tmh.player.inline', function () {
									$placeholder.find( 'a' ).detach();
									$placeholder.find( 'video,audio' ).replaceWith( $videoplayer );
									$videoplayer.transformVideoPlayer().then( function ( $videojs ) {
										var player = $videojs[ 0 ];
										player.ready( function () {
											// Use a setTimeout to ensure all ready callbacks have run before
											// we start playback. This is important for the source selector
											// plugin, which may change sources before playback begins.
											//
											// This is used instead of an event like `canplay` or `loadeddata`
											// because some versions of EdgeHTML don't fire these events.
											// Support: Edge 18
											setTimeout( function () {
												$interstitial.detach();
												player.play();
											}, 0 );
										} );
									} );
								} );
							} else {
								mw.loader.using( 'ext.tmh.player.dialog', function () {
									$interstitial.detach();
									$videoplayer.showVideoPlayerDialog().then( function () {
										currentlyPlaying = false;
									} );
								} );
							}
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
		$content.find( 'video, audio' ).loadVideoPlayer();
	} );
	$( function () {
		// @fixme load and transform immediately for these?
		// The iframe mode
		// eslint-disable-next-line no-jquery/no-global-selector
		$( '#videoContainer video, #videoContainer audio' ).loadVideoPlayer();
	} );

}() );
