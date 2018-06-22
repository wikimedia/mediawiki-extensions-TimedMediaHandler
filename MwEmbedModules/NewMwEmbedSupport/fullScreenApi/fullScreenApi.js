/*
Native FullScreen JavaScript API

Simple fullscreen api wrapper based on John Dyer's blog post on the subject:
http://johndyer.name/native-fullscreen-javascript-api-plus-jquery-plugin/

-------------
Assumes Mozilla naming conventions instead of W3C for now
*/
( function() {

	var fullScreenApi = {
			supportsFullScreen: false,
			isFullScreen: function() { return false; },
			requestFullScreen: function() {},
			cancelFullScreen: function() {},
			fullScreenEventName: '',
			prefix: ''
		},
		browserPrefixes = 'webkit moz o ms khtml'.split(' ');

	// check for native support
	if ( typeof document.exitFullscreen != 'undefined') {
		fullScreenApi.supportsFullScreen = true;
	} else {
		// check for fullscreen support by vendor prefix
		for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {
			fullScreenApi.prefix = browserPrefixes[i];

			// 'New' way, used by ms, webkit
			if (typeof document[fullScreenApi.prefix + 'ExitFullscreen' ] != 'undefined' ) {
				fullScreenApi.supportsFullScreen = true;

				break;
			}
			// 'Old' way, used by moz, webkit
			if (typeof document[fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
				fullScreenApi.supportsFullScreen = true;

				break;
			}
		}
	}

	// update methods to do something useful
	if (fullScreenApi.supportsFullScreen) {
		fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';

		fullScreenApi.isFullScreen = function( doc ) {
			if( !doc ){
				doc = document;
			}
			if (typeof doc.fullscreenElement !== 'undefined') {
				return !!doc.fullscreenElement;
			} else if (typeof doc[this.prefix + 'FullscreenElement'] !== 'undefined') {
				return !!doc[this.prefix + 'FullscreenElement'];
			} else {
				switch (this.prefix) {
					// old style
					case '':
						return doc.fullScreen;
					case 'webkit':
						return doc.webkitIsFullScreen;
					default:
						return doc[this.prefix + 'FullScreen'];
				}
			}
		}
		fullScreenApi.requestFullScreen = function(el) {
			var func = el.requestFullscreen || el[this.prefix + 'RequestFullscreen'] || el[this.prefix + 'RequestFullScreen']
			return func.call(el);
		}
		fullScreenApi.cancelFullScreen = function(el) {
			var func = document.exitFullscreen || document[this.prefix + 'ExitFullscreen'] || document[this.prefix + 'CancelFullScreen'];
			return func.call(document);
		}
	}

	// jQuery plugin
	if (typeof jQuery != 'undefined') {
		jQuery.fn.requestFullScreen = function() {

			return this.each(function() {
				var el = jQuery(this);
				if (fullScreenApi.supportsFullScreen) {
					fullScreenApi.requestFullScreen(el);
				}
			});
		};
	}

	// export api
	window.fullScreenApi = fullScreenApi;

})();
