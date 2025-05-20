const OgvJsSupport = require( 'ext.tmh.OgvJsSupport' );

function secondsToComponents( totalSeconds ) {
	totalSeconds = parseInt( totalSeconds, 10 );
	const hours = Math.floor( totalSeconds / 3600 );
	const minutes = Math.floor( ( totalSeconds % 3600 ) / 60 );
	const seconds = totalSeconds % 60;
	return {
		hours,
		minutes,
		seconds
	};
}

function secondsToDurationString( totalSeconds ) {
	const {
		hours,
		minutes,
		seconds
	} = secondsToComponents( totalSeconds );

	let timeString = String( seconds );
	if ( seconds < 10 ) {
		timeString = '0' + timeString;
	}
	if ( minutes || hours && !minutes ) {
		timeString = minutes + ':' + timeString;
	} else if ( !hours ) {
		timeString = '0:' + timeString;
	}

	if ( hours ) {
		if ( minutes < 10 ) {
			timeString = '0' + timeString;
		}
		timeString = hours + ':' + timeString;
	}
	return timeString;
}

function secondsToDurationLongString( totalSeconds ) {
	const {
		hours,
		minutes,
		seconds
	} = secondsToComponents( totalSeconds );

	if ( hours ) {
		return mw.msg( 'timedmedia-duration-hms', hours, minutes, seconds );
	}
	if ( minutes ) {
		return mw.msg( 'timedmedia-duration-ms', minutes, seconds );
	}
	return mw.msg( 'timedmedia-duration-s', seconds );
}

/**
 * Main entry class for elements enhanced with videojs
 * Provides page player loading, either with click-to-load dialog or inline mode
 */
class MediaElement {
	/**
	 * @param {HTMLMediaElement} element
	 */
	constructor( element ) {
		this.element = element;
		this.$element = $( element );
		this.isAudio = element.tagName.toLowerCase() === 'audio';
		this.$placeholder = null;
	}

	/**
	 * Load our customizations for the media element,
	 * loading videojs inline or upon click inside a MediaDialog
	 */
	load() {
		if ( this.$element.closest( '.mw-tmh-player' ).length ) {
			// This player has already been transformed.
			return;
		}
		// Get this state before modifying
		const playing = this.originalIsPlaying();

		// Hide native controls, we will restore them later once videojs player loads.
		this.$element.removeAttr( 'controls' );
		this.$element.attr( 'playsinline', '' );

		// Make a shallow clone, because we don't need <source> and <track> children
		// for the placeholder and remove unneeded attributes and interactions
		const $clonedVid = $( this.element.cloneNode() );
		$clonedVid.attr( {
			id: $clonedVid.attr( 'id' ) + '_placeholder',
			disabled: '',
			tabindex: -1
		} ).removeAttr( 'src' );

		if ( !this.isAudio ) {
			const aspectRatio = this.$element.attr( 'width' ) + ' / ' + this.$element.attr( 'height' );
			// Chrome has a bug?? where it uses aspect-ration: auto width/height..
			// They somehow fall back to an incorrect A/R when inserting the video
			// if responsive height:auto is used (see our stylesheet)
			// Possibly their AR only kicks in when the poster finished loading
			$clonedVid.css( 'aspect-ratio', aspectRatio );
		}

		this.$placeholder = $( '<span>' )
			.addClass( 'mw-tmh-player' )
			.addClass( this.isAudio ? 'audio' : 'video' )
			.attr( 'style', this.$element.attr( 'style' ) )
			.append( $clonedVid )
			.append( $( '<a>' )
				.addClass( 'mw-tmh-play' )
				.attr( {
					href: this.getUrl(),
					title: this.isAudio ? mw.msg( 'timedmedia-play-audio' ) : mw.msg( 'timedmedia-play-video' ),
					role: 'button'
				} )
				.on( 'click', this.clickHandler.bind( this ) )
				.on( 'keypress', this.keyPressHandler.bind( this ) )
				.append( $( '<span>' ).addClass( 'mw-tmh-play-icon notheme' ) )
			);

		if ( ( this.isAudio && this.$element.attr( 'width' ) >= 150 ) || ( !this.isAudio && this.$element.attr( 'height' ) >= 150 ) ) {
			// Add duration label
			const duration = this.$element.data( 'durationhint' ) || 0;
			const $duration = $( '<span>' )
				.addClass( 'mw-tmh-duration mw-tmh-label' )
				.append( $( '<span>' ).addClass( 'sr-only' ).text( mw.msg(
					'timedmedia-duration',
					secondsToDurationLongString( duration )
				) ) )
				.append( $( '<span>' ).attr( 'aria-hidden', true ).text( secondsToDurationString( duration ) ) );
			this.$placeholder.append( $duration );

			// Add CC label; currently skip for audio due to positioning limitations
			if ( !this.isAudio && this.$element.find( 'track' ).length > 0 ) {
				const $ccLabel = $( '<span>' )
					.addClass( 'mw-tmh-cc mw-tmh-label' )
					.append( $( '<span>' ).addClass( 'sr-only' ).text( mw.msg( 'timedmedia-subtitles-available' ) ) )
					.append( $( '<span>' ).attr( 'aria-hidden', true ).text( 'CC' ) ); // This is used as an icon
				this.$placeholder.append( $ccLabel );
			}
		}

		if ( this.isAudio ) {
			// Transfer the mw-file-element class to the placeholder since a
			// width is added to the placeholder above, either explicitly or
			// with the audio class
			$clonedVid.removeClass( 'mw-file-element' );
			this.$placeholder.addClass( 'mw-file-element' );
		}

		this.$element.replaceWith( this.$placeholder );

		if ( playing ) {
			this.playInlineOrOpenDialog();
		}
	}

