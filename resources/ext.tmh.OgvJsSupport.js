( function ( $, mw ) {

	var support = mw.OgvJsSupport = {
		/**
		 * Ensure that the OGVPlayer class is loaded before continuing.
		 *
		 * @return {jQuery.Deferred}
		 */
		loadOgvJs: function () {
			return $.Deferred( function ( deferred ) {
				if ( typeof OGVPlayer === 'undefined' ) {
					mw.loader.using( 'ext.tmh.OgvJs', function () {
						OGVLoader.base = support.basePath();
						deferred.resolve();
					} );
				} else {
					deferred.resolve();
				}
			} );
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
			var AudioContext = window.AudioContext || window.webkitAudioContext;
			if ( AudioContext ) {
				var context = new AudioContext(),
					node;
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
