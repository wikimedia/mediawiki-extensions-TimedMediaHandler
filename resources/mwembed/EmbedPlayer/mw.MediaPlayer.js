/**
 * mediaPlayer represents a media player plugin.
 *
 * @param {string} id ID used for the plugin.
 * @param {string[]} supportedTypes an array of supported MIME types.
 * @param {string} library External script containing the plugin interface code.
 * @constructor
 */
( function () {
	'use strict';

	mw.MediaPlayer = function ( id, supportedTypes, library ) {
		this.id = id;
		this.supportedTypes = supportedTypes;
		this.library = library;
		this.loaded = false;
		return this;
	};
	mw.MediaPlayer.prototype = {
		// Id of the mediaPlayer
		id: null,

		// Mime types supported by this player
		supportedTypes: null,

		// Player library ie: native, vlc etc.
		library: null,

		// Flag stores the mediaPlayer load state
		loaded: false,

		/**
		 * Checks support for a given MIME type
		 *
		 * @param {string} type Mime type to check against supportedTypes
		 * @return {boolean} The mime type is supported
		 */
		supportsMIMEType: function ( type ) {
			return this.supportedTypes.indexOf( type ) !== -1;
		},

		/**
		 * Get the "name" of the player from a predictable msg key
		 * @return {String} Player name. Unescaped plaintext, might be from untrusted source.
		 */
		getName: function () {
			// Give grep a chance to find the usages:
			// mwe-embedplayer-ogg-player-vlc-player, mwe-embedplayer-ogg-player-oggNative, mwe-embedplayer-ogg-player-mp3Native,
			// mwe-embedplayer-ogg-player-aacNative, mwe-embedplayer-ogg-player-h264Native, mwe-embedplayer-ogg-player-webmNative,
			// mwe-embedplayer-ogg-player-oggPlugin, mwe-embedplayer-ogg-player-quicktime-mozilla,
			// mwe-embedplayer-ogg-player-quicktime-activex, mwe-embedplayer-ogg-player-cortado,
			// mwe-embedplayer-ogg-player-flowplayer, mwe-embedplayer-ogg-player-kplayer, mwe-embedplayer-ogg-player-selected,
			// mwe-embedplayer-ogg-player-omtkplayer
			return mw.msg( 'mwe-embedplayer-ogg-player-' + this.id );
		},

		/**
		 * Loads the player library & player skin config ( if needed ) and then
		 * calls the callback.
		 *
		 * @param {Function}
		 *      callback Function to be called once player library is loaded.
		 */
		load: function ( callback ) {
			// Load player library ( upper case the first letter of the library )

			mw.loader.using( [
				'mw.EmbedPlayer' + this.library.substr( 0, 1 ).toUpperCase() + this.library.substr( 1 )
			], function () {
				if ( callback ) {
					callback();
				}
			} );
		}
	};

}() );
