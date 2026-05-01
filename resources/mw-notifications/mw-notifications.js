'use strict';
const Plugin = videojs.getPlugin( 'plugin' );

class MwNotifications extends Plugin {
	constructor( player, options ) {
		super( player, options );

		this.osdEl = document.createElement( 'div' );
		this.osdEl.className = 'vjs-mw-notifications vjs-mw-notifications-hidden';
		this.osdEl.setAttribute( 'aria-hidden', '' );

		// element used to announce OSD messages to screen readers
		this.osdAriaEl = document.createElement( 'span' );
		this.osdAriaEl.className = 'vjs-mw-notifications-a11y';
		this.osdAriaEl.setAttribute( 'role', 'status' );
		this.osdAriaEl.setAttribute( 'aria-live', 'polite' );
		this.osdAriaEl.setAttribute( 'aria-atomic', 'true' );

		this.hideTimeout = null;
		this.clearTimeout = null;

		player.ready( () => {
			player.el().appendChild( this.osdEl );
			player.el().appendChild( this.osdAriaEl );
		} );

		player.on( 'ratechange', () => {
			this.show(
				mw.msg( 'videojs-rate-change', player.playbackRate() ),
				mw.msg( 'videojs-a11y-rate-change', player.playbackRate() )
			);
		} );
		player.on( 'mwSeek', ( e ) => {
			const direction = ( e.seconds > 0 ? 'forward' : 'backward' );
			this.show(
				// Messages that can be used here:
				// * videojs-seek-forward
				// * videojs-seek-backward
				// * videojs-a11y-seek-forward
				// * videojs-a11y-seek-backward
				mw.msg( 'videojs-seek-' + direction, Math.abs( e.seconds ) ),
				mw.msg( 'videojs-a11y-seek-' + direction, Math.abs( e.seconds ) )
			);
		} );
		player.on( 'mwFrameStep', ( e ) => {
			const direction = ( e.direction > 0 ? 'forward' : 'backward' );
			this.show(
				// Messages that can be used here:
				// * videojs-step-forward
				// * videojs-step-backward
				// * videojs-a11y-step-forward
				// * videojs-a11y-step-backward
				mw.msg( 'videojs-step-' + direction ),
				mw.msg( 'videojs-a11y-step-' + direction )
			);
		} );

		player.on( 'pause', () => {
			if ( !player.ended() ) {
				this.osdAriaEl.textContent = mw.msg( 'videojs-a11y-pause' );
			}
		} );

		player.on( 'ended', () => {
			this.osdAriaEl.textContent = mw.msg( 'videojs-a11y-ended' );
		} );

		player.on( 'volumechange', () => {
			if ( this.player.muted() ) {
				this.osdAriaEl.textContent = mw.msg( 'videojs-a11y-muted' );
			}
		} );
	}

	/**
	 * Show a brief message in the player overlay.
	 *
	 * @param {string} content
	 * @param {string} accessibilityLabel
	 */
	show( content, accessibilityLabel ) {
		this.osdAriaEl.textContent = accessibilityLabel || content;

		this.osdEl.textContent = content;
		this.osdEl.classList.remove( 'vjs-mw-notifications-hidden' );
		this.osdEl.classList.add( 'vjs-mw-notifications-visible' );

		clearTimeout( this.hideTimeout );
		clearTimeout( this.clearTimeout );

		this.hideTimeout = setTimeout( () => {
			this.osdEl.classList.remove( 'vjs-mw-notifications-visible' );
		}, 1000 );

		// After the fadeout, hide completely
		this.clearTimeout = setTimeout( () => {
			this.osdEl.textContent = '';
			this.osdEl.classList.add( 'vjs-mw-notifications-hidden' );
		}, 1150 );
	}

	dispose() {
		clearTimeout( this.hideTimeout );
		clearTimeout( this.clearTimeout );
		if ( this.osdEl.parentNode ) {
			this.osdEl.remove();
		}
		if ( this.osdAriaEl.parentNode ) {
			this.osdAriaEl.remove();
		}
		super.dispose();
	}
}

videojs.registerPlugin( 'mwPlayerNotifications', MwNotifications );
module.exports = MwNotifications;
