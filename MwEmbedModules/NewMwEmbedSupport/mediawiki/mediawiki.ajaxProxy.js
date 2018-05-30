( function ( mw, $ ) {

	// Setup ajaxProxy module
	var ajaxProxy = function ( options ) {
		var defaults;

		// Check if we have success callback
		if ( !$.isFunction( options.success ) ) {
			mw.log( 'mw.ajaxProxy :: Error: missing success callback.' );
			return;
		}

		// Check for url
		if ( !options.url ) {
			mw.log( 'mw.ajaxProxy :: Error: missing url to proxy.' );
		}

		// Setup default vars
		defaults = {
			error: function () {},
			proxyUrl: mw.config.get( 'Mw.XmlProxyUrl' ),
			proxyType: 'jsonp',
			startWithProxy: false
		};

		// Merge options with defaults
		this.options = $.extend( {}, defaults, options );

		// Make request
		this.ajax();
	};

	ajaxProxy.prototype = {

		/*
		 * Make an ajax request, fallback to proxy service
		 */
		ajax: function ( useProxy ) {
			var ajaxOptions,
				self = this;

			if ( self.options.startWithProxy ) {
				self.proxy();
				return;
			}

			ajaxOptions = {
				success: function ( result ) {
					self.handleResult( result );
				}
			};

			if ( useProxy ) {
				ajaxOptions.url = self.options.proxyUrl + encodeURIComponent( self.options.url );
				ajaxOptions.error = function () {
					mw.log( 'mw.ajaxProxy :: Error: request failed with proxy.' );
					self.options.error();
				};
			} else {
				ajaxOptions.url = self.options.url;
				ajaxOptions.error = function () {
					mw.log( 'mw.ajaxProxy :: Error: cross domain request failed, trying with proxy' );
					self.proxy();
				};
			}

			// make the request
			try {
				$.ajax( ajaxOptions );
			} catch ( e ) {
				// do nothing
			}
		},

		/*
		 * Make proxy request
		 */
		proxy: function () {
			var self = this;
			if ( self.options.proxyUrl ) {
				// decide if to use ajax or jsonp
				if ( self.options.proxyType === 'jsonp' ) {
					$.ajax( {
						url: self.options.proxyUrl + '?url=' + encodeURIComponent( self.options.url ) + '&callback=?',
						dataType: 'json',
						success: function ( result ) {
							self.handleResult( result, true );
						},
						error: function ( error ) {
							mw.log( 'mw.ajaxProxy :: Error: could not load:', error );
							self.options.error();
						}
					} );
				} else {
					self.ajax( true );
				}
			} else {
				mw.log( 'mw.ajaxProxy :: Error: please setup proxy configuration' );
				this.options.error();
			}
		},

		/*
		 * Handle request result ( parse xml )
		 */
		handleResult: function ( result, isJsonP ) {
			var resultXML,
				self = this;
			if ( isJsonP ) {
				if ( result.http_code === 'ERROR' || result.http_code === 0 ) {
					mw.log( 'mw.ajaxProxy :: Error: load error with http response' );
					self.options.error();
					return;
				}
				try {
					resultXML = $.parseXML( result.contents );
				} catch ( e ) {
					mw.log( 'mw.ajaxProxy :: Error: could not parse:', resultXML );
					self.options.error();
					return;
				}
				self.options.success( resultXML );
			} else {
				self.options.success( result );
			}
		}
	};

	// Export our module to mw global object
	mw.ajaxProxy = ajaxProxy;

}( mediaWiki, jQuery ) );
