/* global OGVLoader, OGVPlayer */
/** @type {AudioContext?} */
/* global webkitAudioContext */
let context = null;

const AudioContextClass = ( typeof AudioContext === 'function' && AudioContext ) ||
	( typeof webkitAudioContext === 'function' && webkitAudioContext );

class OgvJsSupport {
	/**
	 * Ensure that the OGVPlayer class is loaded before continuing.
	 *
	 * @param {string?} mod - optional module name override
	 * @return {jQuery.Promise<void>}
	 */
	static loadOgvJs( mod = 'ext.tmh.OgvJs' ) {
		return $.Deferred( ( deferred ) => {
			if ( typeof OGVPlayer === 'undefined' ) {
				mw.loader.using( mod, () => {
					OGVLoader.base = OgvJsSupport.basePath();
					deferred.resolve();
				} );
			} else {
				deferred.resolve();
			}
		} ).promise();
	}

	/**
	 * Check if native WebM VP9 playback is available.
	 * It's preferred to use isMediaNativelySupported instead
	 * to check for specific compatibility.
	 *
	 * @param {string} type media MIME type to check for
	 * @return {boolean}
	 */
	static canPlayNatively( type = 'video/webm; codecs="opus,vp9"' ) {
		const el = document.createElement( 'video' );
		return Boolean( el &&
			el.canPlayType &&
			el.canPlayType( type )
		);
	}

	/**
	 * Check if ogv.js would be supported.
	 *
	 * @return {boolean}
	 */
	static isSupported() {
		return !!(
			( typeof WebAssembly === 'object' && WebAssembly ) &&
			AudioContextClass
		);
	}

	/**
	 * Check if loading ogv.js would be needed, and would be supported.
	 *
	 * @return {boolean}
	 */
	static isNeeded() {
		return OgvJsSupport.isSupported() && !OgvJsSupport.canPlayNatively();
	}

	/**
	 * Check if native WebM VP9 playback is available, and if not
	 * then loads the OGVPlayer class before resolving.
	 *
	 * @param {string?} mod - optional module name override
	 * @param {HTMLMediaElement?} media - optional element to check for native support
	 * @return {jQuery.Promise<void>}
	 */
	static loadIfNeeded( mod = 'ext.tmh.OgvJs', media = undefined ) {
		if ( media && OgvJsSupport.isMediaNativelySupported( media ) ) {
			return $.when();
		}
		if ( OgvJsSupport.isNeeded() ) {
			return OgvJsSupport.loadOgvJs( mod );
		}
		return $.when();
	}

	/**
	 * Check if native playback is supported for any of the
	 * sources belonging to this mediaElement
	 *
	 * @param {HTMLMediaElement} mediaElement
	 * @return {boolean}
	 */
	static isMediaNativelySupported( mediaElement ) {
		const sourcesList = mediaElement.querySelectorAll( 'source' );
		for ( const source of sourcesList ) {
			const mediaType = source.getAttribute( 'type' );
			const canPlay = mediaElement.canPlayType( mediaType );
			if ( canPlay ) {
				if ( mediaType === 'video/mpeg' && canPlay === 'maybe' ) {
					// Safari reports "maybe" for "video/mpeg", then doesn't
					// actually support it based on the found video codecs.
					// This produces false positives on old iOS/macOS devices
					// that don't support VP9 in hw. Exclude these, so only
					// those returning 'probably' or another sensible code.
					// But do allow 'maybe' through on other types, namely we
					// need to handle it for application/vnd.apple.mpegurl!
					continue;
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Get a path under the resources/ subdir,
	 *
	 * @param {string} subpath
	 * @return {string}
	 */
	static resourcePath( subpath ) {
		return mw.config.get( 'wgExtensionAssetsPath' ) +
			'/TimedMediaHandler/resources/' + subpath;
	}

	/**
	 * Get the base path of ogv.js and friends.
	 *
	 * @return {string}
	 */
	static basePath() {
		return OgvJsSupport.resourcePath( 'lib/ogv.js' );
	}

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
	static initAudioContext() {
		if ( context ) {
			return context;
		}

		if ( AudioContextClass ) {
			// Workaround for iOS audio output channel issue
			// If there's no <audio> or <video> Safari puts Web Audio onto
			// the ringer channel instead of the media channel!
			const el = document.createElement( 'audio' );
			el.src = OgvJsSupport.resourcePath( 'silence.mp3' );
			el.play();

			context = new AudioContextClass();

			let node;
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
		}
		return context;
	}
}
mw.OgvJsSupport = OgvJsSupport;

module.exports = OgvJsSupport;