	/**
	 * Check if the original element is playing
	 *
	 * @return {boolean}
	 */
	originalIsPlaying() {
		return this.element.readyState > 2 &&
			this.element.currentTime > 0 &&
			!this.element.paused &&
			!this.element.ended;
	}

	/**
	 * Construct URL to the file description page
	 *
	 * @return {string|null}
	 */
	getUrl() {
		// Construct a file target link for middle-click / ctrl-click / right-click
		const parsoidLink = this.element.getAttribute( 'resource' );
		if ( parsoidLink ) {
			return parsoidLink;
		}

		const title = this.$element.data( 'mwtitle' );
		if ( title ) {
			return mw.Title.makeTitle(
				mw.config.get( 'wgNamespaceIds' ).file, title
			).getUrl();
		}
		return null;
	}

	isInline() {
		if ( this.element.classList.contains( 'mw-tmh-inline' ) ) {
			return true;
		}
		if ( this.isAudio && this.$element.find( 'track' ).length === 0 ) {
			return true;
		}
		return false;
	}

	/**
	 * Key press handler for `<a role="button">` element to open a
	 * dialog and play a {MediaElement}.
	 *
	 * @param {KeyboardEvent} event
	 */
	keyPressHandler( event ) {
		if (
			MediaElement.currentlyPlaying ||
			( event.key !== ' ' && event.key !== 'Enter' )
		) {
			return;
		}
		this.playInlineOrOpenDialog();
		event.preventDefault();
	}

	/**
	 * Click handler to open dialog and play a {MediaElement}
	 *
	 * @param {MouseEvent} event
	 */
	clickHandler( event ) {
		if (
			MediaElement.currentlyPlaying ||
			// not left click
			event.button !== 0 ||
			// or modifier pressed at the same time
			event.ctrlKey || event.altKey ||
			event.metaKey || event.shiftKey
		) {
			return;
		}
		this.playInlineOrOpenDialog();
		event.preventDefault();
	}

	/**
	 * Method to load the player inline or open a dialog and
	 * play the element in the dialog.
	 */
	playInlineOrOpenDialog() {
		MediaElement.$interstitial = $( '<div>' ).addClass( 'mw-tmh-player-interstitial' )
			.append( $( '<div>' ).addClass( 'mw-tmh-player-progress' )
				.append( $( '<div>' ).addClass( 'mw-tmh-player-progress-bar' ) ) )
			.appendTo( document.body );

		// If we're using ogv.js, we have to initialize the audio context
		// during a click event to work on Safari, especially for iOS.
		if ( !OgvJsSupport.canPlayNatively() ) {
			OgvJsSupport.initAudioContext();
		}

		// Autoplay busting hack for native audio playback
		// Must force a play during the user gesture on the element we will use.
		// Our later, async loading of the modules can break the path
		const playPromise = this.element.play();
		if ( !playPromise ) {
			// On older browsers, play() didn't return a promise yet.
			this.element.pause();
		} else {
			// Edge 17+
			// Chrome 50+
			// Firefox 53+
			// Safari 10+
			// The reject promise of play is not that reliable when using <source> children
			// It might not ever trigger
			// https://developer.chrome.com/blog/play-request-was-interrupted/#danger-zone
			playPromise.then( () => {
				setTimeout( () => {
					this.element.pause();
				}, 0 );
			} );
		}

		if ( this.isInline() ) {
			mw.loader.using( 'ext.tmh.player.inline' ).then( () => {
				this.$placeholder.find( 'a, .mw-tmh-label' ).detach();
				this.$placeholder.find( 'video,audio' )
					.replaceWith( this.element );

				const InlinePlayer = require( 'ext.tmh.player.inline' );
				const inlinePlayer = new InlinePlayer(
					this.element,
					{ bigPlayButton: false }
				);
				inlinePlayer.infuse().then( ( videojsPlayer ) => {
					videojsPlayer.ready( () => {
						// Use a setTimeout to ensure all ready callbacks have run before
						// we start playback. This is important for the source selector
						// plugin, which may change sources before playback begins.
						//
						// This is used instead of an event like `canplay` or `loadeddata`
						// because some versions of EdgeHTML don't fire these events.
						// Support: Edge 18
						setTimeout( () => {
							MediaElement.$interstitial.detach();
							videojsPlayer.play();
						}, 0 );
					} );
				} );
			} );
		} else {
			MediaElement.currentlyPlaying = true;
			mw.loader.using( 'ext.tmh.player.dialog' ).then( () => {
				MediaElement.$interstitial.detach();
				return this.$element.showVideoPlayerDialog().always( () => {
					// when showing of video player dialog ends
					MediaElement.currentlyPlaying = false;
				} );
			} ).catch( () => {
				MediaElement.$interstitial.detach();
				MediaElement.currentlyPlaying = false;
			} );
		}
	}
}

/**
 * Global state to de-duplicate clicks and to make sure
 * only 1 dialog is presented at a time.
 *
 * @static
 */
MediaElement.currentlyPlaying = false;

/**
 * There should be only 1 interstitial to indicate the dialog is loading.
 *
 * @static
 * @type {jQuery?}
 */
MediaElement.$interstitial = null;

module.exports = MediaElement;
