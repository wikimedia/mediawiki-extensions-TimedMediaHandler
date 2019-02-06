/**
 * Show a prompt to install WebM plugin for IE 9+
 */

( function () {
	'use strict';

	mw.EmbedPlayerIEWebMPrompt = {
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
		instanceOf: 'IEWebMPrompt',

		/*
		 * Embed this "fake" player
		 *
		 * @return {string} embed code to link to WebM plugin download
		 */
		embedPlayerHTML: function () {
			var pluginUrl = 'https://tools.google.com/dlpage/webmmf/',
				$link;

			// Overlay the video placeholder with download plugin link.
			$link = $( '<a>' )
				.attr( 'href', pluginUrl )
				.attr( 'target', '_blank' )
				.text( mw.msg( 'mwe-embedplayer-iewebmprompt-linktext' ) );
			$( this ).append( $( '<div>' ).addClass( 'iewebm-prompt' )
				.width( this.getWidth() )
				.height( this.getHeight() )
				.append( $( '<div>' ).text( mw.msg( 'mwe-embedplayer-iewebmprompt-intro' ) ) )
				.append( $link )
				.append( $( '<div>' ).text( mw.msg( 'mwe-embedplayer-iewebmprompt-outro' ) ) )
			);
		}
	};

}() );
