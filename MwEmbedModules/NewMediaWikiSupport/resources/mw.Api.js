/**
* API Helper functions
*/

( function ( mw, $ ) {
	/**
	* mediaWiki JSON a wrapper for jQuery getJSON:
	* ( could also be named mw.apiRequest )
	*
	* The mwEmbed version lets you skip the url part
	* mw.getJSON( [url], data, callback, [timeoutCallback] );
	*
	* Lets you assume:
	* 	url is optional
	* 		( If the first argument is not a string we assume a local mediaWiki api request )
	*   callback parameter is not needed for the request data
	* 	url param 'action'=>'query' is assumed ( if not set to something else in the "data" param
	* 	format is set to "json" automatically
	* 	automatically issues request over "POST" if the request api post type
	*	automatically will setup apiProxy where request is cross domain
	*
	* @param {Mixed} url or data request
	* @param {Mixed} data or callback
	* @param {Function} callback function called on success
	* @param {Function} callbackTimeout - optional function called on timeout
	* 	Setting timeout callback also avoids default timed-out dialog for proxy requests
	*/
	mw.getJSON = function () {
		// Process the arguments:
		var callback, cbinx, timeoutCallback, requestTimeOutFlag, ranCallback,
			// Set up the url
			url = ( typeof arguments[ 0 ] === 'string' ) ? arguments[ 0 ] : mw.getLocalApiUrl(),
			// Set up the data:
			data = ( typeof arguments[ 0 ] === 'object' ) ? arguments[ 0 ] : null;
		if ( !data && typeof arguments[ 1 ] === 'object' ) {
			data = arguments[ 1 ];
		}

		// Setup the callback
		callback = ( typeof arguments[ 1 ] === 'function' ) ? arguments[ 1 ] : false;
		cbinx = 1;
		if ( !callback && ( typeof arguments[ 2 ] === 'function' ) ) {
			callback = arguments[ 2 ];
			cbinx = 2;
		}

		// Setup the timeoutCallback ( function after callback index )
		timeoutCallback = ( typeof arguments[ cbinx + 1 ] === 'function' ) ? arguments[ cbinx + 1 ] : false;

		// Make sure we got a url:
		if ( !url ) {
			mw.log( 'Error: no api url for api request' );
			return;
		}

		// Add default action if unset:
		if ( !data.action ) {
			data.action = 'query';
		}

		// Add default format if not set:
		if ( !data.format ) {
			data.format = 'json';
		}

		// Setup callback wrapper for timeout
		requestTimeOutFlag = false;
		ranCallback = false;

		/**
		 * local callback function to control timeout
		 * @param {Object} data Result data
		 */
		function myCallback( data ) {
			if ( !requestTimeOutFlag ) {
				ranCallback = true;
				callback( data );
			}
		}
		// Set the local timeout call based on AjaxRequestTimeout
		setTimeout( function () {
			if ( !ranCallback ) {
				requestTimeOutFlag = true;
				mw.log( 'Error:: request timed out: ' + url );
				if ( timeoutCallback ) {
					timeoutCallback();
				}
			}
		}, mw.config.get( 'AjaxRequestTimeout' ) * 1000 );

		// mw.log("run getJSON: " + mw.replaceUrlParams( url, data ) );

		// Check if the request requires a "post"
		// eslint-disable-next-line no-underscore-dangle
		if ( mw.checkRequestPost( data ) || data._method === 'post' ) {

			// Check if we need to setup a proxy
			if ( !mw.isLocalDomain( url ) ) {

				// Set local scope ranCallback to true
				// ( ApiProxy handles timeouts internally )
				ranCallback = true;

				// Load the proxy and issue the request
				mw.load( 'ApiProxy', function () {
					mw.ApiProxy.doRequest( url, data, callback, timeoutCallback );
				} );

			} else {
				// Do the request an ajax post
				$.post( url, data, myCallback, 'json' );
			}
			return;
		}

		// If cross domain setup a callback:
		if ( !mw.isLocalDomain( url ) ) {
			if ( url.indexOf( 'callback=' ) === -1 || data.callback === -1 ) {
				// jQuery specific jsonp format: ( second ? is replaced with the callback )
				url += ( url.indexOf( '?' ) === -1 ) ? '?callback=?' : '&callback=?';
			}
		}
		// Pass off the jQuery getJSON request:
		$.getJSON( url, data, myCallback );
	};

	/**
	* Checks if a mw request data requires a post request or not
	* @param {Object} data
	* @return {boolean} The request requires a post
	*/
	mw.checkRequestPost = function ( data ) {
		if ( $.inArray( data.action, mw.config.get( 'MediaWiki.ApiPostActions' ) ) !== -1 ) {
			return true;
		}
		if ( data.prop === 'info' && data.intoken ) {
			return true;
		}
		if ( data.meta === 'userinfo' ) {
			return true;
		}
		return false;
	};

	/**
	* Check if the url is a request for the local domain
	*  relative paths are "local" domain
	* @param {String} url Url for local domain
	* @return {Boolean}
	*	true if url domain is local or relative
	* 	false if the domain is
	*/
	mw.isLocalDomain = function ( url ) {
		if ( (
			url.indexOf( 'http://' ) !== 0 &&
			url.indexOf( '//' ) !== 0 &&
			url.indexOf( 'https://' ) !== 0
		) ||
			new mw.Uri( document.URL ).host === new mw.Uri( url ).host
		) {
			return true;
		}
		return false;
	};

	/**
	* Get the api url for a given content provider key
	* @param {string} providerId
	* @return {Mixed}
	*	url for the provider
	* 	local wiki api if no apiProvider is set
	*/
	mw.getApiProviderURL = function ( providerId ) {
		if ( mw.config.get( 'MediaWiki.ApiProviders' ) &&
			mw.config.get( 'MediaWiki.ApiProviders' )[ providerId ]
		) {
			return mw.config.get( 'MediaWiki.ApiProviders' )[ providerId ].url;
		}
		return mw.getLocalApiUrl();
	};

	/**
	* Get Api URL from mediaWiki page defined variables
	* @return {Mixed}
	* 	api url
	* 	false
	*/
	mw.getLocalApiUrl = function () {
		if ( typeof mw.config.get( 'wgServer' ) !== 'undefined' &&
			typeof mw.config.get( 'wgScriptPath' ) !== 'undefined'
		) {
			return	mw.config.get( 'wgServer' ) +
				mw.config.get( 'wgScriptPath' ) + '/api.php';
		}
		mw.log( 'Error trying to get local api url without ' );
		return false;
	};

}( window.mediaWiki, window.jQuery ) );
