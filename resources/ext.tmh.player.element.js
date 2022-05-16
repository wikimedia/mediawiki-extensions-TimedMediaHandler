/**
 * Main entry class for elements enhanced with videojs
 * Provides page player loading, either with click-to-load dialog or inline mode
 *
 * @class TimedMediaHandler.MediaElement
 */
/* eslint-disable no-implicit-globals */

/**
 * @param {HTMLMediaElement} element
 * @constructor
 * @type {TimedMediaHandler.MediaElement}
 */
function MediaElement( element ) {
	this.element = element;
	this.$element = $( element );
	this.isAudio = element.tagName.toLowerCase() === 'audio';
	this.$placeholder = null;
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
 */
MediaElement.$interstitial = null;

function secondsToComponents( totalSeconds ) {
	totalSeconds = parseInt( totalSeconds, 10 );
	var hours = Math.floor( totalSeconds / 3600 );
	var minutes = Math.floor( ( totalSeconds % 3600 ) / 60 );
	var seconds = totalSeconds % 60;
	return {
		hours: hours,
		minutes: minutes,
		seconds: seconds
	};
}

function secondsToDurationString( totalSeconds ) {
	var timeString;
	var components = secondsToComponents( totalSeconds );
	var hours = components.hours;
	var minutes = components.minutes;
	var seconds = components.seconds;

	timeString = String( seconds );
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
	var components = secondsToComponents( totalSeconds );
	var hours = components.hours;
	var minutes = components.minutes;
	var seconds = components.seconds;

	if ( hours ) {
		return mw.msg( 'timedmedia-duration-hms', hours, minutes, seconds );
	}
	if ( minutes ) {
		return mw.msg( 'timedmedia-duration-ms', minutes, seconds );
	}
	return mw.msg( 'timedmedia-duration-s', seconds );
}

/**
 * Load our customizations for the media element,
 * loading videojs inline or upon click inside a MediaDialog
 */
MediaElement.prototype.load = function () {
	if ( this.$element.closest( '.mw-tmh-player' ).length ) {
		// This player has already been transformed.
		return;
	}
	// Get this state before modifying
	var playing = this.originalIsPlaying();

	// Hide native controls, we will restore them later once videojs player loads.
	this.$element.removeAttr( 'controls' );

	// Make a shallow clone, because we don't need <source> and <track> children
	// for the placeholder and remove unneeded attributes and interactions
	var $clonedVid = $( this.element.cloneNode() );
	$clonedVid.attr( {
		id: $clonedVid.attr( 'id' ) + '_placeholder',
		disabled: '',
		tabindex: -1
	} ).removeAttr( 'src' );

	if ( !this.isAudio ) {
		var aspectRatio = this.$element.attr( 'width' ) + ' / ' + this.$element.attr( 'height' );
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
				title: mw.msg( 'timedmedia-play-media' ),
				role: 'button'
			} )
			.on( 'click', this.clickHandler.bind( this ) )
			.on( 'keypress', this.keyPressHandler.bind( this ) )
			.append( $( '<span>' ).addClass( 'mw-tmh-play-icon' ) )
		);

	if ( ( this.isAudio && this.$element.attr( 'width' ) >= 150 ) || ( !this.isAudio && this.$element.attr( 'height' ) >= 150 ) ) {
		// Add duration label
		var duration = this.$element.data( 'durationhint' ) || 0;
		var $duration = $( '<span>' )
			.addClass( 'mw-tmh-duration mw-tmh-label' )
			.attr( 'aria-label', secondsToDurationLongString( duration ) )
			.text( secondsToDurationString( duration ) );
		this.$placeholder.append( $duration );

		// Add CC label; currently skip for audio due to positioning limitations
		if ( !this.isAudio && this.$element.find( 'track' ).length > 0 ) {
			var $ccLabel = $( '<span>' )
				.addClass( 'mw-tmh-cc mw-tmh-label' )
				.attr( 'aria-label', mw.msg( 'timedmedia-subtitles-available' ) )
				.text( 'CC' ); // This is used as an icon
			this.$placeholder.append( $ccLabel );
		}
	}

	// Config exported via package files, T60082
	var enableLegacyMediaDOM = require( './config.json' ).ParserEnableLegacyMediaDOM;
	if ( enableLegacyMediaDOM ) {
		this.$element.replaceWith( this.$placeholder );
	} else {
		// Replace the span linkWrap gave us
		this.$element.parent().replaceWith( this.$placeholder );
	}

	if ( playing ) {
		this.playInlineOrOpenDialog();
	}
};

