(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["ogvjs"] = factory();
	else
		root["ogvjs"] = factory();
})(this, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	//
	// -- ogv.js
	// https://github.com/brion/ogv.js
	// Copyright (c) 2013-2016 Brion Vibber
	//

	var OGVCompat = __webpack_require__(1),
		OGVLoader = __webpack_require__(3),
		OGVMediaType = __webpack_require__(7),
		OGVPlayer = __webpack_require__(8),
		OGVVersion = ("1.2.1-20160924234040-a1a879e");

	// Version 1.0's web-facing and test-facing interfaces
	if (window) {
		window.OGVCompat = OGVCompat;
		window.OGVLoader = OGVLoader;
		window.OGVMediaType = OGVMediaType;
		window.OGVPlayer = OGVPlayer;
		window.OGVVersion = OGVVersion;
	}

	module.exports = {
		OGVCompat: OGVCompat,
		OGVLoader: OGVLoader,
		OGVMediaType: OGVMediaType,
		OGVPlayer: OGVPlayer,
		OGVVersion: OGVVersion
	};


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var BogoSlow = __webpack_require__(2);

	var OGVCompat = {
		benchmark: new BogoSlow(),

		hasTypedArrays: function() {
			// emscripten-compiled code requires typed arrays
			return !!window.Uint32Array;
		},

		hasWebAudio: function() {
			return !!(window.AudioContext || window.webkitAudioContext);
		},

		hasFlash: function() {
			if (navigator.userAgent.indexOf('Trident') !== -1) {
				// We only do the ActiveX test because we only need Flash in
				// Internet Explorer 10/11. Other browsers use Web Audio directly
				// (Edge, Safari) or native playback, so there's no need to test
				// other ways of loading Flash.
				try {
					var obj = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
					return true;
				} catch(e) {
					return false;
				}
			}
			return false;
		},

		hasAudio: function() {
			return this.hasWebAudio() || this.hasFlash();
		},

		isBlacklisted: function(userAgent) {
			// JIT bugs in old Safari versions
			var blacklist = [
				/\(i.* OS [67]_.* like Mac OS X\).* Mobile\/.* Safari\//,
				/\(Macintosh.* Version\/6\..* Safari\/\d/
			];
			var blacklisted = false;
			blacklist.forEach(function(regex) {
				if (userAgent.match(regex)) {
					blacklisted = true;
				}
			});
			return blacklisted;
		},

		isSlow: function() {
			return this.benchmark.slow;
		},

		isTooSlow: function() {
			return this.benchmark.tooSlow;
		},

		supported: function(component) {
			if (component === 'OGVDecoder') {
				return (this.hasTypedArrays() && !this.isBlacklisted(navigator.userAgent));
			}
			if (component === 'OGVPlayer') {
				return (this.supported('OGVDecoder') && this.hasAudio() && !this.isTooSlow());
			}
			return false;
		}
	};

	module.exports = OGVCompat;


/***/ },
/* 2 */
/***/ function(module, exports) {

	/**
	 * A quick CPU/JS engine benchmark to guesstimate whether we're
	 * fast enough to handle 360p video in JavaScript.
	 */
	function BogoSlow() {
		var self = this;

		var timer;
		// FIXME: avoid to use window scope here
		if (window.performance && window.performance.now) {
			timer = function() {
				return window.performance.now();
			};
		} else {
			timer = function() {
				return Date.now();
			};
		}

		var savedSpeed = null;
		function run() {
			var ops = 0;
			var fibonacci = function(n) {
				ops++;
				if (n < 2) {
					return n;
				} else {
					return fibonacci(n - 2) + fibonacci(n - 1);
				}
			};

			var start = timer();

			fibonacci(30);

			var delta = timer() - start;
			savedSpeed = (ops / delta);
		}

		/**
		 * Return a scale value of operations/sec from the benchmark.
		 * If the benchmark has already been run, uses a memoized result.
		 *
		 * @property {number}
		 */
		Object.defineProperty(self, 'speed', {
			get: function() {
				if (savedSpeed === null) {
					run();
				}
				return savedSpeed;
			}
		});

		/**
		 * Return the defined cutoff speed value for 'slow' devices,
		 * based on results measured from some test devices.
		 *
		 * @property {number}
		 */
		Object.defineProperty(self, 'slowCutoff', {
			get: function() {
				// 2012 Retina MacBook Pro (Safari 7)  ~150,000
				// 2009 Dell T5500         (IE 11)     ~100,000
				// iPad Air                (iOS 7)      ~65,000
				// 2010 MBP / OS X 10.9    (Safari 7)   ~62,500
				// 2010 MBP / Win7 VM      (IE 11)      ~50,000+-
				//   ^ these play 360p ok
				// ----------- line of moderate doom ----------
				return 50000;
				//   v these play 160p ok
				// iPad Mini non-Retina    (iOS 8 beta) ~25,000
				// Dell Inspiron Duo       (IE 11)      ~25,000
				// Surface RT              (IE 11)      ~18,000
				// iPod Touch 5th-gen      (iOS 8 beta) ~16,000
			}
		});

		/**
		 * Return the defined cutoff speed value for 'too slow' devices,
		 * based on results measured from some test devices.
		 *
		 * No longer used.
		 *
		 * @property {number}
		 * @deprecated
		 */
		Object.defineProperty(self, 'tooSlowCutoff', {
			get: function() {
				return 0;
			}
		});

		/**
		 * 'Slow' devices can play audio and should sorta play our
		 * extra-tiny Wikimedia 160p15 transcodes
		 *
		 * @property {boolean}
		 */
		Object.defineProperty(self, 'slow', {
			get: function() {
				return (self.speed < self.slowCutoff);
			}
		});

		/**
		 * 'Too slow' devices aren't reliable at all
		 *
		 * @property {boolean}
		 */
		Object.defineProperty(self, 'tooSlow', {
			get: function() {
				return (self.speed < self.tooSlowCutoff);
			}
		});
	}

	module.exports = BogoSlow;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var OGVVersion = ("1.2.1-20160924234040-a1a879e");

	(function() {
		var global = this;

		var scriptMap = {
			OGVDemuxerOgg: 'ogv-demuxer-ogg.js',
			OGVDemuxerWebM: 'ogv-demuxer-webm.js',
			OGVDecoderAudioOpus: 'ogv-decoder-audio-opus.js',
			OGVDecoderAudioVorbis: 'ogv-decoder-audio-vorbis.js',
			OGVDecoderVideoTheora: 'ogv-decoder-video-theora.js',
			OGVDecoderVideoVP8: 'ogv-decoder-video-vp8.js'
		};

	  // @fixme make this less awful
		var proxyTypes = {
			OGVDecoderAudioOpus: 'audio',
			OGVDecoderAudioVorbis: 'audio',
			OGVDecoderVideoTheora: 'video',
			OGVDecoderVideoVP8: 'video'
		};
		var proxyInfo = {
			audio: {
				proxy: __webpack_require__(4),
				worker: 'ogv-worker-audio.js',
			},
			video: {
				proxy: __webpack_require__(6),
				worker: 'ogv-worker-video.js'
			}
		}

		function urlForClass(className) {
			var scriptName = scriptMap[className];
			if (scriptName) {
				return urlForScript(scriptName);
			} else {
				throw new Error('asked for URL for unknown class ' + className);
			}
		};

		function urlForScript(scriptName) {
			if (scriptName) {
				var base = OGVLoader.base;
				if (base) {
					base += '/';
				}
				return base + scriptName + '?version=' + encodeURIComponent(OGVVersion);
			} else {
				throw new Error('asked for URL for unknown script ' + scriptName);
			}
		};

		var scriptStatus = {},
			scriptCallbacks = {};
		function loadWebScript(src, callback) {
			if (scriptStatus[src] == 'done') {
				callback();
			} else if (scriptStatus[src] == 'loading') {
				scriptCallbacks[src].push(callback);
			} else {
				scriptStatus[src] = 'loading';
				scriptCallbacks[src] = [callback];

				var scriptNode = document.createElement('script');
				function done(event) {
					var callbacks = scriptCallbacks[src];
					delete scriptCallbacks[src];
					scriptStatus[src] = 'done';

					callbacks.forEach(function(cb) {
						cb();
					});
				}
				scriptNode.addEventListener('load', done);
				scriptNode.addEventListener('error', done);
				scriptNode.src = src;
				document.querySelector('head').appendChild(scriptNode);
			}
		}

		function defaultBase() {
			if (typeof global.window === 'object') {

				// for browser, try to autodetect
				var scriptNodes = document.querySelectorAll('script'),
					regex = /^(?:(.*)\/)ogv(?:-support)?\.js(?:\?|#|$)/,
					path,
					matches;
				for (var i = 0; i < scriptNodes.length; i++) {
					path = scriptNodes[i].getAttribute('src');
					if (path) {
						matches = path.match(regex);
						if (matches) {
							return matches[1];
						}
					}
				}

			} else {

				// for workers, assume current directory
				// if not a worker, too bad.
				return '';

			}
		}

		var OGVLoader = {
			base: defaultBase(),

			loadClass: function(className, callback, options) {
				options = options || {};
				if (options.worker) {
					this.workerProxy(className, callback);
				} else if (typeof global[className] === 'function') {
					// already loaded!
					callback(global[className]);
				} else if (typeof global.window === 'object') {
					loadWebScript(urlForClass(className), function() {
						callback(global[className]);
					});
				} else if (typeof global.importScripts === 'function') {
					// worker has convenient sync importScripts
					global.importScripts(urlForClass(className));
					callback(global[className]);
				}
			},

			workerProxy: function(className, callback) {
				var proxyType = proxyTypes[className],
					info = proxyInfo[proxyType];

				if (!info) {
					throw new Error('Requested worker for class with no proxy: ' + className);
				}

				var proxyClass = info.proxy,
					workerScript = info.worker,
					codecUrl = urlForScript(scriptMap[className]),
					workerUrl = urlForScript(workerScript),
					worker;

				var construct = function(options) {
					return new proxyClass(worker, className, options);
				};

				if (workerUrl.match(/^https?:|\/\//i)) {
					// Can't load workers natively cross-domain, but if CORS
					// is set up we can fetch the worker stub and the desired
					// class and load them from a blob.
					var getCodec,
						getWorker,
						codecResponse,
						workerResponse,
						codecLoaded = false,
						workerLoaded = false,
						blob;

					function completionCheck() {
						if ((codecLoaded == true) && (workerLoaded == true)) {
							try {
								blob = new Blob([codecResponse + " " + workerResponse], {type: 'application/javascript'});
							} catch (e) { // Backwards-compatibility
								window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;
								blob = new BlobBuilder();
								blob.append(codecResponse + " " + workerResponse);
								blob = blob.getBlob();
							}
							// Create the web worker
							worker = new Worker(URL.createObjectURL(blob));
							callback(construct);
						}
					}

					// Load the codec
					getCodec = new XMLHttpRequest();
					getCodec.open("GET", codecUrl, true);
					getCodec.onreadystatechange = function() {
						if(getCodec.readyState == 4 && getCodec.status == 200) {
							codecResponse = getCodec.responseText;
							// Update the codec response loaded flag
							codecLoaded = true;
							completionCheck();
						}
					};
					getCodec.send();

					// Load the worker
					getWorker = new XMLHttpRequest();
					getWorker.open("GET", workerUrl, true);
					getWorker.onreadystatechange = function() {
						if(getWorker.readyState == 4 && getWorker.status == 200) {
							workerResponse = getWorker.responseText;
							// Update the worker response loaded flag
							workerLoaded = true;
							completionCheck();
						}
					};
					getWorker.send();
				} else {
					// Local URL; load it directly for simplicity.
					worker = new Worker(workerUrl);
					callback(construct);
				}
			}
		};

		module.exports = OGVLoader;

	})();


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var OGVProxyClass = __webpack_require__(5);

	var OGVDecoderAudioProxy = OGVProxyClass({
		loadedMetadata: false,
		audioFormat: null,
		audioBuffer: null,
		cpuTime: 0
	}, {
		init: function(callback) {
			this.proxy('init', [], callback);
		},

		processHeader: function(data, callback) {
			this.proxy('processHeader', [data], callback, [data]);
		},

		processAudio: function(data, callback) {
			this.proxy('processAudio', [data], callback, [data]);
		},

		close: function() {
			this.terminate();
		}
	});

	module.exports = OGVDecoderAudioProxy;


/***/ },
/* 5 */
/***/ function(module, exports) {

	/**
	 * Proxy object for web worker interface for codec classes.
	 *
	 * Used by the high-level player interface.
	 *
	 * @author Brion Vibber <brion@pobox.com>
	 * @copyright 2015
	 * @license MIT-style
	 */
	function OGVProxyClass(initialProps, methods) {
		return function(worker, className, options) {
			options = options || {};
			var self = this;

			var transferables = (function() {
				var buffer = new ArrayBuffer(1024),
					bytes = new Uint8Array(buffer);
				try {
					worker.postMessage({
						action: 'transferTest',
						bytes: bytes
					}, [buffer]);
					if (buffer.byteLength) {
						// No transferable support
						return false;
					} else {
						return true;
					}
				} catch (e) {
					return false;
				}
			})();

			// Set up proxied property getters
			var props = {};
			for (var iPropName in initialProps) {
				if (initialProps.hasOwnProperty(iPropName)) {
					(function(propName) {
						props[propName] = initialProps[propName];
						Object.defineProperty(self, propName, {
							get: function getProperty() {
								return props[propName];
							}
						});
					})(iPropName);
				}
			}

			// Current player wants to avoid async confusion.
			var processingQueue = 0;
			Object.defineProperty(self, 'processing', {
				get: function() {
					return (processingQueue > 0);
				}
			});

			// Set up proxied methods
			for (var method in methods) {
				if (methods.hasOwnProperty(method)) {
					self[method] = methods[method];
				}
			}

			// And some infrastructure!
			var messageCount = 0,
				pendingCallbacks = {};
			this.proxy = function(action, args, callback, transfers) {
				if (!worker) {
					throw 'Tried to call "' + action + '" method on closed proxy object';
				}
				var callbackId = 'callback-' + (++messageCount) + '-' + action;
				if (callback) {
					pendingCallbacks[callbackId] = callback;
				}
				var out = {
					'action': action,
					'callbackId': callbackId,
					'args': args || []
				};
				processingQueue++;
				if (transferables) {
					worker.postMessage(out, transfers || []);
				} else {
					worker.postMessage(out);
				}
			};
			this.terminate = function() {
				if (worker) {
					worker.terminate();
					worker = null;
					processingQueue = 0;
					pendingCallbacks = {};
				}
			};

			worker.addEventListener('message', function proxyOnMessage(event) {
				processingQueue--;
				if (event.data.action !== 'callback') {
					// ignore
					return;
				}

				var data = event.data,
					callbackId = data.callbackId,
					args = data.args,
					callback = pendingCallbacks[callbackId];

				// Save any updated properties returned to us...
				if (data.props) {
					for (var propName in data.props) {
						if (data.props.hasOwnProperty(propName)) {
							props[propName] = data.props[propName];
						}
					}
				}

				if (callback) {
					delete pendingCallbacks[callbackId];
					callback.apply(this, args);
				}
			});

			// Tell the proxy to load and initialize the appropriate class
			self.proxy('construct', [className, options], function() {});

			return self;
		};
	}

	module.exports = OGVProxyClass;

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var OGVProxyClass = __webpack_require__(5);

	var OGVDecoderVideoProxy = OGVProxyClass({
		loadedMetadata: false,
		videoFormat: null,
		frameBuffer: null,
		cpuTime: 0
	}, {
		init: function(callback) {
			this.proxy('init', [], callback);
		},

		processHeader: function(data, callback) {
			this.proxy('processHeader', [data], callback, [data]);
		},

		processFrame: function(data, callback) {
			this.proxy('processFrame', [data], callback, [data]);
		},

		close: function() {
			this.terminate();
		}
	});

	module.exports = OGVDecoderVideoProxy;

/***/ },
/* 7 */
/***/ function(module, exports) {

	function OGVMediaType(contentType) {
		contentType = '' + contentType;

		var self = this;
		self.major = null;
		self.minor = null;
		self.codecs = null;

		function trim(str) {
			return str.replace(/^\s+/, '').replace(/\s+$/, '');
		}

		function split(str, sep, limit) {
			var bits = str.split(sep, limit).map(function(substr) {
				return trim(substr);
			});
			if (typeof limit === 'number') {
				while (bits.length < limit) {
					bits.push(null);
				}
			}
			return bits;
		}

		var parts = split(contentType, ';');
		if (parts.length) {
			var base = parts.shift();
			if (base) {
				var bits = split(base, '/', 2);
				self.major = bits[0];
				self.minor = bits[1];
			}

			parts.forEach(function(str) {
				var matches = str.match(/^codecs\s*=\s*"(.*?)"$/);
				if (matches) {
					self.codecs = split(matches[1], ',');
				}
			});
		}
	}

	module.exports = OGVMediaType;


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var YUVCanvas = __webpack_require__(9);

	// -- OGVLoader.js
	var OGVLoader = __webpack_require__(3);

	// -- StreamFile.js
	var StreamFile = __webpack_require__(16);

	// -- AudioFeeder.js
	var AudioFeeder = __webpack_require__(17),
		dynamicaudio_swf = __webpack_require__(18);

	// -- Bisector.js
	var Bisector = __webpack_require__(19);

	// -- OGVMediaType.js
	var OGVMediaType = __webpack_require__(7);

	// -- OGVWrapperCodec.js
	var OGVWrapperCodec = __webpack_require__(20);

	// -- OGVDecoderAudioProxy.js
	var OGVDecoderAudioProxy = __webpack_require__(4);

	// -- OGVDecoderVideoProxy.js
	var OGVDecoderVideoProxy = __webpack_require__(6);

	/**
	 * Constructor for an analogue of the TimeRanges class
	 * returned by various HTMLMediaElement properties
	 *
	 * Pass an array of two-element arrays, each containing a start and end time.
	 */
	// FIXME: not use window scope if possible here
	var OGVTimeRanges = window.OGVTimeRanges = function(ranges) {
		Object.defineProperty(this, 'length', {
			get: function getLength() {
				return ranges.length;
			}
		});
		this.start = function(i) {
			return ranges[i][0];
		};
		this.end = function(i) {
			return ranges[i][1];
		};
		return this;
	};

	/**
	 * Player class -- instantiate one of these to get an 'ogvjs' HTML element
	 * which has a similar interface to the HTML audio/video elements.
	 *
	 * @param options: optional dictionary of options:
	 *                 'base': string; base URL for additional resources, such as Flash audio shim
	 *                 'webGL': bool; pass true to use WebGL acceleration if available
	 *                 'forceWebGL': bool; pass true to require WebGL even if not detected
	 */
	var OGVPlayer = function(options) {
		options = options || {};

		var instanceId = 'ogvjs' + (++OGVPlayer.instanceCount);

		var codecClass = null,
			codecType = null;

		var canvasOptions = {};
		if (options.webGL !== undefined) {
			// @fixme confirm format of webGL option
			canvasOptions.webGL = options.webGL;
		}
		if(!!options.forceWebGL) {
			canvasOptions.webGL = 'required';
		}

		// Experimental options
		var enableWebM = !!options.enableWebM;

		// Running the codec in a worker thread equals happy times!
		var enableWorker = !!window.Worker;
		if (typeof options.worker !== 'undefined') {
			enableWorker = !!options.worker;
		}

		var State = {
			INITIAL: 'INITIAL',
			SEEKING_END: 'SEEKING_END',
			LOADED: 'LOADED',
			PRELOAD: 'PRELOAD',
			READY: 'READY',
			PLAYING: 'PLAYING',
			SEEKING: 'SEEKING',
			ENDED: 'ENDED',
			ERROR: 'ERROR'
		}, state = State.INITIAL;

		var SeekState = {
			NOT_SEEKING: 'NOT_SEEKING',
			BISECT_TO_TARGET: 'BISECT_TO_TARGET',
			BISECT_TO_KEYPOINT: 'BISECT_TO_KEYPOINT',
			LINEAR_TO_TARGET: 'LINEAR_TO_TARGET'
		}, seekState = SeekState.NOT_SEEKING;

		var audioOptions = {},
			codecOptions = {};
		options.base = options.base || OGVLoader.base;
		if (typeof options.base === 'string') {
			// Pass the resource dir down to AudioFeeder, so it can load the dynamicaudio.swf
			audioOptions.base = options.base;

			// And to the worker thread, so it can load the codec JS
			codecOptions.base = options.base;
		}
		if (typeof options.audioContext !== 'undefined') {
			// Try passing a pre-created audioContext in?
			audioOptions.audioContext = options.audioContext;
		}
		// Buffer in largeish chunks to survive long CPU spikes on slow CPUs (eg, 32-bit iOS)
		audioOptions.bufferSize = 8192;

		codecOptions.worker = enableWorker;
		if (typeof options.memoryLimit === 'number') {
			codecOptions.memoryLimit = options.memoryLimit;
		}

		var canvas = document.createElement('canvas');
		var frameSink;

		// Return a magical custom element!
		var self = document.createElement('ogvjs');
		self.className = instanceId;

		canvas.style.position = 'absolute';
		canvas.style.top = '0';
		canvas.style.left = '0';
		canvas.style.width = '100%';
		canvas.style.height = '100%';
		canvas.style.objectFit = 'contain';
		self.appendChild(canvas);

		var getTimestamp;
		// FIXME: don't use window scope, see BogoSlow.js
		if (window.performance === undefined || window.performance.now === undefined) {
			getTimestamp = Date.now;
		} else {
			getTimestamp = window.performance.now.bind(window.performance);
		}
		function time(cb) {
			var start = getTimestamp();
			cb();
			var delta = getTimestamp() - start;
			lastFrameDecodeTime += delta;
			return delta;
		}

		var then = getTimestamp();
		function log(msg) {
			if (options.debug) {
				var now = getTimestamp(),
					delta = now - then;

				//console.log('+' + delta + 'ms ' + msg);
				//then = now;
				
				if (!options.debugFilter || msg.match(options.debugFilter)) {
					console.log('[' + (Math.round(delta * 10) / 10) + 'ms] ' + msg);
				}
			}
		}

		function fireEvent(eventName, props) {
			log('fireEvent ' + eventName);
			var event;
			props = props || {};

			var standard = (typeof window.Event == 'function');
			if (standard) {
				// standard event creation
				event = new CustomEvent(eventName);
			} else {
				// IE back-compat mode
				// https://msdn.microsoft.com/en-us/library/dn905219%28v=vs.85%29.aspx
				event = document.createEvent('Event');
				event.initEvent(eventName, false, false);
			}

			for (var prop in props) {
				if (props.hasOwnProperty(prop)) {
					event[prop] = props[prop];
				}
			}

			var allowDefault = self.dispatchEvent(event);
			if (!standard && eventName === 'resize' && self.onresize && allowDefault) {
				// resize demands special treatment!
				// in IE 11 it doesn't fire through to the .onresize handler
				// for some crazy reason
				self.onresize.call(self, event);
			}
		}
		function fireEventAsync(eventName, props) {
			log('fireEventAsync ' + eventName);
			setTimeout(function() {
				fireEvent(eventName, props);
			}, 0);
		}

		var codec = null,
			videoInfo = null,
			audioInfo = null,
			actionQueue = [],
			audioFeeder = null;
		var muted = false,
			initialPlaybackPosition = 0.0,
			initialPlaybackOffset = 0.0,
			prebufferingAudio = false,
			stoppedForLateFrame = false;
		function initAudioFeeder() {
			audioFeeder = new AudioFeeder( audioOptions );
			audioFeeder.init(audioInfo.channels, audioInfo.rate);

			// At our requested 8192 buffer size, bufferDuration should be
			// about 185ms at 44.1 kHz or 170ms at 48 kHz output, covering
			// 4-6 frames at 24-30fps.
			//
			// 2 or 3 buffers' worth will be in flight at any given time;
			// be warned that durationBuffered includes that in-flight stuff.
			//
			// Set our bufferThreshold to a full 1s for plenty of extra headroom,
			// and make sure we've queued up twice that when we're operating.
			audioFeeder.bufferThreshold = 1;

			audioFeeder.volume = self.volume;
			audioFeeder.muted = self.muted;

			// If we're in a background tab, timers may be throttled.
			// audioFeeder will call us when buffers need refilling,
			// without any throttling.
			audioFeeder.onbufferlow = function audioCallback() {
				log('onbufferlow');
				if ((stream && stream.waiting) || pendingAudio) {
					// We're waiting on input or other async processing;
					// we'll get triggered later.
				} else {
					// We're in an async event so it's safe to run the loop:
					pingProcessing();
				}
			};

			// If we ran out of audio *completely* schedule some more processing.
			// This shouldn't happen if we keep up with onbufferlow, except at
			// the very beginning of playback when we haven't buffered any data yet.
			// @todo pre-buffer a little data to avoid needing this
			audioFeeder.onstarved = function () {
				log('onstarved');
				if (isProcessing()) {
					// We're waiting on input or other async processing;
					// we'll get triggered later.
				} else {
					// Schedule loop after this synchronous event.
					pingProcessing(0);
				}
			};
		}

		function startPlayback(offset) {
			if (audioFeeder) {
				audioFeeder.start();
				var state = audioFeeder.getPlaybackState();
				initialPlaybackPosition = state.playbackPosition;
			} else {
				initialPlaybackPosition = getTimestamp() / 1000;
			}
			if (offset !== undefined) {
				initialPlaybackOffset = offset;
			}
			// Clear the late flag if it was set.
			prebufferingAudio = false;
			stoppedForLateFrame = false;
			log('continuing at ' + initialPlaybackPosition + ', ' + initialPlaybackOffset);
		}

		function stopPlayback() {
			initialPlaybackOffset = getPlaybackTime();
			log('pausing at ' + initialPlaybackOffset);
			if (audioFeeder) {
				audioFeeder.stop();
			}
		}

		/**
		 * Get audio playback time position in file's units
		 *
		 * @return {number} seconds since file start
		 */
		function getPlaybackTime(state) {
			if (prebufferingAudio || stoppedForLateFrame || paused) {
				return initialPlaybackOffset;
			} else {
				var position;
				if (audioFeeder) {
					state = state || audioFeeder.getPlaybackState();
					position = state.playbackPosition;
				} else {
					// @fixme handle paused/stoped time better
					position = getTimestamp() / 1000;
				}
				return (position - initialPlaybackPosition) + initialPlaybackOffset;
			}
		}

		var currentSrc = '',
			stream,
			streamEnded = false,
			dataEnded = false,
			byteLength = 0,
			duration = null,
			lastSeenTimestamp = null,
			nextProcessingTimer,
			nextFrameTimer = null,
			loading = false,
			started = false,
			paused = true,
			ended = false,
			startedPlaybackInDocument = false;

		var framesPlayed = 0;
		// Benchmark data, exposed via getPlaybackStats()
		var framesProcessed = 0, // frames
			targetPerFrameTime = 1000 / 60, // ms
			actualPerFrameTime = 0, // ms
			totalFrameTime = 0, // ms
			totalFrameCount = 0, // frames
			playTime = 0, // ms
			bufferTime = 0, // ms
			drawingTime = 0, // ms
			proxyTime = 0, // ms
			totalJitter = 0; // sum of ms we're off from expected frame delivery time
		// Benchmark data that doesn't clear
		var droppedAudio = 0, // number of times we were starved for audio
			delayedAudio = 0, // seconds audio processing was delayed by blocked CPU
			lateFrames = 0;   // number of times a frame was late and we had to halt audio
		var poster = '', thumbnail;

		// called when stopping old video on load()
		function stopVideo() {
			log("STOPPING");
			// kill the previous video if any
			state = State.INITIAL;
			seekState = SeekState.NOT_SEEKING;
			started = false;
			//paused = true; // don't change this?
			ended = false;
			frameEndTimestamp = 0.0;
			audioEndTimestamp = 0.0;
			lastFrameDecodeTime = 0.0;
			stoppedForLateFrame = false;
			prebufferingAudio = false;

			// Abort all queued actions
			actionQueue.splice(0, actionQueue.length);

			if (stream) {
				// @todo fire an abort event if still loading
				// @todo fire an emptied event if previously had data
				stream.abort();
				stream = null;
				streamEnded = false;
			}
			if (codec) {
				codec.close();
				codec = null;
				pendingFrame = 0;
				pendingAudio = 0;
				dataEnded = false;
			}
			videoInfo = null;
			audioInfo = null;
			if (audioFeeder) {
				audioFeeder.close();
				audioFeeder = null;
			}
			if (nextProcessingTimer) {
				clearTimeout(nextProcessingTimer);
				nextProcessingTimer = null;
			}
			if (nextFrameTimer) {
				clearTimeout(nextFrameTimer);
				nextFrameTimer = null;
			}
			if (frameSink) {
				frameSink.clear();
				frameSink = null;
			}
			if (decodedFrames) {
				decodedFrames.splice(0, decodedFrames.length);
			}
			// @todo set playback position, may need to fire timeupdate if wasnt previously 0
			initialPlaybackPosition = 0;
			initialPlaybackOffset = 0;
			duration = null; // do not fire durationchange
			// timeline offset to 0?
		}

		var lastFrameTime = getTimestamp(),
			frameEndTimestamp = 0.0,
			audioEndTimestamp = 0.0,
			decodedFrames = [];
		var lastFrameDecodeTime = 0.0,
			lastFrameVideoCpuTime = 0,
			lastFrameAudioCpuTime = 0,
			lastFrameDemuxerCpuTime = 0,
			lastFrameDrawingTime = 0,
			lastFrameBufferTime = 0,
			lastFrameProxyTime = 0,
			lastVideoCpuTime = 0,
			lastAudioCpuTime = 0,
			lastDemuxerCpuTime = 0,
			lastBufferTime = 0,
			lastProxyTime = 0,
			lastDrawingTime = 0;
		var lastFrameTimestamp = 0.0,
			currentVideoCpuTime = 0.0;

		var lastTimeUpdate = 0, // ms
			timeUpdateInterval = 250; // ms

		function doFrameComplete() {
			if (startedPlaybackInDocument && !document.body.contains(self)) {
				// We've been de-parented since we last ran
				// Stop playback at next opportunity!
				setTimeout(function() {
					self.stop();
				}, 0);
			}

			var newFrameTimestamp = getTimestamp(),
				wallClockTime = newFrameTimestamp - lastFrameTimestamp,
				//jitter = wallClockTime - targetPerFrameTime;
				jitter = actualPerFrameTime - targetPerFrameTime;
			totalJitter += Math.abs(jitter);
			playTime += wallClockTime;

			var timing = {
				cpuTime: lastFrameDecodeTime,
				drawingTime: drawingTime - lastFrameDrawingTime,
				bufferTime: bufferTime - lastFrameBufferTime,
				proxyTime: proxyTime - lastFrameProxyTime,
				
				demuxerTime: 0,
				videoTime: 0,
				audioTime: 0,
				//clockTime: wallClockTime
				clockTime: actualPerFrameTime,

				late: stoppedForLateFrame
			};
			if (codec) {
				timing.demuxerTime = (codec.demuxerCpuTime - lastFrameDemuxerCpuTime);
				timing.videoTime += (currentVideoCpuTime - lastFrameVideoCpuTime);
				timing.audioTime += (codec.audioCpuTime - lastFrameAudioCpuTime);
			}
			timing.cpuTime += timing.demuxerTime;
			lastFrameDecodeTime = 0;
			lastFrameTimestamp = newFrameTimestamp;
			if (codec) {
				lastFrameVideoCpuTime = currentVideoCpuTime;
				lastFrameAudioCpuTime = codec.audioCpuTime;
				lastFrameDemuxerCpuTime = codec.demuxerCpuTime;
			} else {
				lastFrameVideoCpuTime = 0;
				lastFrameAudioCpuTime = 0;
				lastFrameDemuxerCpuTime = 0;
			}
			lastFrameDrawingTime = drawingTime;
			lastFrameBufferTime = bufferTime;
			lastFrameProxyTime = proxyTime;

			function n(x) {
				return Math.round(x * 10) / 10;
			}
			log('drew frame ' + frameEndTimestamp + ': ' +
				'clock time ' + n(wallClockTime) + ' (jitter ' + n(jitter) + ') ' +
				'cpu: ' + n(timing.cpuTime) + ' (mux: ' + n(timing.demuxerTime) + ' buf: ' + n(timing.bufferTime) + ' draw: ' + n(timing.drawingTime) + ' proxy: ' + n(timing.proxyTime) + ') ' +
				'vid: ' + n(timing.videoTime) + ' aud: ' + n(timing.audioTime));
			fireEventAsync('framecallback', timing);

			if (!lastTimeUpdate || (newFrameTimestamp - lastTimeUpdate) >= timeUpdateInterval) {
				lastTimeUpdate = newFrameTimestamp;
				fireEventAsync('timeupdate');
			}
		}


		// -- seek functions
		var seekTargetTime = 0.0,
			seekTargetKeypoint = 0.0,
			bisectTargetTime = 0.0,
			lastSeekPosition,
			lastFrameSkipped,
			seekBisector;

		function startBisection(targetTime) {
			bisectTargetTime = targetTime;
			seekBisector = new Bisector({
				start: 0,
				end: stream.bytesTotal - 1,
				process: function(start, end, position) {
					if (position == lastSeekPosition) {
						return false;
					} else {
						lastSeekPosition = position;
						lastFrameSkipped = false;
						codec.flush(function() {
							stream.seek(position);
							readBytesAndWait();
						});
						return true;
					}
				}
			});
			seekBisector.start();
		}

		function seek(toTime) {
			log('requested seek to ' + toTime);
			if (stream.bytesTotal === 0) {
				throw new Error('Cannot bisect a non-seekable stream');
			}
			function prepForSeek(callback) {
				if (stream) { //} && stream.waiting) {
					stream.abort();
				}
				// clear any queued input/seek-start
				actionQueue.splice(0, actionQueue.length);
				stopPlayback();
				prebufferingAudio = false;
				stoppedForLateFrame = false;
				if (audioFeeder) {
					audioFeeder.flush();
				}
				state = State.SEEKING;
				seekTargetTime = toTime;
				if (codec) {
					codec.flush(callback);
				} else {
					callback();
				}
			}

			// Abort any previous seek or play suitably
			prepForSeek(function() {
				if (isProcessing()) {
					// already waiting on input
				} else {
					// start up the new load *after* event loop churn
					pingProcessing(0);
				}
			});

			actionQueue.push(function() {
				// Just in case another async task stopped us...
				prepForSeek(function() {
					streamEnded = false;
					dataEnded = false;
					ended = false;
					state = State.SEEKING;
					seekTargetTime = toTime;
					seekTargetKeypoint = -1;
					lastFrameSkipped = false;
					lastSeekPosition = -1;

					decodedFrames.splice(0, decodedFrames.length);
					pendingFrame = 0;
					pendingAudio = 0;

					codec.seekToKeypoint(toTime, function(seeking) {
						if (seeking) {
							seekState = SeekState.LINEAR_TO_TARGET;
							fireEventAsync('seeking');
							if (isProcessing()) {
								// wait for i/o
							} else {
								pingProcessing();
							}
							return;
						}
						// Use the old interface still implemented on ogg demuxer
						codec.getKeypointOffset(toTime, function(offset) {
							if (offset > 0) {
								// This file has an index!
								//
								// Start at the keypoint, then decode forward to the desired time.
								//
								seekState = SeekState.LINEAR_TO_TARGET;
								stream.seek(offset);
								readBytesAndWait();
							} else {
								// No index.
								//
								// Bisect through the file finding our target time, then we'll
								// have to do it again to reach the keypoint, and *then* we'll
								// have to decode forward back to the desired time.
								//
								seekState = SeekState.BISECT_TO_TARGET;
								startBisection(seekTargetTime);
							}

							fireEvent('seeking');
						});
					});
				});
			});
		}

		function continueSeekedPlayback() {
			seekState = SeekState.NOT_SEEKING;
			state = State.READY;
			frameEndTimestamp = codec.frameTimestamp;
			audioEndTimestamp = codec.audioTimestamp;
			if (codec.hasAudio) {
				seekTargetTime = codec.audioTimestamp;
			} else {
				seekTargetTime = codec.frameTimestamp;
			}
			initialPlaybackOffset = seekTargetTime;

			function finishedSeeking() {
				lastTimeUpdate = seekTargetTime;
				fireEventAsync('timeupdate');
				fireEventAsync('seeked');
				if (isProcessing()) {
					// wait for whatever's going on to complete
				} else {
					pingProcessing();
				}
			}

			if (paused) {
				//stopPlayback(); // :P

				// Decode and show first frame immediately
				if (codec.hasVideo && codec.frameReady) {
					// hack! move this into the main loop when retooling
					// to avoid maintaining this double draw
					codec.decodeFrame(function(ok) {
						if (ok) {
							if (thumbnail) {
								self.removeChild(thumbnail);
								thumbnail = null;
							}
							frameSink.drawFrame(codec.frameBuffer);
						}
						finishedSeeking();
					} );
					return;
				}
			}
			finishedSeeking();
		}

		/**
		 * @return {boolean} true to continue processing, false to wait for input data
		 */
		function doProcessLinearSeeking() {
			var frameDuration;
			if (codec.hasVideo) {
				frameDuration = targetPerFrameTime / 1000;
			} else {
				frameDuration = 1 / 256; // approximate packet audio size, fake!
			}

			if (codec.hasVideo) {
				if (pendingFrame) {
					// wait
				} else if (!codec.frameReady) {
					// Haven't found a frame yet, process more data
					pingProcessing();
					return;
				} else if (codec.frameTimestamp + frameDuration < seekTargetTime) {
					// Haven't found a time yet, or haven't reached the target time.
					// Decode it in case we're at our keyframe or a following intraframe...
					codec.decodeFrame(function() {
						pingProcessing();
					});
					return;
				} else {
					// Reached or surpassed the target time.
					if (codec.hasAudio) {
						// Keep processing the audio track
						// fall through...
					} else {
						continueSeekedPlayback();
						return;
					}
				}
			}
			if (codec.hasAudio) {
				if (pendingAudio) {
					// wait
					return;
				} if (!codec.audioReady) {
					// Haven't found an audio packet yet, process more data
					pingProcessing();
					return;
				} else if (codec.audioTimestamp + frameDuration < seekTargetTime) {
					// Haven't found a time yet, or haven't reached the target time.
					// Decode it so when we reach the target we've got consistent data.
					codec.decodeAudio(function() {
						pingProcessing();
					});
					return;
				} else {
					continueSeekedPlayback();
					return;
				}
			}
		}

		function doProcessBisectionSeek() {
			var frameDuration,
				timestamp;
			if (codec.hasVideo) {
				if (!codec.frameReady) {
					// Haven't found a frame yet, process more data
					pingProcessing();
					return;
				}
				timestamp = codec.frameTimestamp;
				frameDuration = targetPerFrameTime / 1000;
			} else if (codec.hasAudio) {
				if (!codec.audioReady) {
					// Haven't found an audio packet yet, process more data
					pingProcessing();
					return;
				}
				timestamp = codec.audioTimestamp;
				frameDuration = 1 / 256; // approximate packet audio size, fake!
			} else {
				throw new Error('Invalid seek state; no audio or video track available');
			}

			if (timestamp < 0) {
				// Haven't found a time yet.
				// Decode in case we're at our keyframe or a following intraframe...
				if (codec.frameReady) {
					codec.decodeFrame(function() {
						pingProcessing();
					});
				} else if (codec.audioReady) {
					codec.decodeAudio(function() {
						pingProcessing();
					});
				} else {
					pingProcessing();
				}
			} else if (timestamp - frameDuration / 2 > bisectTargetTime) {
				if (seekBisector.left()) {
					// wait for new data to come in
				} else {
					log('close enough (left)');
					seekTargetTime = codec.frameTimestamp;
					continueSeekedPlayback();
				}
			} else if (timestamp + frameDuration / 2 < bisectTargetTime) {
				if (seekBisector.right()) {
					// wait for new data to come in
				} else {
					log('close enough (right)');
					seekTargetTime = codec.frameTimestamp;
					continueSeekedPlayback();
				}
			} else {
				// Reached the bisection target!
				if (seekState == SeekState.BISECT_TO_TARGET && (codec.hasVideo && codec.keyframeTimestamp < codec.frameTimestamp)) {
					// We have to go back and find a keyframe. Sigh.
					log('finding the keypoint now');
					seekState = SeekState.BISECT_TO_KEYPOINT;
					startBisection(codec.keyframeTimestamp);
				} else {
					log('straight seeking now');
					// Switch to linear mode to find the final target.
					seekState = SeekState.LINEAR_TO_TARGET;
					pingProcessing();
				}
			}
		}

		function setupVideo() {
			if (videoInfo.fps > 0) {
				targetPerFrameTime = 1000 / videoInfo.fps;
			} else {
				targetPerFrameTime = 16.667; // recalc this later
			}

			canvas.width = videoInfo.displayWidth;
			canvas.height = videoInfo.displayHeight;
			OGVPlayer.styleManager.appendRule('.' + instanceId, {
				width: videoInfo.displayWidth + 'px',
				height: videoInfo.displayHeight + 'px'
			});
			OGVPlayer.updatePositionOnResize();

			frameSink = YUVCanvas.attach(canvas);
		}

		var depth = 0,
			needProcessing = false,
			pendingFrame = 0,
			pendingAudio = 0,
			framePipelineDepth = 3,
			audioPipelineDepth = 3;

		function doProcessing() {
			nextProcessingTimer = null;

			if (isProcessing()) {
				// Called async while waiting for something else to complete...
				// let it finish, then we'll get called again to continue.
				//return;
				//throw new Error('REENTRANCY FAIL: doProcessing during processing');
			}

			if (depth > 0) {
				throw new Error('REENTRANCY FAIL: doProcessing recursing unexpectedly');
			}
			var iters = 0;
			do {
				needProcessing = false;
				depth++;
				doProcessingLoop();
				depth--;
				
				if (needProcessing && isProcessing()) {
					throw new Error('REENTRANCY FAIL: waiting on input or codec but asked to keep processing');
				}
				if (++iters > 500) {
					log('stuck in processing loop; breaking with timer');
					needProcessing = 0;
					pingProcessing(0);
				}
			} while (needProcessing);
		}

		function doProcessingLoop() {

			if (actionQueue.length) {
				// data or user i/o to process in our serialized event stream
				// The function should eventually bring us back here via pingProcessing(),
				// directly or via further i/o.

				var action = actionQueue.shift();
				action();

			} else if (state == State.INITIAL) {

				if (codec.loadedMetadata) {
					// we just fell over from headers into content; call onloadedmetadata etc
					if (!codec.hasVideo && !codec.hasAudio) {
						throw new Error('No audio or video found, something is wrong');
					}
					if (codec.hasAudio) {
						audioInfo = codec.audioFormat;
					}
					if (codec.hasVideo) {
						videoInfo = codec.videoFormat;
						setupVideo();
					}
					if (!isNaN(codec.duration)) {
						duration = codec.duration;
					}
					if (duration === null) {
						if (stream.seekable) {
							state = State.SEEKING_END;
							lastSeenTimestamp = -1;
							codec.flush(function() {
								stream.seek(Math.max(0, stream.bytesTotal - 65536 * 2));
								readBytesAndWait();
							});
						} else {
							// Stream not seekable and no x-content-duration; assuming infinite stream.
							state = State.LOADED;
							pingProcessing();
						}
					} else {
						// We already know the duration.
						state = State.LOADED;
						pingProcessing();
					}
				} else {
					codec.process(function processInitial(more) {
						if (more) {
							// Keep processing headers
							pingProcessing();
						} else {
							// Read more data!
							log('reading more cause we are out of data');
							readBytesAndWait();
						}
					});
				}

			} else if (state == State.SEEKING_END) {

				// Look for the last item.
				if (codec.frameReady) {
					log('saw frame with ' + codec.frameTimestamp);
					lastSeenTimestamp = Math.max(lastSeenTimestamp, codec.frameTimestamp);
					codec.discardFrame(function() {
						pingProcessing();
					});
				} else if (codec.audioReady) {
					log('saw audio with ' + codec.audioTimestamp);
					lastSeenTimestamp = Math.max(lastSeenTimestamp, codec.audioTimestamp);
					codec.discardAudio(function() {
						pingProcessing();
					});
				} else {
					codec.process(function processSeekingEnd(more) {
						if (more) {
							// Keep processing headers
							pingProcessing();
						} else {
							// Read more data!
							if (stream.bytesRead < stream.bytesTotal) {
								readBytesAndWait();
							} else {
								// We are at the end!
								log('seek-duration: we are at the end: ' + lastSeenTimestamp);
								if (lastSeenTimestamp > 0) {
									duration = lastSeenTimestamp;
								}

								// Ok, seek back to the beginning and resync the streams.
								state = State.LOADED;
								codec.flush(function() {
									stream.seek(0);
									streamEnded = false;
									dataEnded = false;
									readBytesAndWait();
								});
							}
						}
					});
				}

			} else if (state == State.LOADED) {

				state = State.PRELOAD;
				fireEventAsync('loadedmetadata');
				fireEventAsync('durationchange');
				if (codec.hasVideo) {
					fireEventAsync('resize');
				}
				pingProcessing(0);

			} else if (state == State.PRELOAD) {

				if ((codec.frameReady || !codec.hasVideo) &&
				    (codec.audioReady || !codec.hasAudio)) {

					state = State.READY;
					fireEventAsync('loadeddata');
					pingProcessing();
				} else {
					codec.process(function doProcessPreload(more) {
						if (more) {
							pingProcessing();
						} else if (streamEnded) {
							// Ran out of data before data available...?
							ended = true;
						} else {
							readBytesAndWait();
						}
					});
				}

			} else if (state == State.READY) {

				if (paused) {

					// Paused? stop here.
					log('paused while in ready');

				} else {

					function finishStartPlaying() {
						log('finishStartPlaying');

						state = State.PLAYING;
						lastFrameTimestamp = getTimestamp();

						if (codec.hasAudio && audioFeeder) {
							// Pre-queue audio before we start the clock
							prebufferingAudio = true;
						} else {
							startPlayback();
						}
						pingProcessing(0);
						fireEventAsync('play');
						fireEventAsync('playing');
					}

					if (codec.hasAudio && !audioFeeder && !muted) {
						initAudioFeeder();
						audioFeeder.waitUntilReady(finishStartPlaying);
					} else {
						finishStartPlaying();
					}
				}

			} else if (state == State.SEEKING) {

				if ((codec.hasVideo && codec.frameTimestamp < 0)
				 || (codec.hasAudio && codec.audioTimestamp < 0)
				) {
					codec.process(function processSeeking(more) {
						if (more) {
							pingProcessing();
						} else {
							readBytesAndWait();
						}
					});
				} else if (seekState == SeekState.NOT_SEEKING) {
					throw new Error('seeking in invalid state (not seeking?)');
				} else if (seekState == SeekState.BISECT_TO_TARGET) {
					doProcessBisectionSeek();
				} else if (seekState == SeekState.BISECT_TO_KEYPOINT) {
					doProcessBisectionSeek();
				} else if (seekState == SeekState.LINEAR_TO_TARGET) {
					doProcessLinearSeeking();
				} else {
					throw new Error('Invalid seek state ' + seekState);
				}

			} else if (state == State.PLAYING) {

				function doProcessPlay() {

					//console.log(more, codec.audioReady, codec.frameReady, codec.audioTimestamp, codec.frameTimestamp);

					if (paused) {

						// ok we're done for now!
						log('paused during playback; stopping loop');

					} else {

						if ((!codec.hasAudio || codec.audioReady || pendingAudio || dataEnded) &&
							(!codec.hasVideo || codec.frameReady || pendingFrame || decodedFrames.length || dataEnded)
						) {

							var audioState = null,
								playbackPosition = 0,
								readyForAudioDecode,
								audioEnded = false,
								readyForFrameDraw,
								frameDelay = 0,
								readyForFrameDecode,
								// timers never fire earlier than 4ms
								timerMinimum = 4,
								// ok to draw a couple ms early
								fudgeDelta = timerMinimum;


							if (codec.hasAudio && audioFeeder) {
								// Drive on the audio clock!

								audioState = audioFeeder.getPlaybackState();
								playbackPosition = getPlaybackTime(audioState);
								audioEnded = (dataEnded && audioFeeder.durationBuffered == 0);

								if (prebufferingAudio && (audioFeeder.durationBuffered >= audioFeeder.bufferThreshold * 2 || dataEnded)) {
									log('prebuffering audio done; buffered to ' + audioFeeder.durationBuffered);
									startPlayback(playbackPosition);
									prebufferingAudio = false;
								}

								if (audioState.dropped != droppedAudio) {
									log('dropped ' + (audioState.dropped - droppedAudio));
								}
								if (audioState.delayed != delayedAudio) {
									log('delayed ' + (audioState.delayed - delayedAudio));
								}
								droppedAudio = audioState.dropped;
								delayedAudio = audioState.delayed;
								
								readyForAudioDecode = audioFeeder.durationBuffered <=
									audioFeeder.bufferThreshold * 2;

								if (!readyForAudioDecode) {
									// just to skip the remaining items in debug log
								} else if (!codec.audioReady) {
									// NEED MOAR BUFFERS
									readyForAudioDecode = false;
								} else if (pendingAudio >= audioPipelineDepth) {
									// We'll check in when done decoding
									log('audio decode disabled: ' + pendingAudio + ' packets in flight');
									readyForAudioDecode = false;
								}

							} else {
								// No audio; drive on the general clock.
								// @fixme account for dropped frame times...
								playbackPosition = getPlaybackTime();

								// If playing muted with no audio output device,
								// just keep up with audio in general.
								readyForAudioDecode = codec.audioReady && (audioEndTimestamp < playbackPosition);
							}

							if (codec.hasVideo) {
								frameDelay = (frameEndTimestamp - playbackPosition) * 1000;
								actualPerFrameTime = targetPerFrameTime - frameDelay;
								//frameDelay = Math.max(0, frameDelay);
								frameDelay = Math.min(frameDelay, targetPerFrameTime);

								readyForFrameDraw = decodedFrames.length > 0;
								readyForFrameDecode = (pendingFrame == 0) && (decodedFrames.length <= framePipelineDepth) && codec.frameReady;

								var audioSyncThreshold = targetPerFrameTime;
								if (prebufferingAudio) {
									if (readyForFrameDecode) {
										log('decoding a frame during prebuffering');
									}
									readyForFrameDraw = false;
								} else if (readyForFrameDraw && dataEnded && audioEnded) {
									// If audio timeline reached end, make sure the last frame draws
									log('audio timeline ended? ready to draw frame');
								} else if (-frameDelay >= audioSyncThreshold) {
									// late frame!
									if (!stoppedForLateFrame) {
										log('late frame at ' + playbackPosition + ': ' + (-frameDelay) + ' expected ' + audioSyncThreshold);
										lateFrames++;
										if (decodedFrames.length > 1) {
											log('late frame has a neighbor; skipping to next frame');
											decodedFrames.shift();
											frameEndTimestamp = decodedFrames[0].frameEndTimestamp;
											framesProcessed++; // pretend!
											doFrameComplete();
										} else {
											stopPlayback();
											stoppedForLateFrame = true;
										}
									}
								} else if (readyForFrameDraw && stoppedForLateFrame && !readyForFrameDecode && !readyForAudioDecode && frameDelay > fudgeDelta) {
									// catching up, ok if we were early
									log('late frame recovery reached ' + frameDelay);
									startPlayback(playbackPosition);
									stoppedForLateFrame = false;
									readyForFrameDraw = false; // go back through the loop again
								} else if (readyForFrameDraw && stoppedForLateFrame) {
									// draw asap
								} else if (readyForFrameDraw && frameDelay <= fudgeDelta) {
									// on time! draw
								} else {
									// not yet
									readyForFrameDraw = false;
								}
							}

							//log([playbackPosition, frameEndTimestamp, audioEndTimestamp, readyForFrameDraw, readyForFrameDecode, readyForAudioDecode].join(', '));

							if (readyForFrameDecode) {

								log('play loop: ready to decode frame; thread depth: ' + pendingFrame + ', have buffered: ' + decodedFrames.length);

								var endy = decodedFrames.length ? decodedFrames[decodedFrames.length - 1].frameEndTimestamp : frameEndTimestamp;
								if (videoInfo.fps == 0 && (codec.frameTimestamp - endy) > 0) {
									// WebM doesn't encode a frame rate
									targetPerFrameTime = (codec.frameTimestamp - endy) * 1000;
								}
								totalFrameTime += targetPerFrameTime;
								totalFrameCount++;
								
								var nextFrameEndTimestamp = codec.frameTimestamp;
								pendingFrame++;
								var frameDecodeTime = time(function() {
									codec.decodeFrame(function processingDecodeFrame(ok) {
										log('play loop callback: decoded frame');
										pendingFrame--;
										if (ok) {
											// Save the buffer until it's time to draw
											decodedFrames.push({
												yCbCrBuffer: codec.frameBuffer,
												videoCpuTime: codec.videoCpuTime,
												frameEndTimestamp: nextFrameEndTimestamp
											});
										} else {
											// Bad packet or something.
											log('Bad video packet or something');
										}
										pingProcessing();
									});
								});
								if (pendingFrame) {
									proxyTime += frameDecodeTime;
									// We can process something else while that's running
									pingProcessing();
								}

							} else if (readyForAudioDecode) {

								log('play loop: ready for audio; depth: ' + pendingAudio);

								pendingAudio++;
								var nextAudioEndTimestamp = codec.audioTimestamp;
								var audioDecodeTime = time(function() {
									codec.decodeAudio(function processingDecodeAudio(ok) {
										pendingAudio--;
										log('play loop callback: decoded audio');
										audioEndTimestamp = nextAudioEndTimestamp;

										if (ok) {
											var buffer = codec.audioBuffer;
											if (buffer) {
												// Keep track of how much time we spend queueing audio as well
												// This is slow when using the Flash shim on IE 10/11
												bufferTime += time(function() {
													if (audioFeeder) {
														audioFeeder.bufferData(buffer);
													}
												});
												if (!codec.hasVideo) {
													framesProcessed++; // pretend!
													doFrameComplete();
												}
											}
										}
										pingProcessing();
									});
								});
								if (pendingAudio) {
									proxyTime += audioDecodeTime;
									// We can process something else while that's running
									if (codec.audioReady) {
										pingProcessing();
									} else {
										// Trigger a demux immediately if we need it;
										// audio processing is our mostly-idle time
										doProcessPlayDemux();
									}
								}

							} else if (readyForFrameDraw) {

								log('play loop: ready to draw frame');

								if (nextFrameTimer) {
									clearTimeout(nextFrameTimer);
									nextFrameTimer = null;
								}

								// Ready to draw the decoded frame...
								if (thumbnail) {
									self.removeChild(thumbnail);
									thumbnail = null;
								}

								var frame = decodedFrames.shift();
								frameEndTimestamp = frame.frameEndTimestamp;
								currentVideoCpuTime = frame.videoCpuTime;

								drawingTime += time(function() {
									frameSink.drawFrame(frame.yCbCrBuffer);
								});

								framesProcessed++;
								framesPlayed++;

								doFrameComplete();

								pingProcessing();

							} else if (decodedFrames.length && !nextFrameTimer && !prebufferingAudio) {

								if (frameDelay < timerMinimum) {
									// Either we're very close or the frame rate is
									// insanely high (infamous '1000fps bug')
									// Timer will take 4ms anyway, so just check in now.
									log('play loop: very short timer, so going back in');
									pingProcessing();
								} else {
									var targetTimer = frameDelay - timerMinimum;
									// @todo consider using requestAnimationFrame
									log('play loop: setting a timer for drawing ' + targetTimer);
									nextFrameTimer = setTimeout(function() {
										nextFrameTimer = null;
										pingProcessing();
									}, targetTimer);
								}

							} else if (dataEnded && !(pendingAudio || pendingFrame || decodedFrames.length)) {
								log('play loop: playback reached end of data ' + [pendingAudio, pendingFrame, decodedFrames.length]);
								var finalDelay = 0;
								if (codec.hasAudio && audioFeeder) {
									finalDelay = audioFeeder.durationBuffered * 1000;
								}
								if (finalDelay > 0) {
									log('play loop: ending pending ' + finalDelay + ' ms');
									pingProcessing(Math.max(0, finalDelay));
								} else {
									log('play loop: ENDING NOW: playback time ' + getPlaybackTime() + '; frameEndTimestamp: ' + frameEndTimestamp);
									stopPlayback();
									stoppedForLateFrame = false;
									prebufferingAudio = false;
									initialPlaybackOffset = Math.max(audioEndTimestamp, frameEndTimestamp);
									ended = true;
									// @todo implement loop behavior
									paused = true;
									fireEventAsync('pause');
									fireEventAsync('ended');
								}

							} else {

								log('play loop: waiting on async/timers');

							}

						} else {

							log('play loop: demuxing');

							doProcessPlayDemux();
						}
					}
				}
				
				function doProcessPlayDemux() {
					var wasFrameReady = codec.frameReady,
						wasAudioReady = codec.audioReady;
					codec.process(function doProcessPlayDemuxHandler(more) {
						if ((codec.frameReady && !wasFrameReady) || (codec.audioReady && !wasAudioReady)) {
							log('demuxer has packets');
							pingProcessing();
						} else if (more) {
							// Have to process some more pages to find data.
							log('demuxer processing to find more packets');
							pingProcessing();
						} else {
							log('demuxer ran out of data');
							if (!streamEnded) {
								// Ran out of buffered input
								log('demuxer loading more data');
								readBytesAndWait();
							} else {
								// Ran out of stream!
								log('demuxer reached end of data stream');
								dataEnded = true;
								pingProcessing();
							}
						}
					});
				}

				doProcessPlay();

			} else if (state == State.ERROR) {

				// Nothing to do.
				console.log("Reached error state. Sorry bout that.");

			} else {

				throw new Error('Unexpected OGVPlayer state ' + state);

			}
		}

		/**
		 * Are we waiting on an async operation that will return later?
		 */
		function isProcessing() {
			return (stream && stream.waiting) || (codec && codec.processing);
		}

		function readBytesAndWait() {
			stream.readBytes();
		}

		function pingProcessing(delay) {
			if (delay === undefined) {
				delay = -1;
			}
			/*
			if (isProcessing()) {
				// We'll get pinged again when whatever we were doing returns...
				log('REENTRANCY FAIL: asked to pingProcessing() while already waiting');
				return;
			}
			*/
			if (stream && stream.waiting) {
				// wait for this input pls
				log('waiting on input');
				return;
			}
			if (nextProcessingTimer) {
				log('canceling old processing timer');
				clearTimeout(nextProcessingTimer);
				nextProcessingTimer = null;
			}
			var fudge = -1 / 256;
			if (delay > fudge) {
				//log('pingProcessing delay: ' + delay);
				nextProcessingTimer = setTimeout(function() {
					// run through pingProcessing again to check
					// in case some io started asynchronously in the meantime
					pingProcessing();
				}, delay);
			} else if (depth) {
				//log('pingProcessing tail call (' + delay + ')');
				needProcessing = true;
			} else {
				doProcessing();
			}
		}

		var videoInfo,
			audioInfo;

		function startProcessingVideo() {
			if (started || codec) {
				return;
			}

			framesProcessed = 0;
			bufferTime = 0;
			drawingTime = 0;
			proxyTime = 0;
			started = true;
			ended = false;

			codec = new codecClass(codecOptions);
			lastVideoCpuTime = 0;
			lastAudioCpuTime = 0;
			lastDemuxerCpuTime = 0;
			lastBufferTime = 0;
			lastDrawingTime = 0;
			lastProxyTime = 0;
			lastFrameVideoCpuTime = 0;
			lastFrameAudioCpuTime = 0;
			lastFrameDemuxerCpuTime = 0;
			lastFrameBufferTime = 0;
			lastFrameProxyTime = 0;
			lastFrameDrawingTime = 0;
			currentVideoCpuTime = 0;
			codec.onseek = function(offset) {
				if (stream) {
					console.log('SEEKING TO', offset);
					stream.seek(offset);
					//readBytesAndWait();
				}
			};
			codec.init(function() {
				readBytesAndWait();
			});
		}

		function loadCodec(callback) {
			// @todo use the demuxer and codec interfaces directly

			// @todo fix detection proper
			if (enableWebM && currentSrc.match(/\.webm$/i)) {
				codecOptions.type = 'video/webm';
			} else {
				codecOptions.type = 'video/ogg';
			}

			codecClass = OGVWrapperCodec;
			callback();
		}

		/**
		 * HTMLMediaElement load method
		 *
		 * https://www.w3.org/TR/html5/embedded-content-0.html#concept-media-load-algorithm
		 */
		self.load = function() {
			prepForLoad();
		};

		function prepForLoad(preload) {
			stopVideo();

			function doLoad() {
				// @todo networkState == NETWORK_LOADING
				stream = new StreamFile({
					url: self.src,
					bufferSize: 32768, //65536 * 4,
					onstart: function() {
						loading = false;

						// @todo handle failure / unrecognized type

						currentSrc = self.src;

						// Fire off the read/decode/draw loop...
						byteLength = stream.bytesTotal;

						// If we get X-Content-Duration, that's as good as an explicit hint
						var durationHeader = stream.getResponseHeader('X-Content-Duration');
						if (typeof durationHeader === 'string') {
							duration = parseFloat(durationHeader);
						}
						loadCodec(startProcessingVideo);
					},
					onread: function(data) {
						log('got input ' + [data.byteLength]);

						// Save chunk to pass into the codec's buffer
						actionQueue.push(function doReceiveInput() {
							codec.receiveInput(data, function() {
								pingProcessing();
							});
						});

						if (isProcessing()) {
							// We're waiting on the codec already...
						} else {
							pingProcessing();
						}
					},
					ondone: function() {
						// @todo record doneness in networkState
						log('stream is at end!');
						streamEnded = true;

						if (isProcessing()) {
							// We're waiting on the codec already...
						} else {
							// Let the read/decode/draw loop know we're out!
							pingProcessing();
						}
					},
					onerror: function(err) {
						// @todo handle failure to initialize
						console.log("reading error: " + err);
						stopPlayback();
						state = State.ERROR;
					}
				});
			}

			// @todo networkState = self.NETWORK_NO_SOURCE;
			// @todo show poster
			// @todo set 'delay load event flag'

			currentSrc = '';
			loading = true;
			actionQueue.push(function() {
				if (preload && self.preload === 'none') {
					// Done for now, we'll pick up if someone hits play() or load()
					loading = false;
				} else {
					doLoad();
				}
			});
			pingProcessing(0);
		}

		/**
		 * HTMLMediaElement canPlayType method
		 */
		self.canPlayType = function(contentType) {
			var type = new OGVMediaType(contentType);
			if (type.minor === 'ogg' &&
				(type.major === 'audio' || type.major === 'video' || type.major === 'application')) {
				if (type.codecs) {
					var supported = ['vorbis', 'opus', 'theora'],
						knownCodecs = 0,
						unknownCodecs = 0;
					type.codecs.forEach(function(codec) {
						if (supported.indexOf(codec) >= 0) {
							knownCodecs++;
						} else {
							unknownCodecs++;
						}
					});
					if (knownCodecs === 0) {
						return '';
					} else if (unknownCodecs > 0) {
						return '';
					}
					// All listed codecs are ones we know. Neat!
					return 'probably';
				} else {
					return 'maybe';
				}
			} else {
				// @todo when webm support is more complete, handle it
				return '';
			}
		};

		/**
		 * HTMLMediaElement play method
		 */
		self.play = function() {
			if (!muted && !audioOptions.audioContext) {
				OGVPlayer.initSharedAudioContext();
			}

			if (paused) {
				startedPlaybackInDocument = document.body.contains(self);

				paused = false;

				if (state == State.SEEKING) {

					// Seeking? Just make sure we know to pick up after.

				} else if (started && codec && codec.loadedMetadata) {

					if (ended && stream && byteLength) {

						log('.play() starting over after end');
						seek(0);

					} else {
						log('.play() while already started');
					}

					state = State.READY;
					if (isProcessing()) {
						// waiting on the codec already
					} else {
						pingProcessing();
					}

				} else if (loading) {

					log('.play() while loading');

				} else {

					log('.play() before started');

					// Let playback begin when metadata loading is complete
					if (!stream) {
						self.load();
					}

				}
			}
		};

		/**
		 * custom getPlaybackStats method
		 */
		self.getPlaybackStats = function() {
			return {
				targetPerFrameTime: targetPerFrameTime,
				framesProcessed: framesProcessed,
				playTime: playTime,
				demuxingTime: codec ? (codec.demuxerCpuTime - lastDemuxerCpuTime) : 0,
				videoDecodingTime: codec ? (codec.videoCpuTime - lastVideoCpuTime) : 0,
				audioDecodingTime: codec ? (codec.audioCpuTime - lastAudioCpuTime) : 0,
				bufferTime: bufferTime - lastBufferTime,
				drawingTime: drawingTime - lastDrawingTime,
				proxyTime: proxyTime - lastProxyTime,
				droppedAudio: droppedAudio,
				delayedAudio: delayedAudio,
				jitter: totalJitter / framesProcessed,
				lateFrames: lateFrames
			};
		};
		self.resetPlaybackStats = function() {
			framesProcessed = 0;
			playTime = 0;
			if (codec) {
				lastDemuxerCpuTime = codec.demuxerCpuTime;
				lastVideoCpuTime = codec.videoCpuTime;
				lastAudioCpuTime = codec.audioCpuTime;
			}
			lastBufferTime = bufferTime;
			lastDrawingTime = drawingTime;
			lastProxyTime = proxyTime;
			totalJitter = 0;
			totalFrameTime = 0;
			totalFrameCount = 0;
		};

		self.getVideoFrameSink = function() {
			return frameSink;
		};

		self.getCanvas = function() {
			return canvas;
		};

		/**
		 * HTMLMediaElement pause method
		 */
		self.pause = function() {
			if (!paused) {
				clearTimeout(nextProcessingTimer);
				nextProcessingTimer = null;
				stopPlayback();
				prebufferingAudio = false;
				stoppedForLateFrame = false;
				paused = true;
				fireEvent('pause');
			}
		};

		/**
		 * custom 'stop' method
		 */
		self.stop = function() {
			stopVideo();
			paused = true;
		};

		/**
		 * @todo implement the fast part of the behavior!
		 */
		self.fastSeek = function(seekToTime) {
			self.currentTime = +seekToTime;
		};

		/**
		 * HTMLMediaElement src property
		 */
		Object.defineProperty(self, "src", {
			get: function getSrc() {
				return self.getAttribute('src') || '';
			},
			set: function setSrc(val) {
				self.setAttribute('src', val);
				loading = false; // just in case?
				prepForLoad("interactive");
			}
		});

		/**
		 * HTMLMediaElement buffered property
		 */
		Object.defineProperty(self, "buffered", {
			get: function getBuffered() {
				var estimatedBufferTime;
				if (stream && byteLength && duration) {
					estimatedBufferTime = (stream.bytesBuffered / byteLength) * duration;
				} else {
					estimatedBufferTime = 0;
				}
				return new OGVTimeRanges([[0, estimatedBufferTime]]);
			}
		});

		/**
		 * HTMLMediaElement seekable property
		 */
		Object.defineProperty(self, "seekable", {
			get: function getSeekable() {
				if (self.duration < Infinity && stream && stream.seekable && codec && codec.seekable) {
					return new OGVTimeRanges([[0, duration]]);
				} else {
					return new OGVTimeRanges([]);
				}
			}
		});

		/**
		 * HTMLMediaElement currentTime property
		 */
		Object.defineProperty(self, "currentTime", {
			get: function getCurrentTime() {
				if (state == State.SEEKING) {
					return seekTargetTime;
				} else {
					if (codec) {
						if (state == State.PLAYING && !paused) {
							return getPlaybackTime();
						} else {
							return initialPlaybackOffset;
						}
					} else {
						return 0;
					}
				}
			},
			set: function setCurrentTime(val) {
				if (stream && byteLength && duration) {
					seek(val);
				}
			}
		});

		/**
		 * HTMLMediaElement duration property
		 */
		Object.defineProperty(self, "duration", {
			get: function getDuration() {
				if (codec && codec.loadedMetadata) {
					if (duration !== null) {
						return duration;
					} else {
						return Infinity;
					}
				} else {
					return NaN;
				}
			}
		});

		/**
		 * HTMLMediaElement paused property
		 */
		Object.defineProperty(self, "paused", {
			get: function getPaused() {
				return paused;
			}
		});

		/**
		 * HTMLMediaElement ended property
		 */
		Object.defineProperty(self, "ended", {
			get: function getEnded() {
				return ended;
			}
		});

		/**
		 * HTMLMediaElement ended property
		 */
		Object.defineProperty(self, "seeking", {
			get: function getSeeking() {
				return (state == State.SEEKING);
			}
		});

		/**
		 * HTMLMediaElement muted property
		 */
		Object.defineProperty(self, "muted", {
			get: function getMuted() {
				return muted;
			},
			set: function setMuted(val) {
				muted = val;
				if (audioFeeder) {
					audioFeeder.muted = muted;
				} else if (started && !muted && codec && codec.hasAudio) {
					log('unmuting: switching from timer to audio clock');
					initAudioFeeder();
					startPlayback(audioEndTimestamp);
				}
				fireEventAsync('volumechange');
			}
		});

		Object.defineProperty(self, "poster", {
			get: function getPoster() {
				return poster;
			},
			set: function setPoster(val) {
				poster = val;
				if (!started) {
					if (thumbnail) {
						self.removeChild(thumbnail);
					}
					thumbnail = new Image();
					thumbnail.src = poster;
					thumbnail.className = 'ogvjs-poster';
					thumbnail.style.position = 'absolute';
					thumbnail.style.top = '0';
					thumbnail.style.left = '0';
					thumbnail.style.width = '100%';
					thumbnail.style.height = '100%';
					thumbnail.style.objectFit = 'contain';
					thumbnail.style.visibility = 'hidden';
					thumbnail.addEventListener('load', function() {
						if (thumbnail === this) {
							OGVPlayer.styleManager.appendRule('.' + instanceId, {
								width: thumbnail.naturalWidth + 'px',
								height: thumbnail.naturalHeight + 'px'
							});
							OGVPlayer.updatePositionOnResize();
							thumbnail.style.visibility = 'visible';
						}
					});
					self.appendChild(thumbnail);
				}
			}
		});

		// Video metadata properties...
		Object.defineProperty(self, "videoWidth", {
			get: function getVideoWidth() {
				if (videoInfo) {
					return videoInfo.displayWidth;
				} else {
					return 0;
				}
			}
		});
		Object.defineProperty(self, "videoHeight", {
			get: function getVideoHeight() {
				if (videoInfo) {
					return videoInfo.displayHeight;
				} else {
					return 0;
				}
			}
		});
		Object.defineProperty(self, "ogvjsVideoFrameRate", {
			get: function getOgvJsVideoFrameRate() {
				if (videoInfo) {
					if (videoInfo.fps == 0) {
						return totalFrameCount / (totalFrameTime / 1000);
					} else {
						return videoInfo.fps;
					}
				} else {
					return 0;
				}
			}
		});

		// Audio metadata properties...
		Object.defineProperty(self, "ogvjsAudioChannels", {
			get: function getOgvJsAudioChannels() {
				if (audioInfo) {
					return audioInfo.channels;
				} else {
					return 0;
				}
			}
		});
		Object.defineProperty(self, "ogvjsAudioSampleRate", {
			get: function getOgvJsAudioChannels() {
				if (audioInfo) {
					return audioInfo.rate;
				} else {
					return 0;
				}
			}
		});

		// Display size...
		var width = 0, height = 0;
		/**
		 * @property width
		 * @todo reflect to the width attribute?
		 */
		Object.defineProperty(self, "width", {
			get: function getWidth() {
				return width;
			},
			set: function setWidth(val) {
				width = parseInt(val, 10);
				self.style.width = width + 'px';
				OGVPlayer.updatePositionOnResize();
			}
		});

		/**
		 * @property height
		 * @todo reflect to the height attribute?
		 */
		Object.defineProperty(self, "height", {
			get: function getHeight() {
				return height;
			},
			set: function setHeight(val) {
				height = parseInt(val, 10);
				self.style.height = height + 'px';
				OGVPlayer.updatePositionOnResize();
			}
		});

		/**
		 * @property autoplay {boolean} stub prop
		 * @todo reflect to the autoplay attribute?
		 * @todo implement actual autoplay behavior
		 */
		Object.defineProperty(self, "autoplay", {
			get: function getAutoplay() {
				return false;
			},
			set: function setAutoplay(val) {
				// ignore
			}
		});

		/**
		 * @property controls {boolean} stub prop
		 * @todo reflect to the controls attribute?
		 * @todo implement actual control behavior
		 */
		Object.defineProperty(self, "controls", {
			get: function getControls() {
				return false;
			},
			set: function setControls(val) {
				// ignore
			}
		});

		/**
		 * @property loop {boolean} stub prop
		 * @todo reflect to the controls attribute?
		 * @todo implement actual loop behavior
		 */
		Object.defineProperty(self, "loop", {
			get: function getLoop() {
				return false;
			},
			set: function setLoop(val) {
				// ignore
			}
		});

		/**
		 * @property crossOrigin {string|null} stub prop
		 * @todo reflect to the crossorigin attribute?
		 * @todo implement actual behavior
		 */
		Object.defineProperty(self, "crossOrigin", {
			get: function getCrossOrigin() {
				return null;
			},
			set: function setCrossOrigin(val) {
				// ignore
			}
		});

		/**
		 * Returns the URL to the currently-playing resource.
		 * @property currentSrc {string|null}
		 */
		Object.defineProperty(self, "currentSrc", {
			get: function getCurrentSrc() {
				// @todo return absolute URL per spec
				return currentSrc;
			}
		});

		Object.defineProperty(self, "defaultMuted", {
			get: function getDefaultMuted() {
				return false;
			}
		});

		Object.defineProperty(self, "defaultPlaybackRate", {
			get: function getDefaultPlaybackRate() {
				return 1;
			}
		});

		/**
		 * @property error {string|null}
		 * @todo implement
		 */
		Object.defineProperty(self, "error", {
			get: function getError() {
				if (state === State.ERROR) {
					return "error occurred in media procesing";
				} else {
					return null;
				}
			}
		});
		/**
	 	 * @property preload {string}
		 */
		Object.defineProperty(self, "preload", {
			get: function getPreload() {
				return self.getAttribute('preload') || '';
			},
			set: function setPreload(val) {
				self.setAttribute('preload', val);
			}
		});

		/**
		 * @property readyState {number}
		 * @todo return more accurate info about availability of data
		 */
		Object.defineProperty(self, "readyState", {
			get: function getReadyState() {
				if (stream && codec && codec.loadedMetadata) {
					// for now we don't really calc this stuff
					// just pretend we have lots of data coming in already
					return OGVPlayer.HAVE_ENOUGH_DATA;
				} else {
					return OGVPlayer.HAVE_NOTHING;
				}
			}
		});

		/**
		 * @property networkState {number}
		 * @todo implement
		 */
		Object.defineProperty(self, "networkState", {
			get: function getNetworkState() {
				if (stream) {
					if (stream.waiting) {
						return OGVPlayer.NETWORK_LOADING;
					} else {
						return OGVPlayer.NETWORK_IDLE;
					}
				} else {
					if (self.readyState == OGVPlayer.HAVE_NOTHING) {
						return OGVPlayer.NETWORK_EMPTY;
					} else {
						return OGVPlayer.NETWORK_NO_SOURCE;
					}
				}
			}
		});

		/**
		 * @property playbackRate {number}
		 * @todo implement
		 */
		Object.defineProperty(self, "playbackRate", {
			get: function getPlaybackRate() {
				return 1;
			},
			set: function setPlaybackRate(val) {
				// ignore
			}
		});

		/**
		 * @property played {OGVTimeRanges}
		 * @todo implement correctly more or less
		 */
		Object.defineProperty(self, "played", {
			get: function getPlayed() {
				return new OGVTimeRanges([[0, self.currentTime]]);
			}
		});

		var _volume = 1;

		/**
		 * @property volume {number}
		 * @todo implement
		 */
		Object.defineProperty(self, "volume", {
			get: function getVolume() {
				return _volume;
			},
			set: function setVolume(val) {
				_volume = +val;
				if (audioFeeder) {
					audioFeeder.volume = _volume;
				}
				fireEventAsync('volumechange');
			}
		});


		// Events!

		/**
		 * custom onframecallback, takes frame decode time in ms
		 */
		self.onframecallback = null;

		/**
		 * Network state events
		 * @todo implement
		 */
		self.onloadstate = null;
		self.onprogress = null;
		self.onsuspend = null;
		self.onabort = null;
		self.onemptied = null;
		self.onstalled = null;

		/**
		 * Called when all metadata is available.
		 * Note in theory we must know 'duration' at this point.
		 */
		self.onloadedmetadata = null;

		/**
		 * Called when enough data for first frame is ready.
		 * @todo implement
		 */
		self.onloadeddata = null;

		/**
		 * Called when enough data is ready to start some playback.
		 * @todo implement
		 */
		self.oncanplay = null;

		/**
		 * Called when enough data is ready to play through to the
		 * end if no surprises in network download rate.
		 * @todo implement
		 */
		self.oncanplaythrough = null;

		/**
		 * Called when playback continues after a stall
		 * @todo implement
		 */
		self.onplaying = null;

		/**
		 * Called when playback is delayed waiting on data
		 * @todo implement
		 */
		self.onwaiting = null;

		/**
		 * Called when seeking begins
		 * @todo implement
		 */
		self.onseeking = null;

		/**
		 * Called when seeking ends
		 * @todo implement
		 */
		self.onseeked = null;

		/**
		 * Called when playback ends
		 */
		self.onended = null;

		/**
		 * Called when duration becomes known
		 * @todo implement
		 */
		self.ondurationchange = null;

		/**
		 * Called periodically during playback
		 */
		self.ontimeupdate = null;

		/**
		 * Called when we start playback
		 */
		self.onplay = null;

		/**
		 * Called when we get paused
		 */
		self.onpause = null;

		/**
		 * Called when the playback rate changes
		 * @todo implement
		 */
		self.onratechange = null;

		/**
		 * Called when the size of the video stream changes
		 * @todo implement
		 */
		self.onresize = null;

		/**
		 * Called when the volume setting changes
		 * @todo implement
		 */
		self.onvolumechange = null;

		// Copy the various public state constants in
		setupConstants(self);

		return self;
	};

	OGVPlayer.initSharedAudioContext = function() {
		AudioFeeder.initSharedAudioContext();
	};

	/**
	 * Set up constants on the class and instances
	 */
	var constants = {
		/**
		 * Constants for networkState
		 */
		NETWORK_EMPTY: 0,
		NETWORK_IDLE: 1,
		NETWORK_LOADING: 2,
		NETWORK_NO_SOURCE: 3,

		/**
		 * Constants for readyState
		 */
		HAVE_NOTHING: 0,
		HAVE_METADATA: 1,
		HAVE_CURRENT_DATA: 2,
		HAVE_FUTURE_DATA: 3,
		HAVE_ENOUGH_DATA: 4
	};
	function setupConstants(obj) {
		for (var constName in constants) {
			if (constants.hasOwnProperty(constName)) {
				(function(name, val) {
					Object.defineProperty(obj, constName, {
						get: function() {
							return val;
						}
					});
				})(constName, constants[constName]);
			}
		}
	}
	setupConstants(OGVPlayer);

	OGVPlayer.instanceCount = 0;

	function StyleManager() {
		var self = this;
		var el = document.createElement('style');
		el.type = 'text/css';
		el.textContent = 'ogvjs { display: inline-block; position: relative; }';
		document.head.appendChild(el);

		var sheet = el.sheet;

		self.appendRule = function(selector, defs) {
			var bits = [];
			for (var prop in defs) {
				if (defs.hasOwnProperty(prop)) {
					bits.push(prop + ':' + defs[prop]);
				}
			}
			var rule = selector + '{' + bits.join(';') + '}';
			sheet.insertRule(rule, sheet.cssRules.length - 1);
		};
	}
	OGVPlayer.styleManager = new StyleManager();

	// IE 10/11 and Edge 12 don't support object-fit.
	// Also just for fun, IE 10 doesn't support 'auto' sizing on canvas. o_O
	OGVPlayer.supportsObjectFit = (typeof document.createElement('div').style.objectFit === 'string');
	if (OGVPlayer.supportsObjectFit && navigator.userAgent.match(/iPhone|iPad|iPod Touch/)) {
		// Safari for iOS 8/9 supports it but positions our <canvas> incorrectly when using WebGL >:(
		OGVPlayer.supportsObjectFit = false;
	}
	if (OGVPlayer.supportsObjectFit) {
		OGVPlayer.updatePositionOnResize = function() {
			// no-op
		};
	} else {
		OGVPlayer.updatePositionOnResize = function() {
			function fixup(el, width, height) {
				var container = el.offsetParent || el.parentNode,
					containerAspect = container.offsetWidth / container.offsetHeight,
					intrinsicAspect = width / height;
				if (intrinsicAspect > containerAspect) {
					var vsize = container.offsetWidth / intrinsicAspect,
						vpad = (container.offsetHeight - vsize) / 2;
					el.style.width = '100%';
					el.style.height = vsize + 'px';
					el.style.marginLeft = 0;
					el.style.marginRight = 0;
					el.style.marginTop = vpad + 'px';
					el.style.marginBottom = vpad + 'px';
				} else {
					var hsize = container.offsetHeight * intrinsicAspect,
						hpad = (container.offsetWidth - hsize) / 2;
					el.style.width = hsize + 'px';
					el.style.height = '100%';
					el.style.marginLeft = hpad + 'px';
					el.style.marginRight = hpad + 'px';
					el.style.marginTop = 0;
					el.style.marginBottom = 0;
				}
			}
			function queryOver(selector, callback) {
				var nodeList = document.querySelectorAll(selector),
					nodeArray = Array.prototype.slice.call(nodeList);
				nodeArray.forEach(callback);
			}

			queryOver('ogvjs > canvas', function(canvas) {
				fixup(canvas, canvas.width, canvas.height);
			});
			queryOver('ogvjs > img', function(poster) {
				fixup(poster, poster.naturalWidth, poster.naturalHeight);
			});
		};
		var fullResizeVideo = function() {
			// fullscreens may ping us before the resize happens
			setTimeout(OGVPlayer.updatePositionOnResize, 0);
		};

		window.addEventListener('resize', OGVPlayer.updatePositionOnResize);
		window.addEventListener('orientationchange', OGVPlayer.updatePositionOnResize);

		document.addEventListener('fullscreenchange', fullResizeVideo);
		document.addEventListener('mozfullscreenchange', fullResizeVideo);
		document.addEventListener('webkitfullscreenchange', fullResizeVideo);
		document.addEventListener('MSFullscreenChange', fullResizeVideo);
	}

	module.exports = OGVPlayer;


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	/*
	Copyright (c) 2014-2016 Brion Vibber <brion@pobox.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	MPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	(function() {
	  "use strict";

	  var FrameSink = __webpack_require__(10),
	    SoftwareFrameSink = __webpack_require__(11),
	    WebGLFrameSink = __webpack_require__(14);

	  /**
	   * @typedef {Object} YUVCanvasOptions
	   * @property {boolean} webGL - Whether to use WebGL to draw to the canvas and accelerate color space conversion. If left out, defaults to auto-detect.
	   */

	  var YUVCanvas = {
	    FrameSink: FrameSink,

	    SoftwareFrameSink: SoftwareFrameSink,

	    WebGLFrameSink: WebGLFrameSink,

	    /**
	     * Attach a suitable FrameSink instance to an HTML5 canvas element.
	     *
	     * This will take over the drawing context of the canvas and may turn
	     * it into a WebGL 3d canvas if possible. Do not attempt to use the
	     * drawing context directly after this.
	     *
	     * @param {HTMLCanvasElement} canvas - HTML canvas element to attach to
	     * @param {YUVCanvasOptions} options - map of options
	     * @returns {FrameSink} - instance of suitable subclass.
	     */
	    attach: function(canvas, options) {
	      options = options || {};
	      var webGL = ('webGL' in options) ? options.webGL : WebGLFrameSink.isAvailable();
	      if (webGL) {
	        return new WebGLFrameSink(canvas, options);
	      } else {
	        return new SoftwareFrameSink(canvas, options);
	      }
	    }
	  };

	  module.exports = YUVCanvas;
	})();


/***/ },
/* 10 */
/***/ function(module, exports) {

	(function() {
	  "use strict";

	  /**
	   * Create a YUVCanvas and attach it to an HTML5 canvas element.
	   *
	   * This will take over the drawing context of the canvas and may turn
	   * it into a WebGL 3d canvas if possible. Do not attempt to use the
	   * drawing context directly after this.
	   *
	   * @param {HTMLCanvasElement} canvas - HTML canvas element to attach to
	   * @param {YUVCanvasOptions} options - map of options
	   * @throws exception if WebGL requested but unavailable
	   * @constructor
	   * @abstract
	   */
	  function FrameSink(canvas, options) {
	    throw new Error('abstract');
	  }

	  /**
	   * Draw a single YUV frame on the underlying canvas, converting to RGB.
	   * If necessary the canvas will be resized to the optimal pixel size
	   * for the given buffer's format.
	   *
	   * @param {YUVBuffer} buffer - the YUV buffer to draw
	   * @see {@link https://www.npmjs.com/package/yuv-buffer|yuv-buffer} for format
	   */
	  FrameSink.prototype.drawFrame = function(buffer) {
	    throw new Error('abstract');
	  };

	  /**
	   * Clear the canvas using appropriate underlying 2d or 3d context.
	   */
	  FrameSink.prototype.clear = function() {
	    throw new Error('abstract');
	  };

	  module.exports = FrameSink;

	})();


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	/*
	Copyright (c) 2014-2016 Brion Vibber <brion@pobox.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	MPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	(function() {
		"use strict";

		var FrameSink = __webpack_require__(10),
			YCbCr = __webpack_require__(12);

		/**
		 * @param {HTMLCanvasElement} canvas - HTML canvas eledment to attach to
		 * @constructor
		 */
		function SoftwareFrameSink(canvas) {
			var self = this,
				ctx = canvas.getContext('2d'),
				imageData = null,
				resampleCanvas = null,
				resampleContext = null;



			function initImageData(width, height) {
				imageData = ctx.createImageData(width, height);

				// Prefill the alpha to opaque
				var data = imageData.data,
					pixelCount = width * height * 4;
				for (var i = 0; i < pixelCount; i += 4) {
					data[i + 3] = 255;
				}
			}

			function initResampleCanvas(cropWidth, cropHeight) {
				resampleCanvas = document.createElement('canvas');
				resampleCanvas.width = cropWidth;
				resampleCanvas.height = cropHeight;
				resampleContext = resampleCanvas.getContext('2d');
			}

			/**
			 * Actually draw a frame into the canvas.
			 * @param {YUVFrame} buffer - YUV frame buffer object to draw
			 */
			self.drawFrame = function drawFrame(buffer) {
				var format = buffer.format;

				if (canvas.width !== format.displayWidth || canvas.height || format.displayHeight) {
					// Keep the canvas at the right size...
					canvas.width = format.displayWidth;
					canvas.height = format.displayHeight;
				}

				if (imageData === null ||
						imageData.width != format.width ||
						imageData.height != format.height) {
					initImageData(format.width, format.height);
				}

				// YUV -> RGB over the entire encoded frame
				YCbCr.convertYCbCr(buffer, imageData.data);

				var resample = (format.cropWidth != format.displayWidth || format.cropHeight != format.displayHeight);
				var drawContext;
				if (resample) {
					// hack for non-square aspect-ratio
					// putImageData doesn't resample, so we have to draw in two steps.
					if (!resampleCanvas) {
						initResampleCanvas(format.cropWidth, format.cropHeight);
					}
					drawContext = resampleContext;
				} else {
					drawContext = ctx;
				}

				// Draw cropped frame to either the final or temporary canvas
				drawContext.putImageData(imageData,
								         0, 0,
												 format.cropLeft, format.cropTop,
												 format.cropWidth, format.cropHeight);

				if (resample) {
					ctx.drawImage(resampleCanvas, 0, 0, format.displayWidth, format.displayHeight);
				}
			};

			self.clear = function() {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
			};

			return self;
		}

		SoftwareFrameSink.prototype = Object.create(FrameSink.prototype);

		module.exports = SoftwareFrameSink;
	})();


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	/*
	Copyright (c) 2014-2016 Brion Vibber <brion@pobox.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	MPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	(function() {
		"use strict";

		var depower = __webpack_require__(13);

		/**
		 * Basic YCbCr->RGB conversion
		 *
		 * @author Brion Vibber <brion@pobox.com>
		 * @copyright 2014-2016
		 * @license MIT-style
		 *
		 * @param {YUVFrame} buffer - input frame buffer
		 * @param {Uint8ClampedArray} output - array to draw RGBA into
		 * Assumes that the output array already has alpha channel set to opaque.
		 */
		function convertYCbCr(buffer, output) {
			var width = buffer.format.width,
				height = buffer.format.height,
				hdec = depower(buffer.format.width / buffer.format.chromaWidth),
				vdec = depower(buffer.format.height / buffer.format.chromaHeight),
				bytesY = buffer.y.bytes,
				bytesCb = buffer.u.bytes,
				bytesCr = buffer.v.bytes,
				strideY = buffer.y.stride,
				strideCb = buffer.u.stride,
				strideCr = buffer.v.stride,
				outStride = 4 * width,
				YPtr = 0, Y0Ptr = 0, Y1Ptr = 0,
				CbPtr = 0, CrPtr = 0,
				outPtr = 0, outPtr0 = 0, outPtr1 = 0,
				colorCb = 0, colorCr = 0,
				multY = 0, multCrR = 0, multCbCrG = 0, multCbB = 0,
				x = 0, y = 0, xdec = 0, ydec = 0;

			if (hdec == 1 && vdec == 1) {
				// Optimize for 4:2:0, which is most common
				outPtr0 = 0;
				outPtr1 = outStride;
				ydec = 0;
				for (y = 0; y < height; y += 2) {
					Y0Ptr = y * strideY;
					Y1Ptr = Y0Ptr + strideY;
					CbPtr = ydec * strideCb;
					CrPtr = ydec * strideCr;
					for (x = 0; x < width; x += 2) {
						colorCb = bytesCb[CbPtr++];
						colorCr = bytesCr[CrPtr++];

						// Quickie YUV conversion
						// https://en.wikipedia.org/wiki/YCbCr#ITU-R_BT.2020_conversion
						// multiplied by 256 for integer-friendliness
						multCrR   = (409 * colorCr) - 57088;
						multCbCrG = (100 * colorCb) + (208 * colorCr) - 34816;
						multCbB   = (516 * colorCb) - 70912;

						multY = (298 * bytesY[Y0Ptr++]);
						output[outPtr0++] = (multY + multCrR) >> 8;
						output[outPtr0++] = (multY - multCbCrG) >> 8;
						output[outPtr0++] = (multY + multCbB) >> 8;
						outPtr0++;

						multY = (298 * bytesY[Y0Ptr++]);
						output[outPtr0++] = (multY + multCrR) >> 8;
						output[outPtr0++] = (multY - multCbCrG) >> 8;
						output[outPtr0++] = (multY + multCbB) >> 8;
						outPtr0++;

						multY = (298 * bytesY[Y1Ptr++]);
						output[outPtr1++] = (multY + multCrR) >> 8;
						output[outPtr1++] = (multY - multCbCrG) >> 8;
						output[outPtr1++] = (multY + multCbB) >> 8;
						outPtr1++;

						multY = (298 * bytesY[Y1Ptr++]);
						output[outPtr1++] = (multY + multCrR) >> 8;
						output[outPtr1++] = (multY - multCbCrG) >> 8;
						output[outPtr1++] = (multY + multCbB) >> 8;
						outPtr1++;
					}
					outPtr0 += outStride;
					outPtr1 += outStride;
					ydec++;
				}
			} else {
				outPtr = 0;
				for (y = 0; y < height; y++) {
					xdec = 0;
					ydec = y >> vdec;
					YPtr = y * strideY;
					CbPtr = ydec * strideCb;
					CrPtr = ydec * strideCr;

					for (x = 0; x < width; x++) {
						xdec = x >> hdec;
						colorCb = bytesCb[CbPtr + xdec];
						colorCr = bytesCr[CrPtr + xdec];

						// Quickie YUV conversion
						// https://en.wikipedia.org/wiki/YCbCr#ITU-R_BT.2020_conversion
						// multiplied by 256 for integer-friendliness
						multCrR   = (409 * colorCr) - 57088;
						multCbCrG = (100 * colorCb) + (208 * colorCr) - 34816;
						multCbB   = (516 * colorCb) - 70912;

						multY = 298 * bytesY[YPtr++];
						output[outPtr++] = (multY + multCrR) >> 8;
						output[outPtr++] = (multY - multCbCrG) >> 8;
						output[outPtr++] = (multY + multCbB) >> 8;
						outPtr++;
					}
				}
			}
		}

		module.exports = {
			convertYCbCr: convertYCbCr
		};
	})();


