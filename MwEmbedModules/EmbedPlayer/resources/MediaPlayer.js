/**
 * mediaPlayer represents a media player plugin.
 *
 * @param {String}
 *      id id used for the plugin.
 * @param {Array}
 *      supported_types an array of supported MIME types.
 * @param {String}
 *      library external script containing the plugin interface code.
 * @constructor
 */
	

function mediaPlayer( id, supported_types, library )
{
	this.id = id;
	this.supported_types = supported_types;
	this.library = library;
	this.loaded = false;
	this.loading_callbacks = new Array();
	return this;
}
mediaPlayer.prototype = {
	// Id of the mediaPlayer
	id:null,

	// Mime types supported by this player
	supported_types:null,

	// Player library ie: native, vlc, java etc.
	library:null,

	// Flag stores the mediaPlayer load state
	loaded:false,

	/**
	 * Checks support for a given MIME type
	 *
	 * @param {String}
	 *      type Mime type to check against supported_types
	 * @return {Boolean} true if mime type is supported false if mime type is
	 *     unsupported
	 */
	supportsMIMEType: function( type ) {
		for ( var i = 0; i < this.supported_types.length; i++ ) {
			if ( this.supported_types[i] == type )
				return true;
		}
		return false;
	},

	/**
	 * Get the "name" of the player from a predictable msg key
	 */
	getName: function() {
		return gM( 'mwe-embedplayer-ogg-player-' + this.id );
	},

	/**
	 * Loads the player library & player skin config ( if needed ) and then
	 * calls the callback.
	 *
	 * @param {Function}
	 *      callback Function to be called once player library is loaded.
	 */
	load: function( callback ) {
		// Load player library ( upper case the first letter of the library )
		mw.load( [
			'mw.EmbedPlayer' + this.library.substr(0,1).toUpperCase() + this.library.substr(1)
		], function() {
			callback();
		} );
	}
};

/**
 * players and supported mime types In an ideal world we would query the plugin
 * to get what mime types it supports in practice not always reliable/available
 *
 * We can't cleanly store these values per library since player library is sometimes
 * loaded post player detection
 */

// Flash based players:
var kplayer = new mediaPlayer('kplayer', ['video/x-flv', 'video/h264'], 'Kplayer');

// Java based player
var cortadoPlayer = new mediaPlayer( 'cortado', ['video/ogg', 'audio/ogg', 'application/ogg'], 'Java' );

// Native html5 players
var oggNativePlayer = new mediaPlayer( 'oggNative', ['video/ogg', 'audio/ogg', 'application/ogg' ], 'Native' );
var h264NativePlayer = new mediaPlayer( 'h264Native', ['video/h264'], 'Native' );
var webmNativePlayer = new mediaPlayer( 'webmNative', ['video/webm'], 'Native' );

// VLC player
var vlcMineList = ['video/ogg', 'audio/ogg', 'application/ogg', 'video/x-flv', 'video/mp4', 'video/h264', 'video/x-msvideo', 'video/mpeg'];
var vlcPlayer = new mediaPlayer( 'vlc-player', vlcMineList, 'Vlc' );

// Generic plugin
var oggPluginPlayer = new mediaPlayer( 'oggPlugin', ['video/ogg', 'application/ogg'], 'Generic' );