/**
 * Check if the original element is playing
 *
 * @return {boolean}
 */
MediaElement.prototype.originalIsPlaying = function () {
	return this.element.readyState > 2 &&
		this.element.currentTime > 0 &&
		!this.element.paused &&
		!this.element.ended;
};

/**
 * Construct URL to the file description page
 *
 * @return {string}
 */
MediaElement.prototype.getUrl = function () {
	// Construct a file target link for middle-click / ctrl-click / right-click
	return ( mw.Title.makeTitle(
		mw.config.get( 'wgNamespaceIds' ).file,
		this.$element.data( 'mwtitle' )
	) ).getUrl();
};

MediaElement.prototype.isInline = function () {
	if ( this.element.classList.contains( 'mw-tmh-inline' ) ) {
		return true;
	}
	if ( this.isAudio && this.$element.find( 'track' ).length === 0 ) {
		return true;
	}
	return false;
};

/**
 * Key press handler for `<a role="button">` element to open a
 * dialog and play a {MediaElement}.
 *
 * @param {MouseEvent} event
 */
MediaElement.prototype.keyPressHandler = function ( event ) {
	if (
		MediaElement.currentlyPlaying ||
		( event.key !== ' ' && event.key !== 'Enter' )
	) {
		return;
	}
	this.playInlineOrOpenDialog();
	event.preventDefault();
};

/**
 * Click handler to open dialog and play a {MediaElement}
 *
 * @param {MouseEvent} event
 */
MediaElement.prototype.clickHandler = function ( event ) {
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
};

/**
 * Method to load the player inline or open a dialog and
 * play the element in the dialog.
 */
MediaElement.prototype.playInlineOrOpenDialog = function () {
	var mediaElement = this;

	MediaElement.$interstitial = $( '<div>' ).addClass( 'mw-tmh-player-interstitial' )
		.append( $( '<div>' ).addClass( 'mw-tmh-player-progress' )
			.append( $( '<div>' ).addClass( 'mw-tmh-player-progress-bar' ) ) )
		.appendTo( document.body );

	// If we're using ogv.js, we have to initialize the audio context
	// during a click event to work on Safari, especially for iOS.
	if ( !mw.OgvJsSupport.canPlayNatively() ) {
		mw.OgvJsSupport.initAudioContext();
	}

	// Autoplay busting hack for native audio playback
	// Must force a play during the user gesture on the element we will use.
	// Our later, async loading of the modules can break the path
	var playPromise = this.element.play();
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
		playPromise.then( function () {
			setTimeout( function () {
				mediaElement.element.pause();
			}, 0 );
		} );
	}

	if ( this.isInline() ) {
		mw.loader.using( 'ext.tmh.player.inline' ).then( function () {
			mediaElement.$placeholder.find( 'a, .mw-tmh-label' ).detach();
			mediaElement.$placeholder.find( 'video,audio' )
				.replaceWith( mediaElement.element );

			var InlinePlayer = mw.loader.require( 'ext.tmh.player.inline' );
			var inlinePlayer = new InlinePlayer( mediaElement.element, { bigPlayButton: false } );
			inlinePlayer.infuse().then( function ( videojsPlayer ) {
				videojsPlayer.ready( function () {
					// Use a setTimeout to ensure all ready callbacks have run before
					// we start playback. This is important for the source selector
					// plugin, which may change sources before playback begins.
					//
					// This is used instead of an event like `canplay` or `loadeddata`
					// because some versions of EdgeHTML don't fire these events.
					// Support: Edge 18
					setTimeout( function () {
						MediaElement.$interstitial.detach();
						videojsPlayer.play();
					}, 0 );
				} );
			} );
		} );
	} else {
		MediaElement.currentlyPlaying = true;
		mw.loader.using( 'ext.tmh.player.dialog' ).then( function () {
			MediaElement.$interstitial.detach();
			return mediaElement.$element.showVideoPlayerDialog().always( function () {
				// when showing of video player dialog ends
				MediaElement.currentlyPlaying = false;
			} );
		} ).catch( function () {
			MediaElement.$interstitial.detach();
			MediaElement.currentlyPlaying = false;
		} );
	}
};

module.exports = MediaElement;