/***/ },
/* 13 */
/***/ function(module, exports) {

	/*
	Copyright (c) 2014-2016 Brion Vibber <brion@pobox.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	MPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	(function() {
	  "use strict";

	  /**
	   * Convert a ratio into a bit-shift count; for instance a ratio of 2
	   * becomes a bit-shift of 1, while a ratio of 1 is a bit-shift of 0.
	   *
	   * @author Brion Vibber <brion@pobox.com>
	   * @copyright 2016
	   * @license MIT-style
	   *
	   * @param {number} ratio - the integer ratio to convert.
	   * @returns {number} - number of bits to shift to multiply/divide by the ratio.
	   * @throws exception if given a non-power-of-two
	   */
	  function depower(ratio) {
	    var shiftCount = 0,
	      n = ratio >> 1;
	    while (n != 0) {
	      n = n >> 1;
	      shiftCount++
	    }
	    if (ratio !== (1 << shiftCount)) {
	      throw 'chroma plane dimensions must be power of 2 ratio to luma plane dimensions; got ' + ratio;
	    }
	    return shiftCount;
	  }

	  module.exports = depower;
	})();


/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	/*
	Copyright (c) 2014-2016 Brion Vibber <brion@pobox.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy of
	this software and associated documentation files (the "Software"), to deal in
	the Software without restriction, including without limitation the rights to
	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	MPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/
	(function() {
		"use strict";

		var FrameSink = __webpack_require__(10),
			shaders = __webpack_require__(15);

		/**
		 * Warning: canvas must not have been used for 2d drawing prior!
		 *
		 * @param {HTMLCanvasElement} canvas - HTML canvas element to attach to
		 * @constructor
		 */
		function WebGLFrameSink(canvas) {
			var self = this,
				gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl'),
				debug = false; // swap this to enable more error checks, which can slow down rendering

			if (gl === null) {
				throw new Error('WebGL unavailable');
			}

			// GL!
			function checkError() {
				if (debug) {
					err = gl.getError();
					if (err !== 0) {
						throw new Error("GL error " + err);
					}
				}
			}

			function compileShader(type, source) {
				var shader = gl.createShader(type);
				gl.shaderSource(shader, source);
				gl.compileShader(shader);

				if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
					var err = gl.getShaderInfoLog(shader);
					gl.deleteShader(shader);
					throw new Error('GL shader compilation for ' + type + ' failed: ' + err);
				}

				return shader;
			}


			var vertexShader,
				fragmentShader,
				program,
				buf,
				err;

			// In the world of GL there are no rectangles.
			// There are only triangles.
			// THERE IS NO SPOON.
			var rectangle = new Float32Array([
				// First triangle (top left, clockwise)
				-1.0, -1.0,
				+1.0, -1.0,
				-1.0, +1.0,

				// Second triangle (bottom right, clockwise)
				-1.0, +1.0,
				+1.0, -1.0,
				+1.0, +1.0
			]);

			var textures = {};
			function attachTexture(name, register, index, width, height, data) {
				var texture,
					texWidth = WebGLFrameSink.stripe ? (width / 4) : width,
					format = WebGLFrameSink.stripe ? gl.RGBA : gl.LUMINANCE,
					filter = WebGLFrameSink.stripe ? gl.NEAREST : gl.LINEAR;

				if (textures[name]) {
					// Reuse & update the existing texture
					texture = textures[name];
				} else {
					textures[name] = texture = gl.createTexture();
					checkError();

					gl.uniform1i(gl.getUniformLocation(program, name), index);
					checkError();
				}
				gl.activeTexture(register);
				checkError();
				gl.bindTexture(gl.TEXTURE_2D, texture);
				checkError();
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
				checkError();
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
				checkError();
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, filter);
				checkError();
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, filter);
				checkError();

				gl.texImage2D(
					gl.TEXTURE_2D,
					0, // mip level
					format, // internal format
					texWidth,
					height,
					0, // border
					format, // format
					gl.UNSIGNED_BYTE, //type
					data // data!
				);
				checkError();

				return texture;
			}

			function buildStripe(width, height) {
				var len = width * height,
					out = new Uint32Array(len);
				for (var i = 0; i < len; i += 4) {
					out[i    ] = 0x000000ff;
					out[i + 1] = 0x0000ff00;
					out[i + 2] = 0x00ff0000;
					out[i + 3] = 0xff000000;
				}
				return new Uint8Array(out.buffer);
			}

			function init(buffer) {
				vertexShader = compileShader(gl.VERTEX_SHADER, shaders.vertex);
				if (WebGLFrameSink.stripe) {
					fragmentShader = compileShader(gl.FRAGMENT_SHADER, shaders.fragmentStripe);
				} else {
					fragmentShader = compileShader(gl.FRAGMENT_SHADER, shaders.fragment);
				}

				program = gl.createProgram();
				gl.attachShader(program, vertexShader);
				checkError();

				gl.attachShader(program, fragmentShader);
				checkError();

				gl.linkProgram(program);
				if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
					var err = gl.getProgramInfoLog(program);
					gl.deleteProgram(program);
					throw new Error('GL program linking failed: ' + err);
				}

				gl.useProgram(program);
				checkError();

				if (WebGLFrameSink.stripe) {
					attachTexture(
						'uStripeLuma',
						gl.TEXTURE3,
						3,
						buffer.y.stride * 4,
						buffer.format.height,
						buildStripe(buffer.y.stride, buffer.format.height)
					);
					checkError();

					attachTexture(
						'uStripeChroma',
						gl.TEXTURE4,
						4,
						buffer.u.stride * 4,
						buffer.format.chromaHeight,
						buildStripe(buffer.u.stride, buffer.format.chromaHeight)
					);
					checkError();
				}
			}

			/**
			 * Actually draw a frame.
			 * @param {YUVFrame} buffer - YUV frame buffer object
			 */
			self.drawFrame = function(buffer) {
				var format = buffer.format;

				if (canvas.width !== format.displayWidth || canvas.height !== format.displayHeight) {
					// Keep the canvas at the right size...
					canvas.width = format.displayWidth;
					canvas.height = format.displayHeight;
					self.clear();
				}

				if (!program) {
					init(buffer);
				}

				// Set up the rectangle and draw it

				//
				// Set up geometry
				//

				buf = gl.createBuffer();
				checkError();

				gl.bindBuffer(gl.ARRAY_BUFFER, buf);
				checkError();

				gl.bufferData(gl.ARRAY_BUFFER, rectangle, gl.STATIC_DRAW);
				checkError();

				var positionLocation = gl.getAttribLocation(program, 'aPosition');
				checkError();

				gl.enableVertexAttribArray(positionLocation);
				checkError();

				gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
				checkError();


				// Set up the texture geometry...
				function setupTexturePosition(varname, texWidth) {
					// Warning: assumes that the stride for Cb and Cr is the same size in output pixels
					var textureX0 = format.cropLeft / texWidth;
					var textureX1 = (format.cropLeft + format.cropWidth) / texWidth;
					var textureY0 = (format.cropTop + format.cropHeight) / format.height;
					var textureY1 = format.cropTop / format.height;
					var textureRectangle = new Float32Array([
						textureX0, textureY0,
						textureX1, textureY0,
						textureX0, textureY1,
						textureX0, textureY1,
						textureX1, textureY0,
						textureX1, textureY1
					]);

					var texturePositionBuffer = gl.createBuffer();
					gl.bindBuffer(gl.ARRAY_BUFFER, texturePositionBuffer);
					checkError();

					gl.bufferData(gl.ARRAY_BUFFER, textureRectangle, gl.STATIC_DRAW);
					checkError();

					var texturePositionLocation = gl.getAttribLocation(program, varname);
					checkError();

					gl.enableVertexAttribArray(texturePositionLocation);
					checkError();

					gl.vertexAttribPointer(texturePositionLocation, 2, gl.FLOAT, false, 0, 0);
					checkError();
				}
				setupTexturePosition('aLumaPosition', buffer.y.stride);
				setupTexturePosition('aChromaPosition', buffer.u.stride * format.width / format.chromaWidth);

				// Create the textures...
				var textureY = attachTexture(
					'uTextureY',
					gl.TEXTURE0,
					0,
					buffer.y.stride,
					format.height,
					buffer.y.bytes
				);
				var textureCb = attachTexture(
					'uTextureCb',
					gl.TEXTURE1,
					1,
					buffer.u.stride,
					format.chromaHeight,
					buffer.u.bytes
				);
				var textureCr = attachTexture(
					'uTextureCr',
					gl.TEXTURE2,
					2,
					buffer.v.stride,
					format.chromaHeight,
					buffer.v.bytes
				);

				// Aaaaand draw stuff.
				gl.drawArrays(gl.TRIANGLES, 0, rectangle.length / 2);
				checkError();
			};

			self.clear = function() {
				gl.viewport(0, 0, canvas.width, canvas.height);
				gl.clearColor(0.0, 0.0, 0.0, 0.0);
				gl.clear(gl.COLOR_BUFFER_BIT);
			};

			self.clear();

			return self;
		}

		// For Windows; luminance and alpha textures are ssllooww to upload,
		// so we pack into RGBA and unpack in the shaders.
		//
		// This seems to affect all browsers on Windows, probably due to fun
		// mismatches between GL and D3D.
		WebGLFrameSink.stripe = (function() {
			if (navigator.userAgent.indexOf('Windows') !== -1) {
				return true;
			}
			return false;
		})();

		/**
		 * Static function to check if WebGL will be available with appropriate features.
		 *
		 * @returns {boolean} - true if available
		 */
		WebGLFrameSink.isAvailable = function() {
			var canvas = document.createElement('canvas'),
				gl;
			canvas.width = 1;
			canvas.height = 1;
			var options = {
				// Still dithering on whether to use this.
				// Recommend avoiding it, as it's overly conservative
				//failIfMajorPerformanceCaveat: true
			};
			try {
				gl = canvas.getContext('webgl', options) || canvas.getContext('experimental-webgl', options);
			} catch (e) {
				return false;
			}
			if (gl) {
				var register = gl.TEXTURE0,
					width = 4,
					height = 4,
					texture = gl.createTexture(),
					data = new Uint8Array(width * height),
					texWidth = WebGLFrameSink.stripe ? (width / 4) : width,
					format = WebGLFrameSink.stripe ? gl.RGBA : gl.LUMINANCE,
					filter = WebGLFrameSink.stripe ? gl.NEAREST : gl.LINEAR;

				gl.activeTexture(register);
				gl.bindTexture(gl.TEXTURE_2D, texture);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, filter);
				gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, filter);
				gl.texImage2D(
					gl.TEXTURE_2D,
					0, // mip level
					format, // internal format
					texWidth,
					height,
					0, // border
					format, // format
					gl.UNSIGNED_BYTE, //type
					data // data!
				);

				var err = gl.getError();
				if (err) {
					// Doesn't support luminance textures?
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		};

		WebGLFrameSink.prototype = Object.create(FrameSink.prototype);

		module.exports = WebGLFrameSink;
	})();


