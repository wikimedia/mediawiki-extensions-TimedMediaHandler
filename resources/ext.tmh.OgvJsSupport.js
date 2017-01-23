/* global OGVLoader */
( function ( $, mw ) {

	var support = mw.OgvJsSupport = {
		/**
		 * Ensure that the OGVPlayer class is loaded before continuing.
		 *
		 * @param {string?} mod - optional module name override
		 * @return {jQuery.Deferred}
		 */
		loadOgvJs: function ( mod ) {
			mod = mod || 'ext.tmh.OgvJs';
			return $.Deferred( function ( deferred ) {
				if ( typeof OGVPlayer === 'undefined' ) {
					mw.loader.using( mod, function () {
						OGVLoader.base = support.basePath();
						deferred.resolve();
					} );
				} else {
					deferred.resolve();
				}
			} );
		},

		/**
		 * Check if native Ogg playback is available.
		 *
		 * @return {boolean}
		 */
		canPlayNatively: function () {
			var el = document.createElement( 'video' );
			if ( el && el.canPlayType && el.canPlayType( 'application/ogg' ) ) {
				return true;
			}
			return false;
		},

		/**
		 * Check if native Ogg playback is available, and if not
		 * then loads the OGVPlayer class before resolving.
		 *
		 * @param {string?} mod - optional module name override
		 * @return {jQuery.Deferred}
		 */
		loadIfNeeded: function ( mod ) {
			mod = mod || 'ext.tmh.OgvJs';
			if ( this.canPlayNatively() ) {
				// Has native ogg playback support, resolve immediately.
				return $.Deferred( function ( deferred ) {
					deferred.resolve();
				} );
			} else {
				return this.loadOgvJs( mod );
			}
		},

		/**
		 * Get the base path of ogv.js and friends.
		 *
		 * @return {string}
		 */
		basePath: function () {
			var ext = mw.config.get( 'wgExtensionAssetsPath' ),
				binPlayers = ext + '/TimedMediaHandler/MwEmbedModules/EmbedPlayer/binPlayers';
			return binPlayers + '/ogv.js';
		},

		/**
		 * Return a stub audio context
		 *
		 * This is used for iOS Safari to enable Web Audio by triggering an empty
		 * audio output channel during a user input event handler. Without that,
		 * audio is left disabled and won't work when we start things up after an
		 * asynchronous code load.
		 *
		 * @return {AudioContext|null}
		 */
		initAudioContext: function () {
			var context, node,
				AudioContext = window.AudioContext || window.webkitAudioContext;
			if ( AudioContext ) {
				context = new AudioContext();

				if ( context.createScriptProcessor ) {
					node = context.createScriptProcessor( 1024, 0, 2 );
				} else if ( context.createJavaScriptNode ) {
					node = context.createJavaScriptNode( 1024, 0, 2 );
				} else {
					throw new Error( 'Bad version of web audio API?' );
				}

				// Don't actually run any audio, just start & stop the node
				node.connect( context.destination );
				node.disconnect();

				return context;
			} else {
				return null;
			}
		}
	};

}( jQuery, mediaWiki ) );
