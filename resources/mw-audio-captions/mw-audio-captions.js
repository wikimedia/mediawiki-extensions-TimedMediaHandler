/*!
 * mediawiki.dj
 *
 * @author Derk-Jan Hartman
 * @since 1.19
 */
( function () {
	/* global require */
	var videojs = null;
	if ( typeof window.videojs === 'undefined' && typeof require === 'function' ) {
		videojs = require( 'video.js' );
	} else {
		videojs = window.videojs;
	}

	( function () {
		var audioCaptions,
			activeTrack,
			hideSubtitleTimer,
			ourContainer,
			ourFrame,
			activeCues = [];

		function renderCues() {
			var i, $subtitleDiv;
			for ( i = 0; i < activeCues.length; i++ ) {
				$subtitleDiv = $( '<div>' ).attr( {
					'aria-live': 'polite'
				} ).addClass( 'caption-cue' ).html( activeCues[ i ].text );
				$subtitleDiv.addClass( activeCues[ i ].align );
				$subtitleDiv.attr( 'lang', activeTrack.language );
			}
			clearTimeout( hideSubtitleTimer );
			ourFrame.toggleClass( 'hidden', activeCues.length === 0 );
			ourFrame.find( '.caption-cue' ).remove();
			ourFrame.append( $subtitleDiv );
		}

		function setupScreen() {
			ourFrame = $( '<div>' ).addClass( 'caption-region hidden' );
			ourContainer = $( '<div>' )
				.addClass( 'caption-container' )
				.append( ourFrame );
			$( document.body ).append( ourContainer );
		}

		function cueChange() {
			var cueDescription,
				track = this;
			if ( track.kind === 'subtitles' || track.kind === 'captions' || track.kind === 'description' ) {
				activeTrack = track;
				activeCues = track.activeCues;

				if ( track.activeCues.length > 0 ) {
					cueDescription = 'id: ' + track.activeCues[ 0 ].id + ', ';
					cueDescription += 'text: ' + track.activeCues[ 0 ].text + ', ';
					cueDescription += 'startTime: ' + track.activeCues[ 0 ].startTime + ',  ';
					cueDescription += 'endTime: ' + track.activeCues[ 0 ].endTime;
				}
				mw.log( cueDescription );
			}
			renderCues();
		}

		function trackChange() {
			var i, track, tracks = this;
			mw.log( 'track change' );
			activeTrack = null;
			activeCues = [];
			for ( i = 0; i < tracks.length; i++ ) {
				track = tracks[ i ];

				if ( track.mode !== 'disabled' &&
					( track.kind === 'subtitles' || track.kind === 'captions' || track.kind === 'description' )
				) {
					activeTrack = track;
					activeCues = track.activeCues;
				}
			}
			renderCues();
		}

		function modeChange() {
			mw.log( 'mode change of track: ' + this.id );
		}

		function setupTrack( track ) {
			track.addEventListener( 'cuechange', cueChange );
			track.addEventListener( 'modechange', modeChange );
		}

		function trackListChange( event ) {
			var tracks = this;
			mw.log( 'We have ' + tracks.length + ' tracks' );
			setupTrack( event.track );
		}

		function hideSubtitles() {
			hideSubtitleTimer = setTimeout( function () {
				ourFrame.toggleClass( 'hidden', true );
			}, 2000 );
		}

		function showSubtitles() {
			clearTimeout( hideSubtitleTimer );
			ourFrame.toggleClass( 'hidden', activeCues.length === 0 );
		}

		/**
		 * Initialize the plugin.
		 * @param {Object} [options] configuration for the plugin
		 */
		audioCaptions = function ( /* options*/ ) {
			// var settings = videojs.mergeOptions(defaults, options),
			var player = this;

			player.ready( function () {
				var i, tracks;
				mw.log( 'player ready' );

				if ( !videojs.dom.hasClass( player.el(), 'vjs-audio' ) ) {
					return;
				}
				player.on( 'play', showSubtitles );
				player.on( 'pause', hideSubtitles ); // pause also fires on ended

				setupScreen();

				tracks = player.remoteTextTracks();
				mw.log( 'We have ' + tracks.length + ' initial tracks' );
				for ( i = 0; i < tracks.length; i++ ) {
					setupTrack( tracks[ i ] );
				}
				tracks.addEventListener( 'change', trackChange );
				tracks.addEventListener( 'addtrack', trackListChange );
			} );
		};

		// register the plugin
		videojs.registerPlugin( 'audioCaptions', audioCaptions );
	}() );
}() );