/***/ },
/* 15 */
/***/ function(module, exports) {

	module.exports = {
	  vertex: "attribute vec2 aPosition;\nattribute vec2 aLumaPosition;\nattribute vec2 aChromaPosition;\nvarying vec2 vLumaPosition;\nvarying vec2 vChromaPosition;\nvoid main() {\n    gl_Position = vec4(aPosition, 0, 1);\n    vLumaPosition = aLumaPosition;\n    vChromaPosition = aChromaPosition;\n}\n",
	  fragment: "// inspired by https://github.com/mbebenita/Broadway/blob/master/Player/canvas.js\n\nprecision mediump float;\nuniform sampler2D uTextureY;\nuniform sampler2D uTextureCb;\nuniform sampler2D uTextureCr;\nvarying vec2 vLumaPosition;\nvarying vec2 vChromaPosition;\nvoid main() {\n   // Y, Cb, and Cr planes are uploaded as LUMINANCE textures.\n   float fY = texture2D(uTextureY, vLumaPosition).x;\n   float fCb = texture2D(uTextureCb, vChromaPosition).x;\n   float fCr = texture2D(uTextureCr, vChromaPosition).x;\n\n   // Premultipy the Y...\n   float fYmul = fY * 1.1643828125;\n\n   // And convert that to RGB!\n   gl_FragColor = vec4(\n     fYmul + 1.59602734375 * fCr - 0.87078515625,\n     fYmul - 0.39176171875 * fCb - 0.81296875 * fCr + 0.52959375,\n     fYmul + 2.017234375   * fCb - 1.081390625,\n     1\n   );\n}\n",
	  fragmentStripe: "// inspired by https://github.com/mbebenita/Broadway/blob/master/Player/canvas.js\n// extra 'stripe' texture fiddling to work around IE 11's poor performance on gl.LUMINANCE and gl.ALPHA textures\n\nprecision mediump float;\nuniform sampler2D uStripeLuma;\nuniform sampler2D uStripeChroma;\nuniform sampler2D uTextureY;\nuniform sampler2D uTextureCb;\nuniform sampler2D uTextureCr;\nvarying vec2 vLumaPosition;\nvarying vec2 vChromaPosition;\nvoid main() {\n   // Y, Cb, and Cr planes are mapped into a pseudo-RGBA texture\n   // so we can upload them without expanding the bytes on IE 11\n   // which doesn\\'t allow LUMINANCE or ALPHA textures.\n   // The stripe textures mark which channel to keep for each pixel.\n   vec4 vStripeLuma = texture2D(uStripeLuma, vLumaPosition);\n   vec4 vStripeChroma = texture2D(uStripeChroma, vChromaPosition);\n\n   // Each texture extraction will contain the relevant value in one\n   // channel only.\n   vec4 vY = texture2D(uTextureY, vLumaPosition) * vStripeLuma;\n   vec4 vCb = texture2D(uTextureCb, vChromaPosition) * vStripeChroma;\n   vec4 vCr = texture2D(uTextureCr, vChromaPosition) * vStripeChroma;\n\n   // Now assemble that into a YUV vector, and premultipy the Y...\n   vec3 YUV = vec3(\n     (vY.x  + vY.y  + vY.z  + vY.w) * 1.1643828125,\n     (vCb.x + vCb.y + vCb.z + vCb.w),\n     (vCr.x + vCr.y + vCr.z + vCr.w)\n   );\n   // And convert that to RGB!\n   gl_FragColor = vec4(\n     YUV.x + 1.59602734375 * YUV.z - 0.87078515625,\n     YUV.x - 0.39176171875 * YUV.y - 0.81296875 * YUV.z + 0.52959375,\n     YUV.x + 2.017234375   * YUV.y - 1.081390625,\n     1\n   );\n}\n"
	};


/***/ },
/* 16 */
/***/ function(module, exports) {

	/**
	 * Quickie wrapper around XHR to fetch a file as array buffer chunks.
	 *
	 * Call streamFile.readBytes() after an onread event's data has been
	 * processed to trigger the next read.
	 *
	 * IE 10: uses MSStream / MSStreamReader for true streaming
	 * Firefox: uses moz-chunked-arraybuffer to buffer & deliver during download
	 * Safari, Chrome: uses binary string to buffer & deliver during download
	 *
	 * @author Brion Vibber <brion@pobox.com>
	 * @copyright 2014
	 * @license MIT-style
	 */
	function StreamFile(options) {
		var self = this,
			url = options.url,
			started = false,
			onstart = options.onstart || function(){},
			onbuffer = options.onbuffer || function(){},
			onread = options.onread || function(){},
			ondone = options.ondone || function(){},
			onerror = options.onerror || function(){},
			bufferSize = options.bufferSize || 8192,
			seekPosition = options.seekPosition || 0,
			bufferPosition = seekPosition,
			chunkSize = options.chunkSize || 1024 * 1024, // read/buffer up to a megabyte at a time
			userAgent = navigator ? navigator.userAgent : '',
			useMSStream = options.useMSStream || !!userAgent.match(/MSIE 10\./),
			waitingForInput = false,
			doneBuffering = false,
			bytesTotal = 0,
			buffers = [],
			cachever = 0,
			responseHeaders = {};

		// Wrapper for array buffers, to allow extending backing store
		function BufferWrapper(buffer) {
			this.byteLength = buffer.byteLength;
			this.buffer = buffer;
		}
		BufferWrapper.prototype.getBuffer = function() {
			return this.buffer;
		};
		BufferWrapper.prototype.split = function(position, callback) {
			var a = new BufferWrapper(this.buffer.slice(0, position)),
				b = new BufferWrapper(this.buffer.slice(position));
			callback(a, b);
		};

		// -- internal private methods
		var internal = {
			/**
			 * @var {XMLHttpRequest}
			 */
			xhr: null,

			/**
			 * Test if a given responseType value is valid on an XHR
			 *
			 * @return boolean
			 */
			tryMethod: function(rt) {
				var xhr = new XMLHttpRequest();
				xhr.open("GET", url);
				try {
					// Set the response type and see if it explodes!
					xhr.responseType = rt;
				} catch (e) {
					// Safari 6 throws a DOM Exception on invalid setting
					return false;
				}
				// Other browsers just don't accept the setting, so check
				// whether it made it through.
				return (xhr.responseType == rt);
			},

			setBytesTotal: function(xhr) {
				if (xhr.status == 206) {
					bytesTotal = internal.getXHRRangeTotal(xhr);
				} else {
					var contentLength = xhr.getResponseHeader('Content-Length');
					if (contentLength === null || contentLength === '') {
						// Unknown file length... maybe streaming live?
						bytesTotal = 0;
					} else {
						bytesTotal = parseInt(contentLength, 10);
					}
				}
			},

			// Save HTTP response headers from the HEAD request for later
			processResponseHeaders: function(xhr) {
				responseHeaders = {};
				var allResponseHeaders = xhr.getAllResponseHeaders(),
					headerLines = allResponseHeaders.split(/\n/);
				headerLines.forEach(function(line) {
					var bits = line.split(/:\s*/, 2);
					if (bits.length > 1) {
						var name = bits[0].toLowerCase(),
							value = bits[1];
						responseHeaders[name] = value;
					}
				});
			},

			openXHR: function() {
				var getUrl = url;
				if (cachever) {
					//
					// Safari sometimes messes up and gives us the wrong chunk.
					// Seems to be a general problem with Safari and cached XHR ranges.
					//
					// Interestingly, it allows you to request _later_ ranges successfully,
					// but when requesting _earlier_ ranges it returns the latest one retrieved.
					// So we only need to update the cache-buster when we rewind.
					//
					// https://bugs.webkit.org/show_bug.cgi?id=82672
					//
					getUrl += '?ogvjs_cachever=' + cachever;
				}

				var xhr = internal.xhr = new XMLHttpRequest();
				xhr.open("GET", getUrl);

				xhr.streamReaderStarted = false;
				internal.setXHROptions(xhr);

				var range = null;
				if (seekPosition || chunkSize) {
					range = 'bytes=' + seekPosition + '-';
				}
				if (chunkSize) {
					if (bytesTotal) {
						range += Math.min(seekPosition + chunkSize, bytesTotal) - 1;
					} else {
						range += (seekPosition + chunkSize) - 1;
					}
				}
				if (range !== null) {
					xhr.setRequestHeader('Range', range);
				}

				xhr.onprogress = function(event) {
					if (xhr.readyState >= 2 && !xhr.streamReaderStarted) {
						internal.onXHRStart(xhr, event);
					} else if (xhr.readyState >= 3) {
						internal.onXHRLoading(xhr, event);
					}
				};

				xhr.onloadend = function(event) {
					internal.onXHRDone(xhr, event);
				}

				xhr.send();
			},

			getXHRRangeMatches: function(xhr) {
				// Note Content-Range must be whitelisted for CORS requests
				var contentRange = xhr.getResponseHeader('Content-Range');
				return contentRange && contentRange.match(/^bytes (\d+)-(\d+)\/(\d+)/);
			},

			getXHRRangeStart: function(xhr) {
				var matches = internal.getXHRRangeMatches(xhr);
				if (matches) {
					return parseInt(matches[1], 10);
				} else {
					return 0;
				}
			},

			getXHRRangeTotal: function(xhr) {
				var matches = internal.getXHRRangeMatches(xhr);
				if (matches) {
					return parseInt(matches[3], 10);
				} else {
					return 0;
				}
			},

			setXHROptions: function(xhr) {
				throw new Error('abstract function');
			},

			onXHRStart: function(xhr, event) {
				xhr.streamReaderStarted = true;
				//console.log('status is ' + xhr.status + '; content range is ' + xhr.getResponseHeader('Content-Range') + ' (start at ' + seekPosition + ')');
				if (xhr.status == 206) {
					var foundPosition = internal.getXHRRangeStart(xhr);
					if (seekPosition != foundPosition) {
						//
						// Safari sometimes messes up and gives us the wrong chunk.
						// Seems to be a general problem with Safari and cached XHR ranges.
						//
						// Interestingly, it allows you to request _later_ ranges successfully,
						// but when requesting _earlier_ ranges it returns the latest one retrieved.
						// So we only need to update the cache-buster when we rewind and actually
						// get an incorrect range.
						//
						// https://bugs.webkit.org/show_bug.cgi?id=82672
						//
						console.log('Expected start at ' + seekPosition + ' but got ' + foundPosition +
							'; working around Safari range caching bug (' + cachever + '): https://bugs.webkit.org/show_bug.cgi?id=82672');
						cachever++;
						internal.abortXHR(xhr);
						internal.openXHR();
						return;
					}
				}
				if (seekPosition && xhr.status != 206) {
					var code = xhr.status;
					internal.abortXHR(xhr);
					onerror('HTTP seek failed; unexpected status code ' + code);
					return;
				}
				if (xhr.status >= 400) {
					onerror('HTTP error; status code ' + xhr.status);
					return;
				}
				if (!started) {
					internal.setBytesTotal(xhr);
					internal.processResponseHeaders(xhr);
					started = true;
					onstart();
				}
				if (xhr.readyState >= 3) {
					internal.onXHRLoading(xhr, event);
				}
				//internal.onXHRHeadersReceived(xhr);
				// @todo check that partial content was supported if relevant
			},

			onXHRLoading: function(xhr, event) {
				throw new Error('abstract function');
			},

			onXHRDone: function(xhr, event) {
				doneBuffering = true;
				if (waitingForInput && !internal.dataToRead()) {
					if (internal.advance()) {
						return;
					}
				}
				internal.onXHRLoading(xhr, event);
			},

			abortXHR: function(xhr) {
				// These events do get called after abort.
				// Let's not and say we did?
				xhr.onprogress = null;
				xhr.onloadend = null;
				xhr.abort();
			},

			advance: function() {
				if (doneBuffering &&
					self.bytesBuffered - self.bytesRead < chunkSize &&
					seekPosition + chunkSize < self.bytesTotal
				) {
					seekPosition += chunkSize;
					internal.clearReadState();
					internal.openXHR();
					return true;
				} else {
					return false;
				}
			},

			bufferData: function(buffer) {
				if (buffer) {
					buffers.push(buffer);
					onbuffer();

					internal.readNextChunk();
				}
			},

			bytesBuffered: function() {
				var bytes = 0;
				buffers.forEach(function(buffer) {
					bytes += buffer.byteLength;
				});
				return bytes;
			},

			dataToRead: function() {
				return internal.bytesBuffered() > 0;
			},

			popBuffer: function() {
				var bufferOut = new ArrayBuffer(bufferSize),
					bytesOut = new Uint8Array(bufferOut),
					byteLength = 0;

				function stuff(bufferIn) {
					var bytesIn = new Uint8Array(bufferIn);
					bytesOut.set(bytesIn, byteLength);
					byteLength += bufferIn.byteLength;
				}

				var min = bufferSize;
				while (byteLength < min) {
					var needBytes = min - byteLength,
						nextBuffer = buffers.shift();
					if (!nextBuffer) {
						break;
					}

					if (needBytes >= nextBuffer.byteLength) {
						// if it fits, it sits
						stuff(nextBuffer.getBuffer());
					} else {
						// Split the buffer and requeue the rest
						nextBuffer.split(needBytes, function(croppedBuffer, remainderBuffer) {
							buffers.unshift(remainderBuffer);
							stuff(croppedBuffer.getBuffer());
						});
						break;
					}
				}

				bufferPosition += byteLength;
				return bufferOut.slice(0, byteLength);
			},

			clearReadState: function() {
				doneBuffering = false;
			},

			clearBuffers: function() {
				internal.clearReadState();
				buffers.splice(0, buffers.length);
				bufferPosition = seekPosition;
			},

			// Read the next binary buffer out of the buffered data
			readNextChunk: function() {
				if (waitingForInput) {
					waitingForInput = false;
					onread(internal.popBuffer());
				}
			},

			onReadDone: function() {
				ondone();
			},

			// See if we can seek within already-buffered data
			quickSeek: function(pos) {
				return false;
			}

		};

		// -- Public methods
		self.readBytes = function() {
			if (waitingForInput) {
				throw new Error('StreamFile re-entrancy fail; readBytes called while waiting for data');
			}
			if (internal.dataToRead()) {
				var buffer = internal.popBuffer();
				internal.advance();
				onread(buffer);
			} else if (doneBuffering) {
				// We're out of data!
				if (!internal.advance()) {
					internal.onReadDone();
				}
			} else {
				// Nothing queued...
				waitingForInput = true;
			}
		};

		self.abort = function() {
			if (internal.xhr) {
				internal.abortXHR(internal.xhr);
				internal.xhr = null;
			}
			internal.clearBuffers();
			waitingForInput = false;
		};

		self.seek = function(bytePosition) {
			if (internal.quickSeek(bytePosition)) {
				//console.log('quick seek successful');
			} else {
				self.abort();
				seekPosition = bytePosition;
				internal.clearBuffers();
				internal.openXHR();
			}
		};

		self.getResponseHeader = function(headerName) {
			var lowerName = headerName.toLowerCase(),
				value = responseHeaders[lowerName];
			if (value === undefined) {
				return null;
			} else {
				return value;
			}
		};

		// -- public properties
		Object.defineProperty(self, 'bytesTotal', {
			get: function() {
				return bytesTotal;
			}
		});

		Object.defineProperty(self, 'bytesBuffered', {
			get: function() {
				return bufferPosition + internal.bytesBuffered();
			}
		});

		Object.defineProperty(self, 'bytesRead', {
			get: function() {
				return bufferPosition;
			}
		});

		Object.defineProperty(self, 'seekable', {
			get: function() {
				return (self.bytesTotal > 0);
			}
		});

		Object.defineProperty(self, 'waiting', {
			get: function() {
				return waitingForInput;
			}
		});

		// Handy way to call super functions
		var orig = {};
		for (var prop in internal) {
			orig[prop] = internal[prop];
		}

		// -- Backend selection and method overrides
		if (internal.tryMethod('moz-chunked-arraybuffer')) {
			internal.setXHROptions = function(xhr) {
				xhr.responseType = 'moz-chunked-arraybuffer';
			};

			internal.onXHRLoading = function(xhr, event) {
				// we have to get from the 'progress' event
				// xhr.response is a per-chunk ArrayBuffer
				if (xhr.response) {
					internal.bufferData(new BufferWrapper(xhr.response));
				}
			};

		} else if (useMSStream && internal.tryMethod('ms-stream')) {
			// IE 10 supports returning a Stream from XHR.
			// This seems unreliable in practice as the connections tend to die
			// unexpectedly; recommend using the chunking even if it's primitive.

			// Don't bother reading in chunks, MSStream handles it for us
			chunkSize = 0;

			var stream, streamReader;
			var restarted = false;

			internal.setXHROptions = function(xhr) {
				xhr.responseType = 'ms-stream';
				// onprogress doesn't get fired with ms-stream?
				xhr.onreadystatechange = function(event) {
					if (xhr.readyState >= 2 && !xhr.streamReaderStarted) {
						internal.onXHRStart(xhr, event);
					} else if (xhr.readyState >= 3) {
						internal.onXHRLoading(xhr, event);
					}
				}
			};

			internal.abortXHR = function(xhr) {
				restarted = true;
				if (streamReader) {
					streamReader.abort();
					streamReader = null;
				}
				if (stream) {
					stream.msClose();
					stream = null;
				}
				orig.abortXHR(xhr);
			};

			internal.onXHRLoading = function(xhr, event) {
				// Transfer us over to the StreamReader...
				stream = xhr.response;
				xhr.onprogress = null;
				xhr.onloadend = null;
				xhr.onreadystatechange = null;
				if (waitingForInput) {
					waitingForInput = false;
					self.readBytes();
				}
			};

			internal.bytesBuffered = function() {
				// We don't know how much ahead is buffered, it's opaque.
				// Just return what we've read.
				return 0;
			};

			self.readBytes = function() {
				if (waitingForInput) {
					throw new Error('StreamFile re-entrancy fail; readBytes called while waiting for data');
				}
				if (stream) {
					// Save the current position in case the stream died
					// and we have to restart it...
					var currentPosition = self.bytesRead;

					var reader = streamReader = new MSStreamReader();
					reader.onload = function(event) {
						var buffer = event.target.result,
							len = buffer.byteLength;
						if (len > 0) {
							bufferPosition += len;
							onread(buffer);
						} else {
							// Zero length means end of stream.
							ondone();
						}
					};
					reader.onerror = function(event) {
						console.log('MSStreamReader error: ' + reader.error + '; trying to recover');
						self.seek(currentPosition);
						self.readBytes();
					};
					streamReader.readAsArrayBuffer(stream, bufferSize);
				} else {
					waitingForInput = true;
				}
			};

		} else if ((new XMLHttpRequest()).overrideMimeType !== undefined) {

			// Use old binary string method since we can read reponseText
			// progressively and extract ArrayBuffers from that.

			internal.setXHROptions = function(xhr) {
				xhr.responseType = "text";
				xhr.overrideMimeType('text/plain; charset=x-user-defined');
				xhr.streamReaderLastPosition = 0;
			};

			// Is there a better way to do this conversion? :(
			var stringToArrayBuffer = function(chunk) {
				var len = chunk.length,
					buffer = new ArrayBuffer(len),
					bytes = new Uint8Array(buffer);
				for (var i = 0; i < len; i++) {
					bytes[i] = chunk.charCodeAt(i);
				}
				return buffer;
			};

			// Wrapper for buffers, to let us lazy-extract the binary data
			function StringBufferWrapper(xhr, start, end) {
				this.start = start;
				this.end = end;
				this.byteLength = end - start;
				this.xhr = xhr;
			}
			StringBufferWrapper.prototype.getBuffer = function() {
				var str = this.xhr.responseText,
					chunk = str.slice(this.start, this.end),
					buffer = stringToArrayBuffer(chunk);
				return buffer;
			};
			StringBufferWrapper.prototype.split = function(position, callback) {
				var a = new StringBufferWrapper(this.xhr, this.start, this.start + position),
					b = new StringBufferWrapper(this.xhr, this.start + position, this.end)
				callback(a, b);
			};

			internal.onXHRLoading = function(xhr, event) {
				var position;
				if (typeof event.loaded === 'number') {
					position = event.loaded;
				} else {
					// xhr.responseText is a binary string of entire file so far
					position = xhr.responseText.length;
				}
				if (xhr.streamReaderLastPosition < position) {
					var buffer = new StringBufferWrapper(xhr, xhr.streamReaderLastPosition, position);
					xhr.streamReaderLastPosition = position;
					internal.bufferData(buffer);
				}
			};

			/*
			internal.quickSeek = function(pos) {
				var bufferedPos = pos - seekPosition;
				if (bufferedPos < 0) {
					return false;
				} else if (bufferedPos >= internal.xhr.responseText.length) {
					return false;
				} else {
					lastPosition = bufferedPos;
					bytesRead = lastPosition;
					setTimeout(function() {
						onbuffer()
					}, 0);
					return true;
				}
			};
			*/
		} else {
			throw new Error("No streaming HTTP input method found.");
		}

		internal.openXHR();
	}

	module.exports = StreamFile;


/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	(function webpackUniversalModuleDefinition(root, factory) {
		if(true)
			module.exports = factory();
		else if(typeof define === 'function' && define.amd)
			define([], factory);
		else if(typeof exports === 'object')
			exports["AudioFeeder"] = factory();
		else
			root["AudioFeeder"] = factory();
	})(this, function() {
	return /******/ (function(modules) { // webpackBootstrap
	/******/ 	// The module cache
	/******/ 	var installedModules = {};

	/******/ 	// The require function
	/******/ 	function __webpack_require__(moduleId) {

	/******/ 		// Check if module is in cache
	/******/ 		if(installedModules[moduleId])
	/******/ 			return installedModules[moduleId].exports;

	/******/ 		// Create a new module (and put it into the cache)
	/******/ 		var module = installedModules[moduleId] = {
	/******/ 			exports: {},
	/******/ 			id: moduleId,
	/******/ 			loaded: false
	/******/ 		};

	/******/ 		// Execute the module function
	/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

	/******/ 		// Flag the module as loaded
	/******/ 		module.loaded = true;

	/******/ 		// Return the exports of the module
	/******/ 		return module.exports;
	/******/ 	}


	/******/ 	// expose the modules object (__webpack_modules__)
	/******/ 	__webpack_require__.m = modules;

	/******/ 	// expose the module cache
	/******/ 	__webpack_require__.c = installedModules;

	/******/ 	// __webpack_public_path__
	/******/ 	__webpack_require__.p = "";

	/******/ 	// Load entry module and return exports
	/******/ 	return __webpack_require__(0);
	/******/ })
	/************************************************************************/
	/******/ ([
	/* 0 */
	/***/ function(module, exports, __webpack_require__) {

		(function() {

			var BufferQueue = __webpack_require__(1),
				WebAudioBackend = __webpack_require__(2),
				FlashBackend = __webpack_require__(4);


			/**
			 * Audio sample buffer format passed to {@link AudioFeeder#bufferData} and its backends.
			 *
			 * Buffers are arrays containing one Float32Array of sample data
			 * per channel. Channel counts must match the expected value, and
			 * all channels within a buffer must have the same length in samples.
			 *
			 * Since input data may be stored for a while before being taken
			 * back out, be sure that your Float32Arrays for channel data are
			 * standalone, not backed on an ArrayBuffer that might change!
			 *
			 * @typedef {SampleBuffer} SampleBuffer
			 * @todo consider replacing this with AudioBuffer-like wrapper object
			 */

			/**
			 * Object dictionary format used to pass options into {@link AudioFeeder} and its backends.
			 *
			 * @typedef {Object} AudioFeederOptions
			 * @property {string} base - (optional) base URL to find additional resources in,
			 *                           such as the Flash audio output shim
			 * @property {AudioContext} audioContext - (optional) Web Audio API AudioContext
			 *                          instance to use inplace of creating a default one
			 */

			/**
			 * Object dictionary format returned from {@link AudioFeeder#getPlaybackState} and friends.
			 *
			 * @typedef {Object} PlaybackState
			 * @property {number} playbackPosition - total seconds so far of audio data that have played
			 * @property {number} samplesQueued - number of samples at target rate that are queued up for playback
			 * @property {number} dropped - number of underrun events, when we had to play silence due to data starvation
			 * @property {number} delayedTime - total seconds so far of silent time during playback due to data starvation
			 * @todo drop 'dropped' in favor of delayedTime
			 * @todo replace sampledQueued with a time unit?
			 */

			/**
			 * Object that we can throw audio data into and have it drain out.
			 * @class
			 * @param {AudioFeederOptions} options - dictionary of config settings
			 *
			 * @classdesc
			 * Object that we can throw audio data into and have it drain out.
			 */
			function AudioFeeder(options) {
				this._options = options || {};
				this._backend = null; // AudioBackend instance, after init...
			};

			/**
			 * Sample rate in Hz, as requested by the caller in {@link AudioFeeder#init}.
			 *
			 * If the backend's actual sample rate differs from this requested rate,
			 * input data will be resampled automatically.
			 *
			 * @type {number}
			 * @readonly
			 * @see AudioFeeder#targetRate
			 */
			AudioFeeder.prototype.rate = 0;

			/**
			 * Actual output sample rate in Hz, as provided by the backend.
			 * This may differ from the rate requested, in which case input data
			 * will be resampled automatically.
			 *
			 * @type {number}
			 * @readonly
			 * @see AudioFeeder#rate
			 */
			AudioFeeder.prototype.targetRate = 0;

			/**
			 * Number of output channels, as requested by the caller in {@link AudioFeeder#init}.
			 *
			 * If the backend's actual channel count differs from this requested count,
			 * input data will be resampled automatically.
			 *
			 * Warning: currently more than 2 channels may result in additional channels
			 * being cropped out, as downmixing has not yet been implemented.
			 *
			 * @type {number}
			 * @readonly
			 */
			AudioFeeder.prototype.channels = 0;

			/**
			 * Size of output buffers in samples, as a hint for latency/scheduling
			 * @type {number}
			 * @readonly
			 */
			AudioFeeder.prototype.bufferSize = 0;

			/**
			 * Duration of the minimum buffer size, in seconds.
			 * If the amount of buffered data falls below this,
			 * caller will receive a synchronous 'starved' event
			 * with a last chance to buffer more data.
			 *
			 * @type {number}
			 * @readonly
			 */
			Object.defineProperty(AudioFeeder.prototype, 'bufferDuration', {
				get: function getBufferDuration() {
					if (this.targetRate) {
						return this.bufferSize / this.targetRate;
					} else {
						return 0;
					}
				}
			});

			/**
			 * Duration of remaining data at which a 'bufferlow' event will be
			 * triggered, in seconds.
			 *
			 * This defaults to twice bufferDuration, but can be overridden.
			 *
			 * @type {number}
			 */
			Object.defineProperty(AudioFeeder.prototype, 'bufferThreshold', {
				get: function getBufferThreshold() {
					if (this._backend) {
						return this._backend.bufferThreshold / this.targetRate;
					} else {
						return 0;
					}
				},
				set: function setBufferThreshold(val) {
					if (this._backend) {
						this._backend.bufferThreshold = Math.round(val * this.targetRate);
					} else {
						throw 'Invalid state: AudioFeeder cannot set bufferThreshold before init';
					}
				}
			});

			/**
			 * Current playback position, in seconds.
			 * This compensates for drops and underruns, and is suitable for a/v sync.
			 *
			 * @type {number}
			 * @readonly
			 */
			Object.defineProperty(AudioFeeder.prototype, 'playbackPosition', {
				get: function getPlaybackPosition() {
					if (this._backend) {
						var playbackState = this.getPlaybackState();
						return playbackState.playbackPosition;
					} else {
						return 0;
					}
				}
			});

			/**
			 * Duration of remaining queued data, in seconds.
			 *
			 * @type {number}
			 */
			Object.defineProperty(AudioFeeder.prototype, 'durationBuffered', {
				get: function getDurationBuffered() {
					if (this._backend) {
						var playbackState = this.getPlaybackState();
						return playbackState.samplesQueued / this.targetRate;
					} else {
						return 0;
					}
				}
			});

			/**
			 * Is the feeder currently set to mute output?
			 * When muted, this overrides the volume property.
			 *
			 * @type {boolean}
			 */
			Object.defineProperty(AudioFeeder.prototype, 'muted', {
		 		get: function getMuted() {
					if (this._backend) {
						return this._backend.muted;
					} else {
						throw 'Invalid state: cannot get mute before init';
					}
		 		},
		 		set: function setMuted(val) {
					if (this._backend) {
						this._backend.muted = val;
					} else {
						throw 'Invalid state: cannot set mute before init';
					}
		 		}
		 	});

			/**
			 * @deprecated in favor of muted and volume properties
			 */
			AudioFeeder.prototype.mute = function() {
				this.muted = true;
			};

			/**
			* @deprecated in favor of muted and volume properties
			 */
			AudioFeeder.prototype.unmute = function() {
				this.muted = false;
			};

			/**
			 * Volume multiplier, defaults to 1.0.
			 * @name volume
			 * @type {number}
			 */
			Object.defineProperty(AudioFeeder.prototype, 'volume', {
				get: function getVolume() {
					if (this._backend) {
						return this._backend.volume;
					} else {
						throw 'Invalid state: cannot get volume before init';
					}
				},
				set: function setVolume(val) {
					if (this._backend) {
						this._backend.volume = val;
					} else {
						throw 'Invalid state: cannot set volume before init';
					}
				}
			});

			/**
			 * Start setting up for output with the given channel count and sample rate.
			 * Audio data you provide will be resampled if necessary to whatever the
			 * backend actually supports.
			 *
			 * @param {number} numChannels - requested number of channels (output may differ)
			 * @param {number} sampleRate - requested sample rate in Hz (output may differ)
			 *
			 * @todo merge into constructor?
			 */
			AudioFeeder.prototype.init = function(numChannels, sampleRate) {
				this.channels = numChannels;
				this.rate = sampleRate;

				if (WebAudioBackend.isSupported()) {
					this._backend = new WebAudioBackend(numChannels, sampleRate, this._options);
				} else if (FlashBackend.isSupported()) {
					this._backend = new FlashBackend(numChannels, sampleRate, this._options);
				} else {
					throw 'No supported backend';
				}

				this.targetRate = this._backend.rate;
				this.bufferSize = this._backend.bufferSize;

				// Pass through the starved event
				this._backend.onstarved = (function() {
					if (this.onstarved) {
						this.onstarved();
					}
				}).bind(this);
				this._backend.onbufferlow = (function() {
					if (this.onbufferlow) {
						this.onbufferlow();
					}
				}).bind(this);
			};

			/**
			 * Resample a buffer from the input rate/channel count to the output.
			 *
			 * This is horribly naive and wrong.
			 * Replace me with a better algo!
			 *
			 * @param {SampleBuffer} sampleData - input data in requested sample rate / channel count
			 * @returns {SampleBuffer} output data in backend's sample rate/channel count
			 */
			AudioFeeder.prototype._resample = function(sampleData) {
				var rate = this.rate,
					channels = this.channels,
					targetRate = this._backend.rate,
					targetChannels = this._backend.channels;

				if (rate == targetRate && channels == targetChannels) {
					return sampleData;
				} else {
					var newSamples = [];
					for (var channel = 0; channel < targetChannels; channel++) {
						var inputChannel = channel;
						if (channel >= channels) {
							// Flash forces output to stereo; if input is mono, dupe the first channel
							inputChannel = 0;
						}
						var input = sampleData[inputChannel],
							output = new Float32Array(Math.round(input.length * targetRate / rate));
						for (var i = 0; i < output.length; i++) {
							output[i] = input[(i * rate / targetRate) | 0];
						}
						newSamples.push(output);
					}
					return newSamples;
				}
			};

			/**
			 * Queue up some audio data for playback.
			 *
			 * @param {SampleBuffer} sampleData - input data to queue up for playback
			 *
			 * @todo throw if data invalid or uneven
			 */
			AudioFeeder.prototype.bufferData = function(sampleData) {
				if (this._backend) {
					var samples = this._resample(sampleData);
					this._backend.appendBuffer(samples);
				} else {
					throw 'Invalid state: AudioFeeder cannot bufferData before init';
				}
			};

			/**
			 * Get an object with information about the current playback state.
			 *
			 * @return {PlaybackState} - info about current playback state
			 */
			AudioFeeder.prototype.getPlaybackState = function() {
				if (this._backend) {
					return this._backend.getPlaybackState();
				} else {
					throw 'Invalid state: AudioFeeder cannot getPlaybackState before init';
				}
			};

			/**
			 * Checks if audio system is ready and calls the callback when ready
			 * to begin playback.
			 *
			 * This will wait for the Flash shim to load on IE 10/11; waiting
			 * is not required when using native Web Audio but you should use
			 * this callback to support older browsers.
			 *
			 * @param {function} callback - called when ready
			 */
			AudioFeeder.prototype.waitUntilReady = function(callback) {
				if (this._backend) {
					this._backend.waitUntilReady(callback);
				} else {
					throw 'Invalid state: AudioFeeder cannot waitUntilReady before init';
				}
			};

			/**
			 * Start/continue playback as soon as possible.
			 *
			 * You should buffer some audio ahead of time to avoid immediately
			 * running into starvation.
			 */
			AudioFeeder.prototype.start = function() {
				if (this._backend) {
					this._backend.start();
				} else {
					throw 'Invalid state: AudioFeeder cannot start before init';
				}
			};

			/**
			 * Stop/pause playback as soon as possible.
			 *
			 * Audio that has been buffered but not yet sent to the device will
			 * remain buffered, and can be continued with another call to start().
			 */
			AudioFeeder.prototype.stop = function() {
				if (this._backend) {
					this._backend.stop();
				} else {
					throw 'Invalid state: AudioFeeder cannot stop before init';
				}
			};

			/**
			 * Flush any queued data out of the system.
			 */
			AudioFeeder.prototype.flush = function() {
				if (this._backend) {
					this._backend.flush();
				} else {
					throw 'Invalid state: AudioFeeder cannot flush before init';
				}
			}

			/**
			 * Close out the audio channel. The AudioFeeder instance will no
			 * longer be usable after closing.
			 *
			 * @todo close out the AudioContext if no longer needed
			 * @todo make the instance respond more consistently once closed
			 */
			AudioFeeder.prototype.close = function() {
				if (this._backend) {
					this._backend.close();
					this._backend = null;
				}
			};

			/**
			 * Synchronous callback when we find we're out of buffered data.
			 *
			 * @type {function}
			 */
			AudioFeeder.prototype.onstarved = null;

			/**
			 * Asynchronous callback when we're running low on buffered data.
			 *
			 * @type {function}
			 */
			AudioFeeder.prototype.onbufferlow = null;

			/**
			 * Is the AudioFeeder class supported in this browser?
			 *
			 * Note that it's still possible to be supported but not work, for instance
			 * if there are no audio output devices but the APIs are available.
			 *
			 * @returns {boolean} - true if Web Audio API is available
			 */
			AudioFeeder.isSupported = function() {
				return !!Float32Array && (WebAudioBackend.isSupported() || FlashBackend.isSupported());
			};

			/**
			 * Force initialization of the default Web Audio API context, if applicable.
			 *
			 * Some browsers (such as mobile Safari) disable audio output unless
			 * first triggered from a UI event handler; call this method as a hint
			 * that you will be starting up an AudioFeeder soon but won't have data
			 * for it until a later callback.
			 *
			 * @returns {AudioContext|null} - initialized AudioContext instance, if applicable
			 */
			AudioFeeder.initSharedAudioContext = function() {
				if (WebAudioBackend.isSupported()) {
					return WebAudioBackend.initSharedAudioContext();
				} else {
					return null;
				}
			};

			module.exports = AudioFeeder;

		})();


	/***/ },
	/* 1 */
	/***/ function(module, exports) {

		/**
		 * @file Abstraction around a queue of audio buffers.
		 *
		 * @author Brion Vibber <brion@pobox.com>
		 * @copyright (c) 2013-2016 Brion Vibber
		 * @license MIT
		 */

		/**
		 * Constructor for BufferQueue class.
		 * @class
		 * @param {number} numChannels - channel count to validate against
		 * @param {number} bufferSize - desired size of pre-chunked output buffers, in samples
		 *
		 * @classdesc
		 * Abstraction around a queue of audio buffers.
		 *
		 * Stuff input buffers of any length in via {@link BufferQueue#appendBuffer},
		 * check how much is queued with {@link BufferQueue#sampleCount}, and pull out
		 * data in fixed-size chunks from the start with {@link BufferQueue#shift}.
		 */
		function BufferQueue(numChannels, bufferSize) {
		  if (numChannels < 1 || numChannels !== Math.round(numChannels)) {
		    throw 'Invalid channel count for BufferQueue';
		  }
		  this.channels = numChannels;
		  this.bufferSize = bufferSize;
		  this.flush();
		}

		/**
		 * Flush any data out of the queue, resetting to empty state.
		 */
		BufferQueue.prototype.flush = function() {
		  this._buffers = [];
		  this._pendingBuffer = this.createBuffer(this.bufferSize);
		  this._pendingPos = 0;
		};

		/**
		 * Count how many samples are queued up
		 *
		 * @returns {number} - total count in samples
		 */
		BufferQueue.prototype.sampleCount = function() {
		  var count = 0;
		  this._buffers.forEach(function(buffer) {
		    count += buffer[0].length;
		  });
		  return count;
		};

		/**
		 * Create an empty audio sample buffer with space for the given count of samples.
		 *
		 * @param {number} sampleCount - number of samples to reserve in the buffer
		 * @returns {SampleBuffer} - empty buffer
		 */
		BufferQueue.prototype.createBuffer = function(sampleCount) {
		  var output = [];
		  for (var i = 0; i < this.channels; i++) {
		    output[i] = new Float32Array(sampleCount);
		  }
		  return output;
		};

		/**
		 * Validate a buffer for correct object layout
		 *
		 * @param {SampleBuffer} buffer - an audio buffer to check
		 * @returns {boolean} - true if input buffer is valid
		 */
		BufferQueue.prototype.validate = function(buffer) {
		  if (buffer.length !== this.channels) {
		    return false;
		  }

		  var sampleCount;
		  for (var i = 0; i < buffer.length; i++) {
		    var channelData = buffer[i];
		    if (!(channelData instanceof Float32Array)) {
		      return false;
		    }
		    if (i == 0) {
		      sampleCount = channelData.length;
		    } else if (channelData.length !== sampleCount) {
		      return false;
		    }
		  }

		  return true;
		};

		/**
		 * Append a buffer of input data to the queue...
		 *
		 * @param {SampleBuffer} sampleData - an audio buffer to append
		 * @throws exception on invalid input
		 */
		BufferQueue.prototype.appendBuffer = function(sampleData) {
		  if (!this.validate(sampleData)) {
		    throw "Invalid audio buffer passed to BufferQueue.appendBuffer";
		  }

		  var firstChannel = sampleData[0],
		    sampleCount = firstChannel.length;

		  // @todo this seems hella inefficient
		  for (var i = 0; i < sampleCount; i++) {
		    for (var channel = 0; channel < this.channels; channel++) {
		      this._pendingBuffer[channel][this._pendingPos] = sampleData[channel][i];
		    }
		    if (++this._pendingPos == this.bufferSize) {
		      this._buffers.push(this._pendingBuffer);
		      this._pendingPos = 0;
		      this._pendingBuffer = this.createBuffer(this.bufferSize);
		    }
		  }

		};

		/**
		 * Unshift the given sample buffer onto the beginning of the buffer queue.
		 *
		 * @param {SampleBuffer} sampleData - an audio buffer to prepend
		 * @throws exception on invalid input
		 *
		 * @todo this is currently pretty inefficient as it rechunks all the buffers.
		 */
		BufferQueue.prototype.prependBuffer = function(sampleData) {
		  if (!this.validate(sampleData)) {
		    throw "Invalid audio buffer passed to BufferQueue.prependBuffer";
		  }

		  // Since everything is pre-chunked in the queue, we're going to have
		  // to pull everything out and re-append it.
		  var buffers = this._buffers.slice(0)
		  buffers.push(this.trimBuffer(this._pendingBuffer, 0, this._pendingPos));

		  this.flush();
		  this.appendBuffer(sampleData);

		  // Now put back any old buffers, dividing them up into chunks.
		  for (var i = 0; i < buffers.length; i++) {
		    this.appendBuffer(buffers[i]);
		  }
		};

		/**
		 * Shift out a buffer from the head of the queue, containing a maximum of
		 * {@link BufferQueue#bufferSize} samples; if there are not enough samples
		 * you may get a shorter buffer. Call {@link BufferQueue#sampleCount} to
		 * check if enough samples are available for your needs.
		 *
		 * @returns {SampleBuffer} - an audio buffer with zero or more samples
		 */
		BufferQueue.prototype.nextBuffer = function() {
		  if (this._buffers.length) {
		    return this._buffers.shift();
		  } else {
		    var trimmed = this.trimBuffer(this._pendingBuffer, 0, this._pendingPos);
		    this._pendingBuffer = this.createBuffer(this.bufferSize);
		    this._pendingPos = 0;
		    return trimmed;
		  }
		};

		/**
		 * Trim a buffer down to a given maximum sample count.
		 * Any additional samples will simply be cropped off of the view.
		 * If no trimming is required, the same buffer will be returned.
		 *
		 * @param {SampleBuffer} sampleData - input data
		 * @param {number} start - sample number to start at
		 * @param {number} maxSamples - count of samples to crop to
		 * @returns {SampleBuffer} - output data with at most maxSamples samples
		 */
		BufferQueue.prototype.trimBuffer = function(sampleData, start, maxSamples) {
		  var bufferLength = sampleData[0].length,
		    end = start + Math.min(maxSamples, bufferLength);
		  if (start == 0 && end >= bufferLength) {
		    return sampleData;
		  } else {
		    var output = [];
		    for (var i = 0; i < this.channels; i++) {
		      output[i] = sampleData[i].subarray(start, end);
		    }
		    return output;
		  }
		};

		module.exports = BufferQueue;


	/***/ },
	/* 2 */
	/***/ function(module, exports, __webpack_require__) {

		/**
		 * @file Web Audio API backend for AudioFeeder
		 * @author Brion Vibber <brion@pobox.com>
		 * @copyright (c) 2013-2016 Brion Vibber
		 * @license MIT
		 */

		(function() {

		  var AudioContext = window.AudioContext || window.webkitAudioContext,
		    BufferQueue = __webpack_require__(1),
		    nextTick = __webpack_require__(3);

		  /**
		   * Constructor for AudioFeeder's Web Audio API backend.
		   * @class
		   * @param {number} numChannels - requested count of output channels
		   * @param {number} sampleRate - requested sample rate for output
		   * @param {Object} options - pass URL path to directory containing 'dynamicaudio.swf' in 'base' parameter
		   *
		   * @classdesc Web Audio API output backend for AudioFeeder.
		   * Maintains an internal {@link BufferQueue} of audio samples to be output on demand.
		   */
		  function WebAudioBackend(numChannels, sampleRate, options) {
		    var context = options.audioContext || WebAudioBackend.initSharedAudioContext();

		    this._context = context;


		    /*
		     * Optional audio node can be provided to connect the feeder to
		     * @type {AudioNode}
		     */
		    this.output = options.output || context.destination

		    /**
		     * Actual sample rate supported for output, in Hz
		     * @type {number}
		     * @readonly
		     */
		    this.rate = context.sampleRate;

		    /**
		     * Actual count of channels supported for output
		     * @type {number}
		     * @readonly
		     */
		    this.channels = Math.min(numChannels, 2); // @fixme remove this limit

		    if (options.bufferSize) {
		        this.bufferSize = (options.bufferSize | 0);
		    }
		    this.bufferThreshold = 2 * this.bufferSize;

		    this._bufferQueue = new BufferQueue(this.channels, this.bufferSize);
		    this._playbackTimeAtBufferTail = context.currentTime;
		    this._queuedTime = 0;
		    this._delayedTime = 0;
		    this._dropped = 0;
		    this._liveBuffer = this._bufferQueue.createBuffer(this.bufferSize);

		    // @todo support new audio worker mode too
		    if (context.createScriptProcessor) {
		      this._node = context.createScriptProcessor(this.bufferSize, 0, this.channels);
		    } else if (context.createJavaScriptNode) {
		      // In older Safari versions
		      this._node = context.createJavaScriptNode(this.bufferSize, 0, this.channels);
		    } else {
		      throw new Error("Bad version of web audio API?");
		    }
		  }

		  /**
		   * Size of output buffers in samples, as a hint for latency/scheduling
		   * @type {number}
		   * @readonly
		   */
		  WebAudioBackend.prototype.bufferSize = 4096;

		  /**
		   * Remaining sample count at which a 'bufferlow' event will be triggered.
		   *
		   * Will be pinged when falling below bufferThreshold or bufferSize,
		   * whichever is larger.
		   *
		   * @type {number}
		   */
		  WebAudioBackend.prototype.bufferThreshold = 8192;

		  /**
		   * Internal volume property backing.
		   * @type {number}
		   * @access private
		   */
		  WebAudioBackend.prototype._volume = 1;

		  /**
			 * Volume multiplier, defaults to 1.0.
			 * @name volume
			 * @type {number}
			 */
			Object.defineProperty(WebAudioBackend.prototype, 'volume', {
				get: function getVolume() {
		      return this._volume;
				},
				set: function setVolume(val) {
		      this._volume = +val;
				}
			});

		  /**
		   * Internal muted property backing.
		   * @type {number}
		   * @access private
		   */
		  WebAudioBackend.prototype._muted = false;

		  /**
			 * Is the backend currently set to mute output?
			 * When muted, this overrides the volume property.
			 *
			 * @type {boolean}
			 */
			Object.defineProperty(WebAudioBackend.prototype, 'muted', {
		 		get: function getMuted() {
		      return this._muted;
		 		},
		 		set: function setMuted(val) {
		      this._muted = !!val;
		 		}
		 	});

		  /**
		   * onaudioprocess event handler for the ScriptProcessorNode
		   * @param {AudioProcessingEvent} event - audio processing event object
		   * @access private
		   */
		  WebAudioBackend.prototype._audioProcess = function(event) {
		    var channel, input, output, i, playbackTime;
		    if (typeof event.playbackTime === 'number') {
		      playbackTime = event.playbackTime;
		    } else {
		      // Safari 6.1 hack
		      playbackTime = this._context.currentTime + (this.bufferSize / this.rate);
		    }

		    var expectedTime = this._playbackTimeAtBufferTail;
		    if (expectedTime < playbackTime) {
		      // we may have lost some time while something ran too slow
		      this._delayedTime += (playbackTime - expectedTime);
		    }

		    if (this._bufferQueue.sampleCount() < this.bufferSize) {
		      // We might be in a throttled background tab; go ping the decoder
		      // and let it know we need more data now!
		      // @todo use standard event firing?
		      if (this.onstarved) {
		        this.onstarved();
		      }
		    }

		    // If we still haven't got enough data, write a buffer of silence
		    // to all channels and record an underrun event.
		    // @todo go ahead and output the data we _do_ have?
		    if (this._bufferQueue.sampleCount() < this.bufferSize) {
		      for (channel = 0; channel < this.channels; channel++) {
		        output = event.outputBuffer.getChannelData(channel);
		        for (i = 0; i < this.bufferSize; i++) {
		          output[i] = 0;
		        }
		      }
		      this._dropped++;
		      return;
		    }

		    var volume = (this.muted ? 0 : this.volume);

		    // Actually get that data and write it out...
		    var inputBuffer = this._bufferQueue.nextBuffer();
		    if (inputBuffer[0].length < this.bufferSize) {
		      // This should not happen, but trust no invariants!
		      throw 'Audio buffer not expected length.';
		    }
		    for (channel = 0; channel < this.channels; channel++) {
		      input = inputBuffer[channel];

		      // Save this buffer data for later in case we pause
		      this._liveBuffer[channel].set(inputBuffer[channel]);

		      // And play it out with volume applied...
		      output = event.outputBuffer.getChannelData(channel);
		      for (i = 0; i < input.length; i++) {
		        output[i] = input[i] * volume;
		      }
		    }
		    this._queuedTime += (this.bufferSize / this.rate);
		    this._playbackTimeAtBufferTail = playbackTime + (this.bufferSize / this.rate);

		    if (this._bufferQueue.sampleCount() < Math.max(this.bufferSize, this.bufferThreshold)) {
		      // Let the decoder know ahead of time we're running low on data.
		      // @todo use standard event firing?
		      if (this.onbufferlow) {
		        nextTick(this.onbufferlow.bind(this));
		      }
		    }
		  };


		  /**
		   * Return a count of samples that have been queued or output but not yet played.
		   *
		   * @returns {number} - sample count
		   * @access private
		   */
		  WebAudioBackend.prototype._samplesQueued = function() {
		    var bufferedSamples = this._bufferQueue.sampleCount();
		    var remainingSamples = Math.floor(this._timeAwaitingPlayback() * this.rate);

		    return bufferedSamples + remainingSamples;
		  };

		  /**
		   * Return time duration between the present and the endpoint of audio
		   * we have already sent out from our queue to Web Audio.
		   *
		   * @returns {number} - seconds
		   */
		  WebAudioBackend.prototype._timeAwaitingPlayback = function() {
		    return Math.max(0, this._playbackTimeAtBufferTail - this._context.currentTime);
		  };

		  /**
		   * Get info about current playback state.
		   *
		   * @return {PlaybackState} - info about current playback state
		   */
		  WebAudioBackend.prototype.getPlaybackState = function() {
		    return {
		      playbackPosition: this._queuedTime - this._timeAwaitingPlayback(),
		      samplesQueued: this._samplesQueued(),
		      dropped: this._dropped,
		      delayed: this._delayedTime
		    };
		  };

		  /**
		   * Wait asynchronously until the backend is ready before continuing.
		   *
		   * This will always call immediately for the Web Audio API backend,
		   * as there is no async setup process.
		   *
		   * @param {function} callback - to be called when ready
		   */
		  WebAudioBackend.prototype.waitUntilReady = function(callback) {
		    callback();
		  };

		  /**
		   * Append a buffer of audio data to the output queue for playback.
		   *
		   * Audio data must be at the expected sample rate; resampling is done
		   * upstream in {@link AudioFeeder}.
		   *
		   * @param {SampleBuffer} sampleData - audio data at target sample rate
		   */
		  WebAudioBackend.prototype.appendBuffer = function(sampleData) {
		    this._bufferQueue.appendBuffer(sampleData);
		  };

		  /**
		   * Start playback.
		   *
		   * Audio should have already been queued at this point,
		   * or starvation may occur immediately.
		   */
		  WebAudioBackend.prototype.start = function() {
		    this._node.onaudioprocess = this._audioProcess.bind(this);
		    this._node.connect(this.output);
		    this._playbackTimeAtBufferTail = this._context.currentTime;
		  };

		  /**
		   * Stop playback, but don't release resources or clear the buffers.
		   * We'll probably come back soon.
		   */
		  WebAudioBackend.prototype.stop = function() {
		    if (this._node) {
		      var timeRemaining = this._timeAwaitingPlayback();
		      if (timeRemaining > 0) {
		        // We have some leftover samples that got queued but didn't get played.
		        // Unshift them back onto the beginning of the buffer.
		        // @todo make this not a horrible hack
		        var samplesRemaining = Math.round(timeRemaining * this.rate),
		            samplesAvailable = this._liveBuffer ? this._liveBuffer[0].length : 0;
		        if (samplesRemaining > samplesAvailable) {
		          //console.log('liveBuffer size ' + samplesRemaining + ' vs ' + samplesAvailable);
		          this._bufferQueue.prependBuffer(this._liveBuffer);
		          this._bufferQueue.prependBuffer(
		            this._bufferQueue.createBuffer(samplesRemaining - samplesAvailable));
		        } else {
		          this._bufferQueue.prependBuffer(
		            this._bufferQueue.trimBuffer(this._liveBuffer, samplesAvailable - samplesRemaining, samplesRemaining));
		        }
		        this._playbackTimeAtBufferTail -= timeRemaining;
		      }
		      this._node.onaudioprocess = null;
		      this._node.disconnect();
		    }
		  };

		  /**
		   * Flush any queued data out of the system.
		   */
		  WebAudioBackend.prototype.flush = function() {
		    this._bufferQueue.flush();
		  };

		  /**
		   * Close out the playback system and release resources.
		   *
		   * @todo consider releasing the AudioContext when possible
		   */
		  WebAudioBackend.prototype.close = function() {
		    this.stop();

		    this._context = null;
		  };

		  /**
		   * Synchronous callback for when we run out of input data
		   *
		   * @type function|null
		   */
		  WebAudioBackend.prototype.onstarved = null;

		  /**
		   * Asynchronous callback for when the buffer runs low and
		   * should be refilled soon.
		   *
		   * @type function|null
		   */
		  WebAudioBackend.prototype.onbufferlow = null;

		  /**
		   * Check if Web Audio API appears to be supported.
		   *
		   * Note this is somewhat optimistic; will return true even if there are no
		   * audio devices available, as long as the API is present.
		   *
		   * @returns {boolean} - true if this browser appears to support Web Audio API
		   */
		  WebAudioBackend.isSupported = function() {
		    return !!AudioContext;
		  };

		  /**
		   * Holder of audio context to be used/reused by WebAudioBackend.
		   * @see {WebAudioBackend#initSharedAudioContext}
		   *
		   * @type {AudioContext}
		   */
		  WebAudioBackend.sharedAudioContext = null;

		  /**
			 * Force initialization of the default Web Audio API context.
			 *
			 * Some browsers (such as mobile Safari) disable audio output unless
			 * first triggered from a UI event handler; call this method as a hint
			 * that you will be starting up an AudioFeeder soon but won't have data
			 * for it until a later callback.
		   *
		   * @returns {AudioContext|null} - initialized AudioContext instance, if applicable
			 */
		  WebAudioBackend.initSharedAudioContext = function() {
				if (!WebAudioBackend.sharedAudioContext) {
					if (WebAudioBackend.isSupported()) {
						// We're only allowed 4 contexts on many browsers
						// and there's no way to discard them (!)...
						var context = new AudioContext(),
							node;
						if (context.createScriptProcessor) {
							node = context.createScriptProcessor(1024, 0, 2);
						} else if (context.createJavaScriptNode) {
							node = context.createJavaScriptNode(1024, 0, 2);
						} else {
							throw new Error( "Bad version of web audio API?" );
						}

						// Don't actually run any audio, just start & stop the node
						node.connect(context.destination);
						node.disconnect();

		        // So far so good. Keep it around!
		        WebAudioBackend.sharedAudioContext = context;
					}
				}
		    return WebAudioBackend.sharedAudioContext;
			};

		  module.exports = WebAudioBackend;

		})();


	/***/ },
	/* 3 */
	/***/ function(module, exports) {

		module.exports = (function() {
			// Don't try to check for setImmediate directly; webpack implements
			// it using setTimeout which will be throttled in background tabs.
			// Checking directly on the global window object skips this interference.
			if (typeof window.setImmediate !== 'undefined') {
				return window.setImmediate;
			}

			// window.postMessage goes straight to the event loop, no throttling.
			if (window && window.postMessage) {
				var nextTickQueue = [];
				window.addEventListener('message', function(event) {
					if (event.source === window) {
						var data = event.data;
						if (typeof data === 'object' && data.nextTickBrowserPingMessage) {
							var callback = nextTickQueue.pop();
							if (callback) {
								callback();
							}
						}
					}
				});
				return function(callback) {
					nextTickQueue.push(callback);
					window.postMessage({
						nextTickBrowserPingMessage: true
					}, document.location.toString())
				};
			}

			// Timeout fallback may be poor in background tabs
			return function(callback) {
				setTimeout(callback, 0);
			}
		})();


	/***/ },
	/* 4 */
	/***/ function(module, exports, __webpack_require__) {

		(function() {

		  /* global ActiveXObject */
		  var dynamicaudio_swf = __webpack_require__(5);

		  var nextTick = __webpack_require__(3);

		  /**
		   * Constructor for AudioFeeder's Flash audio backend.
		   * @class
		   * @param {number} numChannels - requested count of output channels (actual will be fixed at 2)
		   * @param {number} sampleRate - requested sample rate for output (actual will be fixed at 44.1 kHz)
		   * @param {Object} options - pass URL path to directory containing 'dynamicaudio.swf' in 'base' parameter
		   *
		   * @classdesc Flash audio output backend for AudioFeeder.
		   * Maintains a local queue of data to be sent down to the Flash shim.
		   * Resampling to stereo 44.1 kHz is done upstream in AudioFeeder.
		   */
		  var FlashBackend = function(numChannels, sampleRate, options) {
		    options = options || {};
		    var flashOptions = {};
		    if (typeof options.base === 'string') {
		      // @fixme replace the version string with an auto-updateable one
		      flashOptions.swf = options.base + '/' + dynamicaudio_swf;
		    }
		    if (options.bufferSize) {
		      this.bufferSize = (options.bufferSize | 0);
		    }

		    this._flashaudio = new DynamicAudio(flashOptions);
		    this._flashBuffer = '';
		    this._flushTimeout = null;
		    this._cachedFlashState = null;
		    this._cachedFlashTime = 0;
		    this._cachedFlashInterval = 40; // resync state no more often than every X ms

		    this._waitUntilReadyQueue = [];
		    this.onready = function() {
		        this._flashaudio.flashElement.setBufferSize(this.bufferSize);
		        this._flashaudio.flashElement.setBufferThreshold(this.bufferThreshold);
		        while (this._waitUntilReadyQueue.length) {
		            var callback = this._waitUntilReadyQueue.shift();
		            callback.apply(this);
		        }
		    };
		    this.onlog = function(msg) {
		        console.log('AudioFeeder FlashBackend: ' + msg);
		    }

		    this.bufferThreshold = this.bufferSize * 2;

		    var events = {
		        'ready': 'sync',
		        'log': 'sync',
		        'starved': 'sync',
		        'bufferlow': 'async'
		    };
		    this._callbackName = 'AudioFeederFlashBackendCallback' + this._flashaudio.id;
		    var self = this;
		    window[this._callbackName] = (function(eventName) {
		        var method = events[eventName],
		            callback = this['on' + eventName];
		        if (method && callback) {
		            if (method === 'async') {
		                nextTick(callback.bind(this));
		            } else {
		                callback.apply(this, Array.prototype.slice.call(arguments, 1));
		                this._flushFlashBuffer();
		            }
		        }
		    }).bind(this);
		  };

		  /**
		   * Actual sample rate supported for output, in Hz
		   * Fixed to 44.1 kHz for Flash backend.
		   * @type {number}
		   * @readonly
		   */
		  FlashBackend.prototype.rate = 44100;

		  /**
		   * Actual count of channels supported for output
		   * Fixed to stereo for Flash backend.
		   * @type {number}
		   * @readonly
		   */
		  FlashBackend.prototype.channels = 2;

		  /**
		   * Buffer size hint.
		   * @type {number}
		   * @readonly
		   */
		  FlashBackend.prototype.bufferSize = 4096;

		  /**
		   * Internal bufferThreshold property backing.
		   * @type {number}
		   * @access private
		   */
		  FlashBackend.prototype._bufferThreshold = 8192;

		  /**
		   * Remaining sample count at which a 'bufferlow' event will be triggered.
		   *
		   * Will be pinged when falling below bufferThreshold or bufferSize,
		   * whichever is larger.
		   *
		   * @type {number}
		   */
		  Object.defineProperty(FlashBackend.prototype, 'bufferThreshold', {
		    get: function getBufferThreshold() {
		      return this._bufferThreshold;
		    },
		    set: function setBufferThreshold(val) {
		      this._bufferThreshold = val | 0;
		      this.waitUntilReady((function() {
		        this._flashaudio.flashElement.setBufferThreshold(this._bufferThreshold);
		      }).bind(this));
		    }
		  });

		  /**
		   * Internal volume property backing.
		   * @type {number}
		   * @access private
		   */
		  FlashBackend.prototype._volume = 1;

		  /**
			 * Volume multiplier, defaults to 1.0.
			 * @name volume
			 * @type {number}
			 */
			Object.defineProperty(FlashBackend.prototype, 'volume', {
				get: function getVolume() {
		      return this._volume;
				},
				set: function setVolume(val) {
		      this._volume = +val;
		      this.waitUntilReady(this._flashVolumeUpdate.bind(this));
				}
			});

		  /**
		   * Internal muted property backing.
		   * @type {number}
		   * @access private
		   */
		  FlashBackend.prototype._muted = false;

		  /**
			 * Is the backend currently set to mute output?
			 * When muted, this overrides the volume property.
			 *
			 * @type {boolean}
			 */
			Object.defineProperty(FlashBackend.prototype, 'muted', {
		 		get: function getMuted() {
		      return this._muted;
		 		},
		 		set: function setMuted(val) {
		      this._muted = !!val;
		      this.waitUntilReady(this._flashVolumeUpdate.bind(this));
		 		}
		 	});

		  /**
		   * Are we paused/idle?
		   * @type {boolean}
		   * @access private
		   */
		  FlashBackend.prototype._paused = true;

		  /**
		   * Pass the currently configured muted+volume state down to the Flash plugin
		   * @access private
		   */
		  FlashBackend.prototype._flashVolumeUpdate = function() {
		    if (this._flashaudio && this._flashaudio.flashElement && this._flashaudio.flashElement.setVolume) {
		      this._flashaudio.flashElement.setVolume(this.muted ? 0 : this.volume);
		    }
		  }

		  /**
		   * Scaling and reordering of output for the Flash fallback.
		   * Input data must be pre-resampled to the correct sample rate.
		   *
		   * @param {SampleBuffer} samples - input data as separate channels of 32-bit float
		   * @returns {Int16Array} - interleaved stereo 16-bit signed integer output
		   * @access private
		   *
		   * @todo handle input with higher channel counts better
		   * @todo try sending floats to flash without losing precision?
		   */
		  FlashBackend.prototype._resampleFlash = function(samples) {
		    var sampleincr = 1;
		  	var samplecount = samples[0].length;
		  	var newSamples = new Int16Array(samplecount * 2);
		  	var chanLeft = samples[0];
		  	var chanRight = this.channels > 1 ? samples[1] : chanLeft;
		    var volume = this.muted ? 0 : this.volume;
		  	var multiplier = volume * 16384; // smaller than 32768 to allow some headroom from those floats
		  	for(var s = 0; s < samplecount; s++) {
		  		var idx = (s * sampleincr) | 0;
		  		var idx_out = s * 2;
		  		// Use a smaller
		  		newSamples[idx_out] = chanLeft[idx] * multiplier;
		  		newSamples[idx_out + 1] = chanRight[idx] * multiplier;
		  	}
		  	return newSamples;
		  };

		  var hexDigits = ['0', '1', '2', '3', '4', '5', '6', '7',
		           '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
		  var hexBytes = [];
		  for (var i = 0; i < 256; i++) {
		    hexBytes[i] = hexDigits[(i & 0x0f)] +
		            hexDigits[(i & 0xf0) >> 4];
		  }
		  function hexString(buffer) {
		    var samples = new Uint8Array(buffer);
		    var digits = "",
		      len = samples.length;
		    for (var i = 0; i < len; i++) {
		      // Note that in IE 11 string concatenation is twice as fast as
		      // the traditional make-an-array-and-join here.
		      digits += hexBytes[samples[i]];
		    }
		    return digits;
		  }

		  /**
		   * Send any pending data off to the Flash plugin.
		   *
		   * @access private
		   */
		  FlashBackend.prototype._flushFlashBuffer = function() {
		    var chunk = this._flashBuffer,
		      flashElement = this._flashaudio.flashElement;

		    if (this._cachedFlashState) {
		        this._cachedFlashState.samplesQueued += chunk.length / 8;
		    }
		    this._flashBuffer = '';
		    this._flushTimeout = null;

		    if (chunk.length > 0) {
		      this.waitUntilReady(function() {
		        flashElement.write(chunk);
		      });
		    }
		  };

		  /**
		   * Append a buffer of audio data to the output queue for playback.
		   *
		   * Audio data must be at the expected sample rate; resampling is done
		   * upstream in {@link AudioFeeder}.
		   *
		   * @param {SampleBuffer} sampleData - audio data at target sample rate
		   */
		  FlashBackend.prototype.appendBuffer = function(sampleData) {
		    var resamples = this._resampleFlash(sampleData);
		    if (resamples.length > 0) {
		      var str = hexString(resamples.buffer);
		      this._flashBuffer += str;
		      if (!this._flushTimeout) {
		        // consolidate multiple consecutive tiny buffers in one pass;
		        // pushing data to Flash is relatively expensive on slow machines
		        this._flushTimeout = true;
		        nextTick(this._flushFlashBuffer.bind(this));
		      }
		    }
		  };

		  /**
		   * Get info about current playback state.
		   *
		   * @return {PlaybackState} - info about current playback state
		   */
		  FlashBackend.prototype.getPlaybackState = function() {
		    if (this._flashaudio && this._flashaudio.flashElement && this._flashaudio.flashElement.write) {
		      var now = Date.now(),
		        delta = this._paused ? 0 : (now - this._cachedFlashTime),
		        state;
		      if (this._cachedFlashState && delta < this._cachedFlashInterval) {
		        var cachedFlashState = this._cachedFlashState;
		        state = {
		          playbackPosition: cachedFlashState.playbackPosition + delta / 1000,
		          samplesQueued: cachedFlashState.samplesQueued -
		            Math.max(0, Math.round(delta * this.rate / 1000)),
		          dropped: cachedFlashState.dropped,
		          delayed: cachedFlashState.delayed
		        };
		      } else {
		        state = this._flashaudio.flashElement.getPlaybackState();
		        this._cachedFlashState = state;
		        this._cachedFlashTime = now;
		      }
		      state.samplesQueued += this._flashBuffer.length / 8;
		      return state;
		    } else {
		      //console.log('getPlaybackState USED TOO EARLY');
		      return {
		        playbackPosition: 0,
		        samplesQueued: 0,
		        dropped: 0,
		        delayed: 0
		      };
		    }
		  };

		  /**
		   * Wait until the backend is ready to start, then call the callback.
		   *
		   * @param {function} callback - called on completion
		   * @todo handle fail case better?
		   */
		  FlashBackend.prototype.waitUntilReady = function(callback) {
		    if (this._flashaudio && this._flashaudio.flashElement.write) {
		      callback.apply(this);
		    } else {
		      this._waitUntilReadyQueue.push(callback);
		    }
		  };

		  /**
		   * Start playback.
		   *
		   * Audio should have already been queued at this point,
		   * or starvation may occur immediately.
		   */
		  FlashBackend.prototype.start = function() {
		    this._flashaudio.flashElement.start();
		    this._paused = false;
		    this._cachedFlashState = null;
		  };

		  /**
		   * Stop playback, but don't release resources or clear the buffers.
		   * We'll probably come back soon.
		   */
		  FlashBackend.prototype.stop = function() {
		    this._flashaudio.flashElement.stop();
		    this._paused = true;
		    this._cachedFlashState = null;
		  };

		  /**
		   * Flush any queued data out of the system.
		   */
		  FlashBackend.prototype.flush = function() {
		    this._flashaudio.flashElement.flush();
		    this._cachedFlashState = null;
		  };

		  /**
		   * Close out the playback system and release resources.
		   */
		  FlashBackend.prototype.close = function() {
		    this.stop();

		    var wrapper = this._flashaudio.flashWrapper;
		    wrapper.parentNode.removeChild(wrapper);
		    this._flashaudio = null;
		    delete window[this._callbackName];
		  };

		  /**
		   * Synchronous callback for when we run out of input data
		   *
		   * @type function|null
		   */
		  FlashBackend.prototype.onstarved = null;

		  /**
		   * Asynchronous callback for when the buffer runs low and
		   * should be refilled soon.
		   *
		   * @type function|null
		   */
		  FlashBackend.prototype.onbufferlow = null;

		  /**
		   * Check if the browser appears to support Flash.
		   *
		   * Note this is somewhat optimistic, in that Flash may be supported
		   * but the dynamicaudio.swf file might not load, or it might load
		   * but there might be no audio devices, etc.
		   *
		   * Currently only checks for the ActiveX Flash plugin for Internet Explorer,
		   * as other target browsers support Web Audio API.
		   *
		   * @returns {boolean} - true if this browser appears to support Flash
		   */
		  FlashBackend.isSupported = function() {
				if (navigator.userAgent.indexOf('Trident') !== -1) {
					// We only do the ActiveX test because we only need Flash in
					// Internet Explorer 10/11. Other browsers use Web Audio directly
					// (Edge, Safari) or native playback, so there's no need to test
					// other ways of loading Flash.
					try {
						var obj = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
						return true;
					} catch(e) {
						return false;
					}
				}
				return false;
		  };

			/** Flash fallback **/

			/*
			The Flash fallback is based on https://github.com/an146/dynamicaudio.js

			This is the contents of the LICENSE file:

			Copyright (c) 2010, Ben Firshman
			All rights reserved.

			Redistribution and use in source and binary forms, with or without
			modification, are permitted provided that the following conditions are met:

			 * Redistributions of source code must retain the above copyright notice, this
			   list of conditions and the following disclaimer.
			 * Redistributions in binary form must reproduce the above copyright notice,
			   this list of conditions and the following disclaimer in the documentation
			   and/or other materials provided with the distribution.
			 * The names of its contributors may not be used to endorse or promote products
			   derived from this software without specific prior written permission.

			THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
			ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
			WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
			DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
			ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
			(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
			LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
			ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
			(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
			SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
			*/


		  /**
		   * Wrapper class for instantiating Flash plugin.
		   *
		   * @constructor
		   * @param {Object} args - pass 'swf' to override default dynamicaudio.swf URL
		   * @access private
		   */
			function DynamicAudio(args) {
				if (this instanceof arguments.callee) {
					if (typeof this.init === "function") {
						this.init.apply(this, (args && args.callee) ? args : arguments);
					}
				} else {
					return new arguments.callee(arguments);
				}
			}


			DynamicAudio.nextId = 1;

			DynamicAudio.prototype = {
				nextId: null,
				swf: dynamicaudio_swf,

				flashWrapper: null,
				flashElement: null,

				init: function(opts) {
					var self = this;
					self.id = DynamicAudio.nextId++;

					if (opts && typeof opts.swf !== 'undefined') {
						self.swf = opts.swf;
					}


					self.flashWrapper = document.createElement('div');
					self.flashWrapper.id = 'dynamicaudio-flashwrapper-'+self.id;
					// Credit to SoundManager2 for this:
					var s = self.flashWrapper.style;
					s.position = 'fixed';
					s.width = '11px'; // must be at least 6px for flash to run fast
					s.height = '11px';
					s.bottom = s.left = '0px';
					s.overflow = 'hidden';
					self.flashElement = document.createElement('div');
					self.flashElement.id = 'dynamicaudio-flashelement-'+self.id;
					self.flashWrapper.appendChild(self.flashElement);

					document.body.appendChild(self.flashWrapper);

					var id = self.flashElement.id;
		            var params = '<param name="FlashVars" value="objectId=' + self.id + '">';

					self.flashWrapper.innerHTML = "<object id='"+id+"' width='10' height='10' type='application/x-shockwave-flash' data='"+self.swf+"' style='visibility: visible;'><param name='allowscriptaccess' value='always'>" + params + "</object>";
					self.flashElement = document.getElementById(id);
				},
			};

		  module.exports = FlashBackend;

		})();


	/***/ },
	/* 5 */
	/***/ function(module, exports, __webpack_require__) {

		module.exports = __webpack_require__.p + "dynamicaudio.swf?version=e187c46551bc31edb18fea80fdc3e941";

	/***/ }
	/******/ ])
	});
	;

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__.p + "dynamicaudio.swf?version=e187c46551bc31edb18fea80fdc3e941";

/***/ },
/* 19 */
/***/ function(module, exports) {

	/**
	 * Give as your 'process' function something that will trigger an async
	 * operation, then call the left() or right() methods to run another
	 * iteration, bisecting to the given direction.
	 *
	 * Caller is responsible for determining when done.
	 *
	 * @params options object {
	 *   start: number,
	 *   end: number,
	 *   process: function(start, position, end)
	 * }
	 */
	function Bisector(options) {
		var start = options.start,
			end = options.end,
			position = 0,
			self = this,
			n = 0;

		function iterate() {
			n++;
			position = Math.floor((start + end) / 2);
			return options.process(start, end, position);
		}

		self.start = function() {
			iterate();
			return self;
		};

		self.left = function() {
			end = position;
			return iterate();
		};

		self.right = function() {
			start = position;
			return iterate();
		};
	}

	module.exports = Bisector;


/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Proxy object for web worker interface for codec classes.
	 *
	 * Used by the high-level player interface.
	 *
	 * @author Brion Vibber <brion@pobox.com>
	 * @copyright 2015
	 * @license MIT-style
	 */
	var OGVLoader = __webpack_require__(3);

	var OGVWrapperCodec = (function(options) {
		options = options || {};
		var self = this,
			suffix = '?version=' + encodeURIComponent(("1.2.1-20160924234040-a1a879e")),
			base = (typeof options.base === 'string') ? (options.base + '/') : '',
			type = (typeof options.type === 'string') ? options.type : 'video/ogg',
			processing = false,
			demuxer = null,
			videoDecoder = null,
			audioDecoder = null,
			flushIter = 0;

		// Wrapper for callbacks to drop them after a flush
		function flushSafe(func) {
			var savedFlushIter = flushIter;
			return function(arg) {
				if (flushIter <= savedFlushIter) {
					func(arg);
				}
			};
		}

		var loadedMetadata = false;
		Object.defineProperty(self, 'loadedMetadata', {
			get: function() {
				return loadedMetadata;
			}
		});

		Object.defineProperty(self, 'processing', {
			get: function() {
				return processing/*
					|| (videoDecoder && videoDecoder.processing)
					|| (audioDecoder && audioDecoder.processing)*/;
			}
		});

		Object.defineProperty(self, 'duration', {
			get: function() {
				if (self.loadedMetadata) {
					return demuxer.duration;
				} else {
					return NaN;
				}
			}
		});

		Object.defineProperty(self, 'hasAudio', {
			get: function() {
				return self.loadedMetadata && !!audioDecoder;
			}
		});

		Object.defineProperty(self, 'audioReady', {
			get: function() {
				return self.hasAudio && demuxer.audioReady;
			}
		});

		Object.defineProperty(self, 'audioTimestamp', {
			get: function() {
				return demuxer.audioTimestamp;
			}
		});

		Object.defineProperty(self, 'audioFormat', {
			get: function() {
				if (self.hasAudio) {
					return audioDecoder.audioFormat;
				} else {
					return null;
				}
			}
		});

		Object.defineProperty(self, 'audioBuffer', {
			get: function() {
				if (self.hasAudio) {
					return audioDecoder.audioBuffer;
				} else {
					return null;
				}
			}
		});

		Object.defineProperty(self, 'hasVideo', {
			get: function() {
				return self.loadedMetadata && !!videoDecoder;
			}
		});

		Object.defineProperty(self, 'frameReady', {
			get: function() {
				return self.hasVideo && demuxer.frameReady;
			}
		});

		Object.defineProperty(self, 'frameTimestamp', {
			get: function() {
				return demuxer.frameTimestamp;
			}
		});

		Object.defineProperty(self, 'keyframeTimestamp', {
			get: function() {
				return demuxer.keyframeTimestamp;
			}
		});

		Object.defineProperty(self, 'videoFormat', {
			get: function() {
				if (self.hasVideo) {
					return videoDecoder.videoFormat;
				} else {
					return null;
				}
			}
		});

		Object.defineProperty(self, 'frameBuffer', {
			get: function() {
				if (self.hasVideo) {
					return videoDecoder.frameBuffer;
				} else {
					return null;
				}
			}
		});

		Object.defineProperty(self, 'seekable', {
			get: function() {
				return demuxer.seekable;
			}
		});

		// - public methods
		self.init = function(callback) {
			var demuxerClassName;
			if (options.type === 'video/webm') {
				demuxerClassName = 'OGVDemuxerWebM';
			} else {
				demuxerClassName = 'OGVDemuxerOgg';
			}
			processing = true;
			OGVLoader.loadClass(demuxerClassName, function(demuxerClass) {
				demuxer = new demuxerClass();
				demuxer.onseek = function(offset) {
					if (self.onseek) {
						self.onseek(offset);
					}
				};
				demuxer.init(function() {
					processing = false;
					callback();
				});
			});
		};

		self.close = function() {
			if (demuxer) {
				demuxer.close();
				demuxer = null;
			}
			if (videoDecoder) {
				videoDecoder.close();
				videoDecoder = null;
			}
			if (audioDecoder) {
				audioDecoder.close();
				audioDecoder = null;
			}
		};

		self.receiveInput = function(data, callback) {
			demuxer.receiveInput(data, callback);
		};

		var audioClassMap = {
			vorbis: 'OGVDecoderAudioVorbis',
			opus: 'OGVDecoderAudioOpus'
		};
		function loadAudioCodec(callback) {
			if (demuxer.audioCodec) {
				var className = audioClassMap[demuxer.audioCodec];
				processing = true;
				OGVLoader.loadClass(className, function(audioCodecClass) {
					var audioOptions = {};
					if (demuxer.audioFormat) {
						audioOptions.audioFormat = demuxer.audioFormat;
					}
					audioDecoder = new audioCodecClass(audioOptions);
					audioDecoder.init(function() {
						loadedAudioMetadata = audioDecoder.loadedMetadata;
						processing = false;
						callback();
					});
				}, {
					worker: options.worker
				});
			} else {
				callback();
			}
		}

		var videoClassMap = {
			theora: 'OGVDecoderVideoTheora',
			vp8: 'OGVDecoderVideoVP8',
			vp9: 'OGVDecoderVideoVP9'
		};
		function loadVideoCodec(callback) {
			if (demuxer.videoCodec) {
				var className = videoClassMap[demuxer.videoCodec];
				processing = true;
				OGVLoader.loadClass(className, function(videoCodecClass) {
					var videoOptions = {};
					if (demuxer.videoFormat) {
						videoOptions.videoFormat = demuxer.videoFormat;
					}
					if (options.memoryLimit) {
						videoOptions.memoryLimit = options.memoryLimit;
					}
					videoDecoder = new videoCodecClass(videoOptions);
					videoDecoder.init(function() {
						loadedVideoMetadata = videoDecoder.loadedMetadata;
						processing = false;
						callback();
					});
				}, {
					worker: options.worker
				});
			} else {
				callback();
			}
		}

		var loadedDemuxerMetadata = false,
			loadedAudioMetadata = false,
			loadedVideoMetadata = false,
			loadedAllMetadata = false;

		self.process = function(callback) {
			if (processing) {
				throw new Error('reentrancy fail on OGVWrapperCodec.process');
			}
			processing = true;

			var videoPacketCount = demuxer.videoPackets.length,
				audioPacketCount = demuxer.audioPackets.length,
				start = (window.performance ? performance.now() : Date.now());
			function finish(result) {
				processing = false;
				var delta = (window.performance ? performance.now() : Date.now()) - start;
				if (delta > 8) {
					console.log('slow demux in ' + delta + ' ms; ' +
						(demuxer.videoPackets.length - videoPacketCount) + ' +video packets, ' +
						(demuxer.audioPackets.length - audioPacketCount) + ' +audio packets');
				}
				//console.log('demux returned ' + (result ? 'true' : 'false') + '; ' + demuxer.videoPackets.length + '; ' + demuxer.audioPackets.length);
				callback(result);
			}

			function doProcessData() {
				videoPacketCount = demuxer.videoPackets.length,
				audioPacketCount = demuxer.audioPackets.length,
				start = (window.performance ? performance.now() : Date.now());
				demuxer.process(finish);
			}

			if (demuxer.loadedMetadata && !loadedDemuxerMetadata) {

				// Demuxer just reached its metadata. Load the relevant codecs!
				loadAudioCodec(function() {
					loadVideoCodec(function() {
						loadedDemuxerMetadata = true;
						loadedAudioMetadata = !audioDecoder;
						loadedVideoMetadata = !videoDecoder;
						loadedAllMetadata = loadedAudioMetadata && loadedVideoMetadata;
						finish(true);
					});
				});

			} else if (loadedDemuxerMetadata && !loadedAudioMetadata) {

				if (audioDecoder.loadedMetadata) {

					loadedAudioMetadata = true;
					loadedAllMetadata = loadedAudioMetadata && loadedVideoMetadata;
					finish(true);

				} else if (demuxer.audioReady) {

					demuxer.dequeueAudioPacket(function(packet) {
						audioDecoder.processHeader(packet, function(ret) {
							finish(true);
						});
					});

				} else {

					doProcessData();

				}

			} else if (loadedAudioMetadata && !loadedVideoMetadata) {

				if (videoDecoder.loadedMetadata) {

					loadedVideoMetadata = true;
					loadedAllMetadata = loadedAudioMetadata && loadedVideoMetadata;
					finish(true);

				} else if (demuxer.frameReady) {

					processing = true;
					demuxer.dequeueVideoPacket(function(packet) {
						videoDecoder.processHeader(packet, function() {
							finish(true);
						});
					});

				} else {

					doProcessData();

				}

			} else if (loadedVideoMetadata && !self.loadedMetadata && loadedAllMetadata) {

				// Ok we've found all the metadata there is. Enjoy.
				loadedMetadata = true;
				finish(true);

			} else if (self.loadedMetadata && (!self.hasAudio || demuxer.audioReady) && (!self.hasVideo || demuxer.frameReady)) {

				// Already queued up some packets. Go read them!
				finish(true);

			} else {

				// We need to process more of the data we've already received,
				// or ask for more if we ran out!
				doProcessData();

			}

		};

		self.decodeFrame = function(callback) {
			var cb = flushSafe(callback),
				timestamp = self.frameTimestamp,
				keyframeTimestamp = self.keyframeTimestamp;
			demuxer.dequeueVideoPacket(function(packet) {
				videoDecoder.processFrame(packet, function(ok) {
					// hack
					if (videoDecoder.frameBuffer) {
						videoDecoder.frameBuffer.timestamp = timestamp;
						videoDecoder.frameBuffer.keyframeTimestamp = keyframeTimestamp;
					}
					cb(ok);
				});
			});
		};

		self.decodeAudio = function(callback) {
			var cb = flushSafe(callback);
			demuxer.dequeueAudioPacket(function(packet) {
				audioDecoder.processAudio(packet, cb);
			});
		}

		self.discardFrame = function(callback) {
			demuxer.dequeueVideoPacket(function(packet) {
				callback();
			});
		};

		self.discardAudio = function(callback) {
			demuxer.dequeueAudioPacket(function(packet) {
				callback();
			});
		};

		self.flush = function(callback) {
			flushIter++;
			demuxer.flush(callback);
		};

		self.getKeypointOffset = function(timeSeconds, callback) {
			demuxer.getKeypointOffset(timeSeconds, callback);
		};

		self.seekToKeypoint = function(timeSeconds, callback) {
			demuxer.seekToKeypoint(timeSeconds, flushSafe(callback));
		}

		self.onseek = null;

		Object.defineProperty(self, "demuxerCpuTime", {
			get: function() {
				if (demuxer) {
					return demuxer.cpuTime;
				} else {
					return 0;
				}
			}
		});
		Object.defineProperty(self, "audioCpuTime", {
			get: function() {
				if (audioDecoder) {
					return audioDecoder.cpuTime;
				} else {
					return 0;
				}
			}
		});
		Object.defineProperty(self, "videoCpuTime", {
			get: function() {
				if (videoDecoder) {
					return videoDecoder.cpuTime;
				} else {
					return 0;
				}
			}
		});

		return self;
	});

	module.exports = OGVWrapperCodec;


/***/ }
/******/ ])
});
;