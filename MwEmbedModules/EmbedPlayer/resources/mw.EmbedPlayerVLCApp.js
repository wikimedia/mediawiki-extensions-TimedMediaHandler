/**
 * Play the video using the vlc app on iOS
 */

( function ( mw, $ ) {
	'use strict';

	mw.EmbedPlayerVLCApp = {
		// List of supported features (or lack thereof)
		supports: {
			playHead: false, /* The seek slider */
			pause: true, /* Play pause button in control bar */
			stop: true, /* Does this actually do anything?? */
			fullscreen: false,
			timeDisplay: false,
			volumeControl: false
		},

		// Instance name:
		instanceOf: 'VLCApp',

		/*
		 * Embed this "fake" player
		 *
		 * @return {String}
		 * 	embed code to link to VLC app
		 */
		embedPlayerHTML: function () {
			var fileUrl = this.getSrc( this.seekTimeSec ),
				vlcUrl = 'vlc://' + ( new mw.Uri( fileUrl ) ).toString(),
				appStoreUrl = '//itunes.apple.com/us/app/vlc-for-ios/id650377962',
				appInstalled = false,
				promptInstallTimeout,
				$link,
				startTime;

			// Replace video with download in vlc link.
			// the <span> ends up being not used as we get the html via .html()
			$link = $( '<span></span>' ).append( $( '<a></a>' ).attr( 'href', appStoreUrl ).append(
				mw.html.escape( mw.msg( 'mwe-embedplayer-vlcapp-vlcapplinktext' ) )
			) );
			$( this ).html( $( '<div class="vlcapp-player"></div>' )
				.width( this.getWidth() )
				.height( this.getHeight() )
				.append(
				// mw.msg doesn't have rawParams() equivalent. Lame.
					mw.html.escape(
						mw.msg( 'mwe-embedplayer-vlcapp-intro' )
					).replace( /\$1/g, $link.html() )
				).append( $( '<ul></ul>' )
					.append( $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', appStoreUrl )
						.text( mw.msg( 'mwe-embedplayer-vlcapp-downloadapp' ) ) )
					).append( $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', vlcUrl )
						.text( mw.msg( 'mwe-embedplayer-vlcapp-openvideo' ) ) )
					).append( $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', fileUrl )
						.text( mw.msg( 'mwe-embedplayer-vlcapp-downloadvideo' ) ) )
					)
				)
			);

			// Try to auto-open in vlc.
			// Based on http://stackoverflow.com/questions/627916/check-if-url-scheme-is-supported-in-javascript

			$( window ).one( 'pagehide', function () {
				appInstalled = true;
				if ( promptInstallTimeout ) {
					window.clearTimeout( promptInstallTimeout );
				}
			} );
			startTime = ( new Date() ).getTime();
			try {
				window.location = vlcUrl;
			} catch ( e ) {
			// Just to be safe, ignore any exceptions
			// However, it appears iOS doesn't throw any. Other browsers do.
			}
			promptInstallTimeout = window.setTimeout( function () {
				var install;
				if ( appInstalled ) {
					return;
				}
				if ( document.hidden || document.webkitHidden ) {
				// browser still running, but in background.
				// probably means an App was opened up.
					return;
				}
				if ( ( new Date() ).getTime() - 15000 > startTime ) {
				// We switched to VLC more than fifteen seconds ago.
				// Probably we succesfully switched and the other detection
				// methods failed.
					return;
				}
				// eslint-disable-next-line no-alert
				install = confirm( mw.msg( 'mwe-embedplayer-vlcapp-vlcapppopup' ) );
				if ( install ) {
					window.location = appStoreUrl;
				}
				// Note about timeout: iPad air needs longer than an iPhone
			}, 1000 );
		}
	};

}( mediaWiki, jQuery ) );
