// Add support for html5 / mwEmbed elements to IE
// For discussion and comments, see: http://remysharp.com/2009/01/07/html5-enabling-script/
'video audio source track'.replace( /\w+/g, function ( n ) { document.createElement( n ); } );

/**
 * NewMwEmbedSupport includes shared mwEmbed utilities that either
 * wrap core mediawiki functionality or support legacy mwEmbed module code
 *
 * @license
 * mwEmbed
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * @copyright (C) 2010 Kaltura
 * @author Michael Dale ( michael.dale at kaltura.com )
 *
 * @url http://www.kaltura.org/project/HTML5_Video_Media_JavaScript_Library
 *
 * Libraries used include code license in headers
 *
 * @dependencies
 */

( function ( mw, $ ) {

	/**
	 * mw.ready method
	 * @deprecated
	 * @param {Function} callback Callback
	 */
	mw.ready = function ( callback ) {
		mw.log( 'Error mw.ready has been deprecated.' );
		// use the native jQuery dom ready call:
		$( callback );
	};

	mw.setConfig = function ( name, value ) {
		mediaWiki.config.set( name, value );
	};
	mw.getConfig = function ( name, value ) {
		return mediaWiki.config.get( name, value );
	};
	mw.setDefaultConfig = function ( name, value ) {
		if ( mediaWiki.config.get( name ) === null ) {
			mediaWiki.config.set( name, value );
		}
	};
	/**
	 * Set any pre-mwEmbed embed configuration
	 */
	if ( typeof window.preMwEmbedConfig !== 'undefined' ) {
		mw.setConfig( window.preMwEmbedConfig );
	}

	/**
	 * Aliased load function
	 *
	 * @param {string[]} resources Resources
	 * @param {Function} callback Callback
	 */
	mw.load = function ( resources, callback ) {
		mediaWiki.loader.using( resources, callback, function () {
			// failed to load
			mw.log( 'Failed to load resources:' + resources );
		} );
	};

	mw.getEmbedPlayerPath = function () {
		if ( mediaWiki.config.get( 'wgExtensionAssetsPath' ) ) {
			return mediaWiki.config.get( 'wgExtensionAssetsPath' ) + '/TimedMediaHandler/MwEmbedModules/EmbedPlayer';
		} else if ( mediaWiki.config.get( 'wgLoadScript' ) ) {
			return mw.getMwEmbedPath() + 'modules/EmbedPlayer';
		}
	};

	/**
	 * legacy support to get the mwEmbed resource path:
	 *
	 * @return {string|boolean}
	 */
	mw.getMwEmbedPath = function () {
		// check for wgExtensionAssetsPath path ( running in mediaWiki instance )
		if ( mediaWiki.config.get( 'wgLoadScript' ) ) {
			return mediaWiki.config.get( 'wgLoadScript' ).replace( 'load.php', '' );
		}
		return false;
	};

	/**
	 * Merge in a configuration value:
	 *
	 * @param {string} name Config name
	 * @param {Mixed} value Config value
	 */
	mw.mergeConfig = function ( name, value ) {
		var existingValue, i;
		if ( typeof name === 'object' ) {
			$.each( name, function ( inx, val ) {
				mw.mergeConfig( inx, val );
			} );
			return;
		}
		existingValue = mediaWiki.config.get( name );
		if ( !existingValue || typeof existingValue !== 'object' ) {
			mw.setConfig( name, value );
			return;
		}
		if ( typeof existingValue === 'object' ) {
			if ( $.isArray( existingValue ) && $.isArray( value ) ) {
				for ( i = 0; i < value.length; i++ ) {
					existingValue.push( value[ i ] );
				}
				mw.setConfig( name, $.uniqueArray( existingValue ) );
			} else {
				mw.setConfig( name, $.extend( {}, existingValue, value ) );
			}
			return;
		}
	};

	/**
	 * Simple inheritance. We will move to something like
	 * http://javascriptmvc.com/docs.html#&who=jQuery.Class
	 * in the near future. This is just a stop gap.
	 *
	 * @param {Function} _this Subclass
	 * @param {Function} inhertParent Parent class
	 */
	mw.inherit = function ( _this, inhertParent ) {
		var method;
		for ( method in inhertParent ) {
			if ( _this[ method ] ) {
				_this[ 'parent_' + method ] = inhertParent[ method ];
			} else {
				_this[ method ] = inhertParent[ method ];
			}
		}
	};

	/**
	 * Utility Functions
	 */

	/**
	 * Checks if a string is a url ( parsed success by mw.Uri )
	 * @param {string} url URL version to be checked with mw.Uri
	 * @return {boolean}
	 */
	mw.isUrl = function ( url ) {
		try {
			// eslint-disable-next-line no-new
			new mw.Uri( url );
			return true;
		} catch ( e ) {
			// no error
		}
		return false;
	};

	/**
	 * A version comparison utility function Handles version of types
	 * {Major}.{MinorN}.{Patch}
	 *
	 * Note this just handles version numbers not patch letters.
	 *
	 * @param {string} minVersion Minimum version needed
	 * @param {string} clientVersion Client version to be checked
	 * @return {boolean} The version is at least of minVersion
	 */
	mw.versionIsAtLeast = function ( minVersion, clientVersion ) {
		var i,
			minVersionParts = minVersion.split( '.' ),
			clientVersionParts = clientVersion.split( '.' );
		for ( i = 0; i < minVersionParts.length; i++ ) {
			if ( parseInt( clientVersionParts[ i ] ) > parseInt( minVersionParts[ i ] ) ) {
				return true;
			}
			if ( parseInt( clientVersionParts[ i ] ) < parseInt( minVersionParts[ i ] ) ) {
				return false;
			}
		}
		// Same version:
		return true;
	};

	/**
	 * addLoaderDialog small helper for displaying a loading dialog
	 *
	 * @param {string} dialogHtml HTML of the loader msg
	 * @return {jQuery} Dialog element
	 */
	mw.addLoaderDialog = function ( dialogHtml ) {
		var $dialog;
		if ( !dialogHtml ) {
			dialogHtml = mw.msg( 'mwe-loading' );
		}
		$dialog = mw.addDialog( {
			title: dialogHtml,
			content: dialogHtml + '<br>' +
				$( '<div />' )
					.loadingSpinner()
					.html()
		} );
		return $dialog;
	};

	/**
	 * Add a dialog window:
	 *
	 * @param {Object} options Options object, any jquery.ui.dialog option and:
	 * @param {string} options.title Title string for the dialog
	 * @param {string} options.content to be inserted in msg box
	 * @param {Object} options.buttons A button object for the dialog. Can be a string for the close button
	 * @return {jQuery} Dialog element
	 */
	mw.addDialog = function ( options ) {
		var uiRequest, buttonMsg;
		// Remove any other dialog
		$( '#mweDialog' ).remove();

		if ( !options ) {
			options = {};
		}

		// Extend the default options with provided options
		options = $.extend( {
			bgiframe: true,
			draggable: true,
			resizable: false,
			modal: true
		}, options );

		if ( !options.title || !options.content ) {
			mw.log( 'Error: mwEmbed addDialog missing required options ( title, content ) ' );
		}

		// Append the dialog div on top:
		$( 'body' ).append(
			$( '<div />' )
				.attr( {
					id: 'mweDialog',
					title: options.title
				} )
				.css( {
					display: 'none'
				} )
				.append( options.content )
		);

		// Build the uiRequest
		uiRequest = [ 'jquery.ui.dialog' ];
		if ( options.draggable ) {
			uiRequest.push( 'jquery.ui.draggable' );
		}
		if ( options.resizable ) {
			uiRequest.push( 'jquery.ui.resizable' );
		}

		// Special button string
		if ( typeof options.buttons === 'string' ) {
			buttonMsg = options.buttons;
			options.buttons = {};
			options.buttons[ buttonMsg ] = function () {
				$( this ).dialog( 'close' );
			};
		}

		// Load the dialog resources
		mw.load( uiRequest, function () {
			$( '#mweDialog' ).dialog( options );
		} );
		return $( '#mweDialog' );
	};

	/**
	 * Close the loader dialog created with addLoaderDialog
	 */
	mw.closeLoaderDialog = function () {
		$( '#mweDialog' ).dialog( 'destroy' ).remove();
	};

	// An event once mwEmbedSupport is Ready,
	$( mw ).trigger( 'MwEmbedSupportReady' );

	/**
	 * Convert hexadecimal string to HTML color code
	 *
	 * @param {string} color Color code in hexadecimal notation
	 * @return {string} HTML color code
	 */
	mw.getHexColor = function ( color ) {
		var len, pre, i;
		if ( typeof color === 'string' && color.substr( 0, 2 ) === '0x' ) {
			return color.replace( '0x', '#' );
		} else {
			color = parseInt( color );
			color = color.toString( 16 );
			len = 6 - color.length;
			if ( len > 0 ) {
				pre = '';
				for ( i = 0; i < len; i++ ) {
					pre += '0';
				}
				color = pre + color;
			}
			return '#' + color;
		}
	};

	/*
	 * Send beacon ( used by ads and analytics plugins )
	 * @param {String} Beacon URL to load
	 */
	mw.sendBeaconUrl = function ( beaconUrl ) {
		var beacon = new Image();
		beacon.src = beaconUrl;
	};

}( mediaWiki, jQuery ) );
