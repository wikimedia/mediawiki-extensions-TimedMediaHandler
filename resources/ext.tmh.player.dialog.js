/**
 * If the device does not support fullscreen,
 * it is likely to be an iPhone. In that case use a fullscreen sized dialog.
 *
 * @return {boolean}
 */
function useFillscreen() {
	// eslint-disable-next-line compat/compat
	return !document.fullscreenEnabled &&
		!document.webkitFullscreenEnabled &&
		matchMedia( '(pointer:coarse)' ).matches;
}

const INACTIVITY_THRESHOLD = 2500;

/**
 * A media OOUI dialog to open and play a media element in.
 *
 * A modal interaction, only one dialog should be opened at a time
 *
 * @extends OO.ui.ProcessDialog
 */
class MediaDialog extends OO.ui.ProcessDialog {
	/**
	 * @param {Object} config
	 * @param {JQuery} config.$video element to present
	 */
	constructor( config ) {
		if ( useFillscreen() ) {
			Object.assign( config, { size: 'full' } );
		}
		super( config );
		this.$video = config.$video;
	}

	initialize() {
		const oouiWindow = super.initialize();

		this.$element.addClass( 'mw-tmh-media-dialog' );
		this.$element.on( 'click', ( e ) => {
			if (
				!this.$body.get( 0 ).contains( e.target ) &&
				!this.$head.get( 0 ).contains( e.target )
			) {
				// Close the dialog when user clicks outside of it
				this.close();
			}
		} );

		this.content = new OO.ui.PanelLayout( {
			padded: false,
			expanded: true
		} );
		this.content.$element.append( this.$video );
		this.$body.append( this.content.$element );

		// Detect user interactivity
		this.activityTimer = setTimeout( () => this.makeInactive(), INACTIVITY_THRESHOLD );
		const handler = OO.ui.debounce( () => {
			this.resetActivityTimer();
		}, 100, true ).bind( this );

		const element = this.$content.get( 0 );
		element.addEventListener( 'click', handler, true );
		element.addEventListener( 'mousemove', handler, true );
		element.addEventListener( 'mousedown', handler, true );
		element.addEventListener( 'touchstart', handler, true );
		element.addEventListener( 'keypress', handler, true );

		// Check if this uses a desktop viewport on a mobile device
		if ( useFillscreen() && window.innerWidth > screen.availWidth ) {
			this.$element.addClass( 'mw-tmh-desktop-on-mobile' );
		}

		return oouiWindow;
	}

	getBodyHeight() {
		// Fixed 16:10 ratio for the dialog. This may change.
		return Math.round( this.content.$element.width() * 10 / 16 );
	}

	getActionProcess( action ) {
		if ( action ) {
			return new OO.ui.Process( () => {
				this.close( { action: action } );
			} );
		}
		return super.getActionProcess( action );
	}

	/**
	 * Initiate playback of the video element.
	 * Loads the JS playback interface and triggers play
	 *
	 * Note: because of autoplay restrictions, this needs to triggered
	 * after a click, for audio to work.
	 */
	play() {
		const indicator = new OO.ui.ProgressBarWidget( {
			progress: false
		} );
		this.content.$element.append( indicator.$element );

		// We don't need a play button (autoplay) nor a poster
		const options = { poster: false, bigPlayButton: false, fill: true };

		const InlinePlayer = mw.loader.require( 'ext.tmh.player.inline' );
		this.inlinePlayer = new InlinePlayer( this.$video.get( 0 ), options );
		// We might cause a delayed load of videojs here.
		this.loadedPromise = this.inlinePlayer.infuse();

		// Start playback when ready...
		this.loadedPromise.then( ( videojsPlayer ) => {
			videojsPlayer.ready( () => {
				// Use a setTimeout to ensure all ready callbacks have run before
				// we start playback. This is important for the source selector
				// plugin, which may change sources before playback begins.
				//
				// This is used instead of an event like `canplay` or `loadeddata`
				// because some versions of EdgeHTML don't fire these events.
				// Support: Edge 18
				setTimeout( function () {
					$( indicator.$element ).detach();
					videojsPlayer.play();
					// Focus the player so that keyboard events work
					videojsPlayer.el().focus();
				}, 0 );
			} );
		} );
	}

	/**
	 * Call this method to stop playback and to cleanup
	 * the player after closing the dialog
	 */
	stop() {
		this.loadedPromise.then( ( videojsPlayer ) => {
			videojsPlayer.pause();
			$.disposeDetachedPlayers();
		} );
	}

	makeInactive() {
		this.$content.addClass( 'mw-tmh-inactive' );
	}

	resetActivityTimer() {
		this.$content.removeClass( 'mw-tmh-inactive' );
		clearTimeout( this.activityTimer );
		this.activityTimer = setTimeout( () => this.makeInactive(), INACTIVITY_THRESHOLD );
	}
}

MediaDialog.static.name = 'tmhMediaDialog';
MediaDialog.static.actions = [
	{ icon: 'close', title: mw.msg( 'timedmedia-dialog-close' ), flags: 'safe' }
];

module.exports = MediaDialog;
