( function () {
	/* global require */
	var videojs = null;
	if ( typeof window.videojs === 'undefined' && typeof require === 'function' ) {
		videojs = require( 'video.js' );
	} else {
		videojs = window.videojs;
	}

	( function ( window, videojs, mw ) {
		var infoButton,
			// defaults = {},
			Button = videojs.getComponent( 'Button' ),
			InfoButton = videojs.extend( Button, {
				constructor: function ( player, link ) {
					this.link = link;
					Button.call( this, player, {} );
					this.controlText( 'More information' );
				},
				handleClick: function () {
					window.navigator.url = window.open( this.link, '_blank' );
				},
				buildCSSClass: function () {
					return Button.prototype.buildCSSClass.call( this ) + ' mw-info-button';
				}
			} );

		/**
		 * Initialize the plugin.
		 * @param {Object} [options] configuration for the plugin
		 */
		infoButton = function ( /* options*/ ) {
			// var settings = videojs.mergeOptions(defaults, options),
			var player = this;

			player.ready( function () {
				var link = mw.config.get( 'wgScript' ) + '?title=' +
					mw.config.get( 'wgFormattedNamespaces' )[ '6' ] + ':' +
					encodeURIComponent( player.el().getAttribute( 'data-mwtitle' ) ),
					button = new InfoButton( Button, link );
				player.controlBar.infoButton = player.controlBar.addChild( button );
			} );
		};

		// register the plugin
		videojs.registerPlugin( 'infoButton', infoButton );
	}( window, videojs, mediaWiki ) );
}() );
