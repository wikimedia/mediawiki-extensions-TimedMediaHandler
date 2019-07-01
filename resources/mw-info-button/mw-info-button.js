( function () {
	/* global require */
	var videojs = null;
	if ( typeof window.videojs === 'undefined' && typeof require === 'function' ) {
		videojs = require( 'video.js' );
	} else {
		videojs = window.videojs;
	}

	( function () {
		var infoButton,
			// defaults = {},
			Button = videojs.getComponent( 'Button' ),
			InfoButton = videojs.extend( Button, {
				constructor: function ( player, options ) {
					this.link = options.link;
					Button.call( this, player, {} );
				},
				handleClick: function () {
					window.navigator.url = window.open( this.link, '_blank' );
				},
				buildCSSClass: function () {
					return Button.prototype.buildCSSClass.call( this ) + ' mw-info-button';
				}
			} );

		// Register the component with Video.js, so it can be used in players.
		InfoButton.prototype.controlText_ = 'More information';
		videojs.registerComponent( 'InfoButton', InfoButton );

		/**
		 * Initialize the plugin.
		 * @param {Object} [options] configuration for the plugin
		 */
		infoButton = function ( /* options*/ ) {
			// var settings = videojs.mergeOptions(defaults, options),
			var player = this;

			player.ready( function () {
				var title = mw.Title.makeTitle(
					mw.config.get( 'wgNamespaceIds' ).file,
					player.el().getAttribute( 'data-mwtitle' )
				);

				if ( mw.config.get( 'wgTitle' ) !== player.el().getAttribute( 'data-mwtitle' ) ) {
					player.controlBar.infoButton = player.controlBar.addChild( 'InfoButton', { link: title.getUrl() } );
				}
			} );
		};

		// register the plugin
		videojs.registerPlugin( 'infoButton', infoButton );
	}() );
}() );
