/* global OGVLoader */
( function () {

	let context = null;
	const support = mw.OgvJsSupport = {
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
			const el = document.createElement( 'video' );
			return Boolean( el && el.canPlayType && el.canPlayType( 'video/webm; codecs="opus,vp9"' ) );
		},

		/**
		 * Check if ogv.js would be supported.
		 *
		 * @return {boolean}
		 */
		isSupported: function () {
			return !!( window.WebAssembly && (
				window.AudioContext || window.webkitAudioContext
			) );
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
		 * @param {HTMLMediaElement?} media - optional element to check for native support
		 * @return {jQuery.Promise}
		 */
		loadIfNeeded: function ( mod, media ) {
			mod = mod || 'ext.tmh.OgvJs';
			if ( media && this.isMediaNativelySupported( media ) ) {
				return $.when();
			}
			if ( this.isNeeded() ) {
				return this.loadOgvJs( mod );
			}
			return $.when();
		},

		/**
		 * Check if native playback is supported for any of the
		 * sources belonging to this mediaElement
		 *
		 * @param {HTMLMediaElement} mediaElement
		 * @return {boolean}
		 */
		isMediaNativelySupported: function ( mediaElement ) {
			let mediaType, canPlay;
			let supportedNatively = false;
			const sourcesList = mediaElement.querySelectorAll( 'source' );
			// IE11: NodeList.forEach
			Array.prototype.forEach.call( sourcesList, function ( source ) {
				mediaType = source.getAttribute( 'type' );
				canPlay = mediaElement.canPlayType( mediaType );
				if ( canPlay === true || canPlay === 'probably' ) {
					// Safari reports "maybe" for "video/mpeg", then doesn't
					// actually support it based on the found video codecs.
					// This produces false positives on old iOS/macOS devices
					// that don't support VP9 in hw. Exclude these, so only
					// those returning 'probably' or another sensible code.
					// Very very old browsers may return boolean `true`.
					supportedNatively = true;
				}
			} );
			return supportedNatively;
		},

		/**
		 * Get the base path of ogv.js and friends.
		 *
		 * @return {string}
		 */
		basePath: function () {
			return mw.config.get( 'wgExtensionAssetsPath' ) + '/TimedMediaHandler/resources/ogv.js';
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
			let node;
			const AudioContext = window.AudioContext || window.webkitAudioContext;
			if ( context ) {
				return context;
			}
			if ( AudioContext ) {
				// Workaround for iOS audio output channel issue
				// If there's no <audio> or <video> Safari puts Web Audio onto
				// the ringer channel instead of the media channel!
				const el = document.createElement( 'audio' );
				el.src = mw.config.get( 'wgExtensionAssetsPath' ) + '/TimedMediaHandler/resources/silence.mp3';
				el.play();

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
