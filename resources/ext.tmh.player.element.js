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
	this.isInline = element.classList.contains( 'mw-tmh-inline' );
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

/**
 * Load our customizations for the media element,
 * loading videojs inline or upon click inside a MediaDialog
 */
MediaElement.prototype.load = function () {
	if ( this.$element.closest( '.mw-tmh-player' ).length ) {
		// This player has already been transformed.
		return;
	}

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

	this.$placeholder = $( '<span>' )
		.addClass( 'mw-tmh-player' )
		.addClass( this.isAudio ? 'audio' : 'video' )
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

	// Config exported via package files, T60082
	var enableLegacyMediaDOM = require( './config.json' ).ParserEnableLegacyMediaDOM;
	if ( enableLegacyMediaDOM ) {
		this.$element.replaceWith( this.$placeholder );
	} else {
		// Replace the span linkWrap gave us
		this.$element.parent().replaceWith( this.$placeholder );
	}
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

	// Safari autoplay breakage hack for native audio playback
	// Must force a play during the user gesture on the element
	// we will use.
	var playPromise = this.element.play();
	playPromise.then( function () {
		mediaElement.element.pause();
	}, function () {
		mediaElement.element.pause();
	} );

	if ( this.isInline ) {
		mw.loader.using( 'ext.tmh.player.inline' ).then( function () {
			mediaElement.$placeholder.find( 'a' ).detach();
			mediaElement.$placeholder.find( 'video,audio' ).replaceWith( mediaElement.$element );
			mediaElement.$element.transformVideoPlayer().then( function ( $videojs ) {
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
						MediaElement.$interstitial.detach();
						player.play();
					}, 0 );
				} );
			} );
		} );
	} else {
		MediaElement.currentlyPlaying = true;
		mw.loader.using( 'ext.tmh.player.dialog' ).then( function () {
			MediaElement.$interstitial.detach();
			return mediaElement.$element.showVideoPlayerDialog();
		} ).always( function () {
			MediaElement.currentlyPlaying = false;
		} );
	}
};

module.exports = MediaElement;
