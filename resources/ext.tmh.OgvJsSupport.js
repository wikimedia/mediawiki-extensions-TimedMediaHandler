/* global OGVLoader */
( function () {

	var context = null,
		support = mw.OgvJsSupport = {
			/**
			 * Ensure that the OGVPlayer class is loaded before continuing.
			 *
			 * @param {string?} mod - optional module name override
			 * @return {jQuery.Promise}
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
				} ).promise();
			},

			/**
			 * Check if native WebM VP9 playback is available.
			 *
			 * @return {boolean}
			 */
			canPlayNatively: function () {
				var el = document.createElement( 'video' );
				if ( el && el.canPlayType && (
					el.canPlayType( 'video/webm; codecs="opus,vp9"' ) || (
						// Safari 12 workaround, where safari incorrectly reports not to support opus on webm files
						typeof MediaSource !== 'undefined' && MediaSource.isTypeSupported( 'video/webm; codecs="vp9, opus"' )
					) )
				) {
					return true;
				}
				return false;
			},

			/**
			 * Check if ogv.js would be supported.
			 *
			 * @return {boolean}
			 */
			isSupported: function () {
				return !!( window.WebAssembly && ( window.AudioContext || window.webkitAudioContext ) );
			},

			/**
			 * Check if loading ogv.js would be needed, and would be supported.
			 *
			 * @return {boolean}
			 */
			isNeeded: function () {
				return this.isSupported() && !this.canPlayNatively();
			},

			/**
			 * Check if native WebM VP9 playback is available, and if not
			 * then loads the OGVPlayer class before resolving.
			 *
			 * @param {string?} mod - optional module name override
			 * @return {jQuery.Promise}
			 */
			loadIfNeeded: function ( mod ) {
				mod = mod || 'ext.tmh.OgvJs';
				if ( this.isNeeded() ) {
					return this.loadOgvJs( mod );
				} else {
					return $.Deferred( function ( deferred ) {
						deferred.resolve();
					} ).promise();
				}
			},

			/**
			 * Get the base path of ogv.js and friends.
			 *
			 * @return {string}
			 */
			basePath: function () {
				return mw.config.get( 'wgExtensionAssetsPath' ) + '/TimedMediaHandler/resources/mwembed/lib/binPlayers/ogv.js';
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
				var node,
					AudioContext = window.AudioContext || window.webkitAudioContext;
				if ( context ) {
					return context;
				}
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
				}
				return null;
			}
		};

}() );
