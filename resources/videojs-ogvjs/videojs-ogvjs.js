/**
 * Forked from:
 * videojs-ogvjs
 * @version 1.3.1
 * @copyright 2016 Derk-Jan Hartman
 * @license (MIT OR Apache-2.0)
 */
(function (){
'use strict';

var Tech = videojs.getComponent('Tech');

/**
 * Ogvjs Media Controller - Wrapper for Ogvjs Media API
 *
 * @param {Object=} options Object of option names and values
 * @param {Function=} ready Ready callback function
 * @extends Tech
 * @class Ogvjs
 */

function Ogvjs(options, ready) {
  // Set initial state of player
  var _this = Reflect.construct(Tech, [options, ready], Ogvjs);

  _this.el_.src = options.source.src;
  Ogvjs.setIfAvailable(_this.el_, 'autoplay', options.autoplay);
  Ogvjs.setIfAvailable(_this.el_, 'loop', options.loop);
  Ogvjs.setIfAvailable(_this.el_, 'poster', options.poster);
  Ogvjs.setIfAvailable(_this.el_, 'preload', options.preload);

  _this.triggerReady();
  return _this;
}

Ogvjs.prototype = Object.create(Tech.prototype);


/**
 * Create the component's DOM element
 *
 * @return {Element}
 * @method createEl
 */


Ogvjs.prototype.createEl = function createEl() {
  var options = this.options_;

  if (options.base) {
    OGVLoader.base = options.base;
  } else {
    throw new Error('Please specify the base for the ogv.js library');
  }

  var el = new OGVPlayer(options);

  el.className += ' vjs-tech';
  options.tag = el;

  return el;
};


/**
 * Play for Ogvjs tech
 *
 * @method play
 */


Ogvjs.prototype.play = function play() {
  this.el_.play();
};

/**
 * Pause for Ogvjs tech
 *
 * @method pause
 */


Ogvjs.prototype.pause = function pause() {
  this.el_.pause();
};

/**
 * Paused for Ogvjs tech
 *
 * @return {Boolean}
 * @method paused
 */


Ogvjs.prototype.paused = function paused() {
  return this.el_.paused;
};

/**
 * Get current time
 *
 * @return {Number}
 * @method currentTime
 */


Ogvjs.prototype.currentTime = function currentTime() {
  return this.el_.currentTime;
};

/**
 * Set current time
 *
 * @param {Number} seconds Current time of video
 * @method setCurrentTime
 */


Ogvjs.prototype.setCurrentTime = function setCurrentTime(seconds) {
  try {
    this.el_.currentTime = seconds;
  } catch (e) {
    videojs.log(e, 'Video is not ready. (Video.js)');
  }
};

/**
 * Get duration
 *
 * @return {Number}
 * @method duration
 */


Ogvjs.prototype.duration = function duration() {
  return this.el_.duration || 0;
};

/**
 * Get a TimeRange object that represents the intersection
 * of the time ranges for which the user agent has all
 * relevant media
 *
 * @return {TimeRangeObject}
 * @method buffered
 */


Ogvjs.prototype.buffered = function buffered() {
  return this.el_.buffered;
};

/**
 * Get volume level
 *
 * @return {Number}
 * @method volume
 */


Ogvjs.prototype.volume = function volume() {
  return this.el_.volume;
};

/**
 * Set volume level
 *
 * @param {Number} percentAsDecimal Volume percent as a decimal
 * @method setVolume
 */


Ogvjs.prototype.setVolume = function setVolume(percentAsDecimal) {
  this.el_.volume = percentAsDecimal;
};

/**
 * Get if muted
 *
 * @return {Boolean}
 * @method muted
 */


Ogvjs.prototype.muted = function muted() {
  return this.el_.muted;
};

/**
 * Set muted
 *
 * @param {Boolean} If player is to be muted or note
 * @method setMuted
 */


Ogvjs.prototype.setMuted = function setMuted(muted) {
  this.el_.muted = !!muted;
};

/**
 * Get player width
 *
 * @return {Number}
 * @method width
 */


Ogvjs.prototype.width = function width() {
  return this.el_.offsetWidth;
};

/**
 * Get player height
 *
 * @return {Number}
 * @method height
 */


Ogvjs.prototype.height = function height() {
  return this.el_.offsetHeight;
};

/**
 * Get/set video
 *
 * @param {Object=} src Source object
 * @return {Object}
 * @method src
 */


Ogvjs.prototype.src = function src(_src) {
  if (typeof _src === 'undefined') {
    return this.el_.src;
  }
  // Setting src through `src` instead of `setSrc` will be deprecated
  this.setSrc(_src);
};

/**
 * Set video
 *
 * @param {Object} src Source object
 * @deprecated
 * @method setSrc
 */


Ogvjs.prototype.setSrc = function setSrc(src) {
  this.el_.src = src;
};

/**
 * Load media into player
 *
 * @method load
 */


Ogvjs.prototype.load = function load() {
  this.el_.load();
};

/**
 * Get current source
 *
 * @return {Object}
 * @method currentSrc
 */


Ogvjs.prototype.currentSrc = function currentSrc() {
  if (this.currentSource_) {
    return this.currentSource_.src;
  }
  return this.el_.currentSrc;
};

/**
 * Get poster
 *
 * @return {String}
 * @method poster
 */


Ogvjs.prototype.poster = function poster() {
  return this.el_.poster;
};

/**
 * Set poster
 *
 * @param {String} val URL to poster image
 * @method
 */


Ogvjs.prototype.setPoster = function setPoster(val) {
  this.el_.poster = val;
};

/**
 * Get preload attribute
 *
 * @return {String}
 * @method preload
 */


Ogvjs.prototype.preload = function preload() {
  return this.el_.preload || 'none';
};

/**
 * Set preload attribute
 *
 * @param {String} val Value for preload attribute
 * @method setPreload
 */


Ogvjs.prototype.setPreload = function setPreload(val) {
  if (this.el_.hasOwnProperty('preload')) {
    this.el_.preload = val;
  }
};

/**
 * Get autoplay attribute
 *
 * @return {Boolean}
 * @method autoplay
 */


Ogvjs.prototype.autoplay = function autoplay() {
  return this.el_.autoplay || false;
};

/**
 * Set autoplay attribute
 *
 * @param {Boolean} val Value for preload attribute
 * @method setAutoplay
 */


Ogvjs.prototype.setAutoplay = function setAutoplay(val) {
  if (this.el_.hasOwnProperty('autoplay')) {
    this.el_.autoplay = !!val;
    return;
  }
};

/**
 * Get controls attribute
 *
 * @return {Boolean}
 * @method controls
 */


Ogvjs.prototype.controls = function controls() {
  return this.el_controls || false;
};

/**
 * Set controls attribute
 *
 * @param {Boolean} val Value for controls attribute
 * @method setControls
 */


Ogvjs.prototype.setControls = function setControls(val) {
  if (this.el_.hasOwnProperty('controls')) {
    this.el_.controls = !!val;
  }
};

/**
 * Get loop attribute
 *
 * @return {Boolean}
 * @method loop
 */


Ogvjs.prototype.loop = function loop() {
  return this.el_.loop || false;
};

/**
 * Set loop attribute
 *
 * @param {Boolean} val Value for loop attribute
 * @method setLoop
 */


Ogvjs.prototype.setLoop = function setLoop(val) {
  if (this.el_.hasOwnProperty('loop')) {
    this.el_.loop = !!val;
  }
};

/**
 * Get error value
 *
 * @return {String}
 * @method error
 */


Ogvjs.prototype.error = function error() {
  return this.el_.error;
};

/**
 * Get whether or not the player is in the "seeking" state
 *
 * @return {Boolean}
 * @method seeking
 */


Ogvjs.prototype.seeking = function seeking() {
  return this.el_.seeking;
};

/**
 * Get a TimeRanges object that represents the
 * ranges of the media resource to which it is possible
 * for the user agent to seek.
 *
 * @return {TimeRangeObject}
 * @method seekable
 */


Ogvjs.prototype.seekable = function seekable() {
  return this.el_.seekable;
};

/**
 * Get if video ended
 *
 * @return {Boolean}
 * @method ended
 */


Ogvjs.prototype.ended = function ended() {
  return this.el_.ended;
};

/**
 * Get the value of the muted content attribute
 * This attribute has no dynamic effect, it only
 * controls the default state of the element
 *
 * @return {Boolean}
 * @method defaultMuted
 */


Ogvjs.prototype.defaultMuted = function defaultMuted() {
  return this.el_.defaultMuted || false;
};

/**
 * Get desired speed at which the media resource is to play
 *
 * @return {Number}
 * @method playbackRate
 */


Ogvjs.prototype.playbackRate = function playbackRate() {
  return this.el_.playbackRate || 1;
};

/**
 * Returns a TimeRanges object that represents the ranges of the
 * media resource that the user agent has played.
 * @return {TimeRangeObject} the range of points on the media
 * timeline that has been reached through normal playback
 * @see https://html.spec.whatwg.org/multipage/embedded-content.html#dom-media-played
 */


Ogvjs.prototype.played = function played() {
  return this.el_.played;
};

/**
 * Set desired speed at which the media resource is to play
 *
 * @param {Number} val Speed at which the media resource is to play
 * @method setPlaybackRate
 */


Ogvjs.prototype.setPlaybackRate = function setPlaybackRate(val) {
  if (this.el_.hasOwnProperty('playbackRate')) {
    this.el_.playbackRate = val;
  }
};

/**
 * Get the current state of network activity for the element, from
 * the list below
 * NETWORK_EMPTY (numeric value 0)
 * NETWORK_IDLE (numeric value 1)
 * NETWORK_LOADING (numeric value 2)
 * NETWORK_NO_SOURCE (numeric value 3)
 *
 * @return {Number}
 * @method networkState
 */


Ogvjs.prototype.networkState = function networkState() {
  return this.el_.networkState;
};

/**
 * Get a value that expresses the current state of the element
 * with respect to rendering the current playback position, from
 * the codes in the list below
 * HAVE_NOTHING (numeric value 0)
 * HAVE_METADATA (numeric value 1)
 * HAVE_CURRENT_DATA (numeric value 2)
 * HAVE_FUTURE_DATA (numeric value 3)
 * HAVE_ENOUGH_DATA (numeric value 4)
 *
 * @return {Number}
 * @method readyState
 */


Ogvjs.prototype.readyState = function readyState() {
  return this.el_.readyState;
};

/**
 * Get width of video
 *
 * @return {Number}
 * @method videoWidth
 */


Ogvjs.prototype.videoWidth = function videoWidth() {
  return this.el_.videoWidth;
};

/**
 * Get height of video
 *
 * @return {Number}
 * @method videoHeight
 */


Ogvjs.prototype.videoHeight = function videoHeight() {
  return this.el_.videoHeight;
};

/**
 * The technology has no native fullscreen
 * This is important on iOS, where we have to fallback to
 * fullWindow mode due to lack of HTML5 fullscreen api
 */


Ogvjs.prototype.supportsFullScreen = function supportsFullScreen() {
  return false;
};

/*
 * Only set a value on an element if it has that property
 *
 * @param {Element} el
 * @param {String} name
 * @param value
 */


Ogvjs.setIfAvailable = function (el, name, value) {
  if (el.hasOwnProperty(name)) {
    el[name] = value;
  }
};

/*
 * Check if Ogvjs video is supported by this browser/device
 *
 * @return {Boolean}
 */
Ogvjs.isSupported = function () {
  return OGVCompat.supported('OGVPlayer');
};

/*
 * Determine if the specified media type can be played back
 * by the Tech
 *
 * @param  {String} type  A media type description
 * @return {String}         'probably', 'maybe', or '' (empty string)
 */
Ogvjs.canPlayType = function (type) {
  if (Ogvjs.isSupported()) {
    var p = new OGVPlayer();
    return p.canPlayType(type);
  }
  return false;
};

/*
 * Check if the tech can support the given source
 * @param  {Object} srcObj  The source object
 * @return {String}         'probably', 'maybe', or '' (empty string)
 */
Ogvjs.canPlaySource = function (srcObj) {
  return Ogvjs.canPlayType(srcObj.type);
};

/*
 * Check if the volume can be changed in this browser/device.
 * Volume cannot be changed in a lot of mobile devices.
 * Specifically, it can't be changed from 1 on iOS.
 *
 * @return {Boolean}
 */
Ogvjs.canControlVolume = function () {
  return true;
};

/*
 * Check if playbackRate is supported in this browser/device.
 *
 * @return {Number} [description]
 */
Ogvjs.canControlPlaybackRate = function () {
  return true;
};

/*
 * Check to see if native text tracks are supported by this browser/device
 *
 * @return {Boolean}
 */
Ogvjs.supportsNativeTextTracks = function () {
  return false;
};

/**
 * An array of events available on the Ogvjs tech.
 *
 * @private
 * @type {Array}
 */
Ogvjs.Events = ['loadstart', 'suspend', 'abort', 'error', 'emptied', 'stalled', 'loadedmetadata', 'loadeddata', 'canplay', 'canplaythrough', 'playing', 'waiting', 'seeking', 'seeked', 'ended', 'durationchange', 'timeupdate', 'progress', 'play', 'pause', 'ratechange', 'volumechange'];

/*
 * Set the tech's volume control support status
 *
 * @type {Boolean}
 */
Ogvjs.prototype.featuresVolumeControl = Ogvjs.canControlVolume();

/*
 * Set the tech's playbackRate support status
 *
 * @type {Boolean}
 */
Ogvjs.prototype.featuresPlaybackRate = Ogvjs.canControlPlaybackRate();

/*
 * Set the the tech's fullscreen resize support status.
 * HTML video is able to automatically resize when going to fullscreen.
 * (No longer appears to be used. Can probably be removed.)
 */
Ogvjs.prototype.featuresFullscreenResize = true;

/*
 * Set the tech's progress event support status
 * (this disables the manual progress events of the Tech)
 */
Ogvjs.prototype.featuresProgressEvents = true;

/*
 * Sets the tech's status on native text track support
 *
 * @type {Boolean}
 */
Ogvjs.prototype.featuresNativeTextTracks = Ogvjs.supportsNativeTextTracks();

Tech.registerTech('Ogvjs', Ogvjs);

})();