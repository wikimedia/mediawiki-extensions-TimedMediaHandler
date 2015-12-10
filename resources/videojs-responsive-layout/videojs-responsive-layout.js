/**
 * videojs-responsive-layout
 * @version 1.1.1
 * @copyright 2016 Derk-Jan Hartman
 * @license (MIT OR Apache-2.0)
 */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.videojsResponsiveLayout = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){

},{}],2:[function(require,module,exports){
var throttle = require('./throttle');

/**
 * Debounce execution of a function. Debouncing, unlike throttling,
 * guarantees that a function is only executed a single time, either at the
 * very beginning of a series of calls, or at the very end.
 *
 * @param  {Number}   delay         A zero-or-greater delay in milliseconds. For event callbacks, values around 100 or 250 (or even higher) are most useful.
 * @param  {Boolean}  atBegin       Optional, defaults to false. If atBegin is false or unspecified, callback will only be executed `delay` milliseconds
 *                                  after the last debounced-function call. If atBegin is true, callback will be executed only at the first debounced-function call.
 *                                  (After the throttled-function has not been called for `delay` milliseconds, the internal counter is reset).
 * @param  {Function} callback      A function to be executed after delay milliseconds. The `this` context and all arguments are passed through, as-is,
 *                                  to `callback` when the debounced-function is executed.
 *
 * @return {Function} A new, debounced function.
 */
module.exports = function ( delay, atBegin, callback ) {
	return callback === undefined ? throttle(delay, atBegin, false) : throttle(delay, callback, atBegin !== false);
};

},{"./throttle":4}],3:[function(require,module,exports){
module.exports = {
	throttle: require('./throttle'),
	debounce: require('./debounce')
};

},{"./debounce":2,"./throttle":4}],4:[function(require,module,exports){
var $ = require('jquery');

/**
 * Throttle execution of a function. Especially useful for rate limiting
 * execution of handlers on events like resize and scroll.
 *
 * @param  {Number}    delay          A zero-or-greater delay in milliseconds. For event callbacks, values around 100 or 250 (or even higher) are most useful.
 * @param  {Boolean}   noTrailing     Optional, defaults to false. If noTrailing is true, callback will only execute every `delay` milliseconds while the
 *                                    throttled-function is being called. If noTrailing is false or unspecified, callback will be executed one final time
 *                                    after the last throttled-function call. (After the throttled-function has not been called for `delay` milliseconds,
 *                                    the internal counter is reset)
 * @param  {Function}  callback       A function to be executed after delay milliseconds. The `this` context and all arguments are passed through, as-is,
 *                                    to `callback` when the throttled-function is executed.
 * @param  {Boolean}   debounceMode   If `debounceMode` is true (at begin), schedule `clear` to execute after `delay` ms. If `debounceMode` is false (at end),
 *                                    schedule `callback` to execute after `delay` ms.
 *
 * @return {Function}  A new, throttled, function.
 */
module.exports = function ( delay, noTrailing, callback, debounceMode ) {

	// After wrapper has stopped being called, this timeout ensures that
	// `callback` is executed at the proper times in `throttle` and `end`
	// debounce modes.
	var timeoutID;

	// Keep track of the last time `callback` was executed.
	var lastExec = 0;

	// `noTrailing` defaults to falsy.
	if ( typeof(noTrailing) !== 'boolean' ) {
		debounceMode = callback;
		callback = noTrailing;
		noTrailing = undefined;
	}

	// The `wrapper` function encapsulates all of the throttling / debouncing
	// functionality and when executed will limit the rate at which `callback`
	// is executed.
	function wrapper () {

		var self = this;
		var elapsed = Number(new Date()) - lastExec;
		var args = arguments;

		// Execute `callback` and update the `lastExec` timestamp.
		function exec () {
			lastExec = Number(new Date());
			callback.apply(self, args);
		}

		// If `debounceMode` is true (at begin) this is used to clear the flag
		// to allow future `callback` executions.
		function clear () {
			timeoutID = undefined;
		}

		if ( debounceMode && !timeoutID ) {
			// Since `wrapper` is being called for the first time and
			// `debounceMode` is true (at begin), execute `callback`.
			exec();
		}

		// Clear any existing timeout.
		if ( timeoutID ) {
			clearTimeout(timeoutID);
		}

		if ( debounceMode === undefined && elapsed > delay ) {
			// In throttle mode, if `delay` time has been exceeded, execute
			// `callback`.
			exec();

		} else if ( noTrailing !== true ) {
			// In trailing throttle mode, since `delay` time has not been
			// exceeded, schedule `callback` to execute `delay` ms after most
			// recent execution.
			//
			// If `debounceMode` is true (at begin), schedule `clear` to execute
			// after `delay` ms.
			//
			// If `debounceMode` is false (at end), schedule `callback` to
			// execute after `delay` ms.
			timeoutID = setTimeout(debounceMode ? clear : exec, debounceMode === undefined ? delay - elapsed : delay);
		}

	}

	// Set the guid of `wrapper` function to the same of original callback, so
	// it can be removed in jQuery 1.4+ .unbind or .die by using the original
	// callback as a reference.
	if ( $ && $.guid ) {
		wrapper.guid = callback.guid = callback.guid || $.guid++;
	}

	// Return the wrapper function.
	return wrapper;

};

},{"jquery":1}],5:[function(require,module,exports){
(function (global){
/* jshint esnext:true */
'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

var _videoJs = (typeof window !== "undefined" ? window['videojs'] : typeof global !== "undefined" ? global['videojs'] : null);

var _videoJs2 = _interopRequireDefault(_videoJs);

var debounce = require('throttle-debounce').debounce;

// Default options for the plugin.
var defaults = {
  debounceDelay: 200,
  layoutMap: [{ layoutClassName: 'vjs-layout-tiny', width: 2 }, { layoutClassName: 'vjs-layout-x-small', width: 3 }, { layoutClassName: 'vjs-layout-small', width: 4 }, { layoutClassName: 'defaults', width: 5 }]
};

/**
 * Retrieve the outerWidth of an element, including margins
 *
 * @function getElementOuterWidth
 * @param    {Element} el to measure
 * @return   {number} the width of the element in pixels
 */
var _getElementOuterWidth = function _getElementOuterWidth(el) {
  var width = el.offsetWidth;
  var style = getComputedStyle(el);

  width += parseInt(style.marginLeft, 10) + parseInt(style.marginRight, 10);
  return width;
};

/**
 * Retrieve the width an element
 *
 * @function getElementWidth
 * @param    {Element} el to measure
 * @return   {number} the width of the element in pixels
 */
var _getElementWidth = function _getElementWidth(el) {
  return parseInt(getComputedStyle(el).width, 10);
};

/**
 * Check if an element is currently visible.
 *
 * Use this to filter on elements that should be taken into account during calculations.
 *
 * @function isElementVisible
 * @param    {Element} el to test
 * @return   {boolean} true if el is visible
 */
var _isElementVisible = function _isElementVisible(el) {
  return el.offsetWidth > 0 || el.offsetHeight > 0;
};

var dimensionsCheck = function dimensionsCheck() {
  /**
   * Set a layout class on a video-js element
   *
   * @function setLayout
   * @param    {Player} player to apply the layout to
   */
  var setLayout = function setLayout(layouter) {
    var el = layouter.player.el();
    var layoutDefinition = layouter.options.layoutMap[layouter.currentLayout_];

    if (layoutDefinition.layoutClassName !== 'defaults') {
      _videoJs2['default'].addClass(el, layoutDefinition.layoutClassName);
    }
    layouter.options.layoutMap.forEach(function (element, index) {
      if (index !== layouter.currentLayout_ && element.layoutClassName !== 'defaults') {
        _videoJs2['default'].removeClass(el, element.layoutClassName);
      }
    });
  };

  /**
   * Calculate for the giving dimensions which layout class of the layoutMap should be
   * used
   *
   * @function setLayout
   * @param    {Player} player to apply the layout to
   */
  var calculateLayout = function calculateLayout(layouter, playerWidth, controlBarWidth, controlWidth) {
    var layoutMap = layouter.options.layoutMap;

    if (controlBarWidth > playerWidth && layouter.currentLayout_ > 0) {
      // smaller
      layouter.currentLayout_--;
      setLayout(layouter);
      window.setTimeout(dimensionsCheck.bind(layouter), 1);
    } else if (layouter.currentLayout_ < layoutMap.length - 1 && playerWidth >= layoutMap[layouter.currentLayout_ + 1].width * controlWidth) {
      // bigger
      layouter.currentLayout_++;
      setLayout(layouter);
      window.setTimeout(dimensionsCheck.bind(layouter), 1);
    }
  };

  if (!this.el || this.player.usingNativeControls() || !_isElementVisible(this.el.querySelectorAll('.vjs-control-bar')[0])) {
    return;
  }
  var playerWidth = this.getPlayerWidth();
  var controlWidth = this.getControlWidth();
  var controlBarWidth = this.getControlBarWidth();

  if (this.options.calculateLayout) {
    this.options.calculateLayout(this, playerWidth, controlBarWidth, controlWidth);
  } else {
    calculateLayout(this, playerWidth, controlBarWidth, controlWidth);
  }
};

var Layouter = (function () {
  function Layouter(player, options) {
    _classCallCheck(this, Layouter);

    this.player_ = player;
    this.options_ = options;
    this.currentLayout_ = options.layoutMap.length - 1;
    this.debouncedCheckSize_ = debounce(options.debounceDelay, dimensionsCheck);
  }

  /**
   * A video.js plugin.
   *
   * In the plugin function, the value of `this` is a video.js `Player`
   * instance. You cannot rely on the player being in a "ready" state here,
   * depending on how the plugin is invoked. This may or may not be important
   * to you; if not, remove the wait for "ready"!
   *
   * @function responsiveLayout
   * @param    {Object} [options={}]
   *           An object of options left to the plugin author to define.
   */

  _createClass(Layouter, [{
    key: 'ready',
    value: function ready() {
      var _this = this;

      this.player.addClass('vjs-responsive-layout');

      this.windowResizeListener_ = window.addEventListener('resize', function () {
        return _this.debouncedCheckSize_();
      });

      this.player.on(['play', 'resize'], function () {
        return _this.debouncedCheckSize_();
      });
      this.player.on('dispose', function () {
        window.removeEventListener('resize', this.windowResizeListener_);
      });

      // Let's do the first measure
      this.player.trigger('resize');
    }

    /**
     * Retrieve player to which this Layouter object belongs
     *
     * @property player
     * @return   {number} the width of the controlbar in pixels
     */
  }, {
    key: 'getControlWidth',

    /**
     * Retrieve current width of a control in the video.js controlbar
     *
     * This function relies on the presence of the play control. If you
     * mess with it's visibility, things likely will break :)
     *
     * @function getControlWidth
     * @return   {number} the width of the controlbar in pixels
     */
    value: function getControlWidth() {
      return _getElementOuterWidth(this.el.querySelectorAll('.vjs-play-control')[0]);
    }

    /**
     * Retrieve current width of the video.js controlbar
     *
     * @function getControlBarWidth
     * @return   {number} the width of the controlbar in pixels
     */
  }, {
    key: 'getControlBarWidth',
    value: function getControlBarWidth() {
      var controlBarWidth = 0;
      var cbElements = this.el.querySelectorAll('.vjs-control-bar > *');

      Array.from(cbElements).forEach(function (el) {
        if (_isElementVisible(el)) {
          controlBarWidth += _getElementOuterWidth(el);
        }
      });
      return controlBarWidth;
    }

    /**
     * Retrieve current width of the video.js player element
     *
     * @function getPlayerWidth
     * @return   {number} the width of the player in pixels
     */
  }, {
    key: 'getPlayerWidth',
    value: function getPlayerWidth() {
      return _getElementWidth(this.el);
    }

    /**
     * Retrieve the outerWidth of an element, including margins
     *
     * @function outerWidth
     * @param    {Element} el to measure
     * @return   {number} the width of the element in pixels
     */
  }, {
    key: 'player',
    get: function get() {
      return this.player_;
    }
  }, {
    key: 'el',
    get: function get() {
      return this.player_.el();
    }
  }, {
    key: 'options',
    get: function get() {
      return this.options_;
    }
  }], [{
    key: 'getElementOuterWidth',
    value: function getElementOuterWidth(el) {
      return _getElementOuterWidth(el);
    }

    /**
     * Retrieve the width an element
     *
     * @function getElementWidth
     * @param    {Element} el to measure
     * @return   {number} the width of the element in pixels
     */
  }, {
    key: 'getElementWidth',
    value: function getElementWidth(el) {
      return _getElementWidth(el);
    }

    /**
     * Check if an element is currently visible.
     *
     * Use this to filter on elements that should be taken into account during calculations.
     *
     * @function isElementVisible
     * @param    {Element} el to test
     * @return   {boolean} true if el is visible
     */
  }, {
    key: 'isElementVisible',
    value: function isElementVisible(el) {
      return _isElementVisible(el);
    }
  }]);

  return Layouter;
})();

var responsiveLayout = function responsiveLayout(options) {
  var layout = new Layouter(this, _videoJs2['default'].mergeOptions(defaults, options));

  this.ready(function () {
    layout.ready();
  });
};

// Register the plugin with video.js.
_videoJs2['default'].plugin('responsiveLayout', responsiveLayout);

exports['default'] = responsiveLayout;
module.exports = exports['default'];
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"throttle-debounce":3}]},{},[5])(5)
});