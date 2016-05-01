( function( mw, $ ) { "use strict";

var support = mw.OgvJsSupport;

mw.EmbedPlayerOgvJs = {

	// Instance name:
	instanceOf: 'OgvJs',

	// Supported feature set of the OGVPlayer widget:
	supports: {
		'playHead' : true,
		'pause' : true,
		'stop' : true,
		'fullscreen' : true,
		'sourceSwitch': true,
		'timeDisplay' : true,
		'volumeControl' : true,
		'overlays': true,
		'timedText': true
	},

	/**
	 * Perform setup in response to a play start command.
	 * This means loading the code asynchronously if needed,
	 * and enabling web audio for iOS Safari inside the event
	 * handler.
	 *
	 * @return jQuery.Deferred
	 */
	_ogvJsPreInit: function() {
		if( mw.isIOS() ) {
			this._initializeAudioForiOS();
		}
		return support.loadOgvJs();
	},

	/**
	 * Actually initialize the player.
	 *
	 * @return OGVPlayer
	 */
	_ogvJsInit: function() {
		var options = {};
		if ( this._iOSAudioContext ) {
			// Reuse the audio context we opened earlier
			options.audioContext = this._iOSAudioContext;
		}
		return new OGVPlayer( options );
	},

	_iOSAudioContext: undefined,

	_initializeAudioForiOS: function() {
		// iOS Safari Web Audio API must be initialized from an input event handler
		if ( this._iOSAudioContext ) {
			return;
		}
		this._iOSAudioContext = support.initAudioContext();
	},

	/**
	 * Output the the embed html
	 */
	embedPlayerHTML: function (optionalCallback) {

		$( this )
			.empty()
			.append( $.createSpinner( {
				size: 'large',
				type: 'block'
			} ) );

		var _this = this;
		if( mw.isIOS() ) {
			_this._initializeAudioForiOS();
		}
		support.loadOgvJs().done( function() {

			var player = _this._ogvJsInit();
			player.id = _this.pid;
			player.style.width = '100%';
			player.style.height = '100%';
			player.src = _this.getSrc();
			if ( _this.getDuration() ) {
				player.durationHint = parseFloat( _this.getDuration() );
			}
			player.addEventListener('ended', function() {
				_this.onClipDone();
			});

			// simulate timeupdate events, needed for subtitles
			// @todo switch this to native timeupdate event when available upstream
			var lastTime = 0,
				timeupdateInterval = 0.25;
			player.addEventListener( 'framecallback', function( event ) {
				var player = _this.getPlayerElement(),
					now = player ? player.currentTime : lastTime;
				// Don't spam time updates on every frame
				if ( Math.abs( now - lastTime ) >= timeupdateInterval ) {
					lastTime = now;
					$( _this ).trigger( 'timeupdate', [event, _this.id] );
				}
			});

			$( _this ).empty().append( player );
			player.play();

			// Start the monitor:
			_this.monitor();

			if ( optionalCallback ) {
				optionalCallback();
			}
		});
	},

	/**
	 * Get the embed player time
	 */
	getPlayerElementTime: function() {
		this.getPlayerElement();
		var currentTime = 0;
		if ( this.playerElement ) {
			currentTime = this.playerElement.currentTime;
		} else {
			mw.log( "EmbedPlayerOgvJs:: Could not find playerElement" );
		}
		return currentTime;
	},

	/**
	 * Update the playerElement instance with a pointer to the embed object
	 */
	getPlayerElement: function() {
		// this.pid is in the form 'pid_mwe_player_<number>'; inherited from mw.EmbedPlayer.js
		var $el = $( '#' + this.pid );
		if( !$el.length ) {
			return false;
		}
		this.playerElement = $el.get( 0 );
		return this.playerElement;
	},

	/**
	 * Issue the doPlay request to the playerElement
	 *	calls parent_play to update interface
	 */
	play: function() {
		this.getPlayerElement();
		this.parent_play();
		if ( this.playerElement ) {
			this.playerElement.play();
			// Restart the monitor if on second playthrough
			this.monitor();
		}
	},

	/**
	 * Pause playback
	 * 	calls parent_pause to update interface
	 */
	pause: function() {
		this.getPlayerElement();
		// Update the interface
		this.parent_pause();
		// Call the pause function if it exists:
		if ( this.playerElement ) {
			this.playerElement.pause();
		}
	},

	/**
	 * Switch the source!
	 * For simplicity we just replace the player here.
	 */
	playerSwitchSource: function( source, switchCallback, doneCallback ){
		var _this = this;
		var src = source.getSrc();
		var vid = this.getPlayerElement();
		if ( typeof vid.stop !== 'undefined' ) {
			vid.stop();
		}

		switchCallback();

		// Currently have to tear down the player and make a new one
		this.embedPlayerHTML( doneCallback );
	},

	/**
	* Seek in the ogg stream
	* @param {Float} percentage Percentage to seek into the stream
	*/
	seek: function( percentage ) {
		this.setCurrentTime( percentage * parseFloat( this.getDuration() ) );
	},

	setCurrentTime: function( time, callback ) {
		this.getPlayerElement();

		if ( this.playerElement ) {
			this.playerElement.currentTime = time;
		}

		this.currentTime = time;
		this.previousTime = time; // prevent weird double-seek. MwEmbedPlyer is weird!

		// Run the onSeeking interface update
		this.controlBuilder.onSeek();
		// @todo add proper events upstream
		if( this.seeking ){
			this.seeking = false;
			$( this ).trigger( 'seeked' );
		}
		if ( $.isFunction( callback ) ) {
			callback();
		}
	},

	/**
	 * Toggle the Mute
	 * calls parent_toggleMute to update the interface
	 */
	toggleMute: function() {
		this.parent_toggleMute();
		this.getPlayerElement();
		if ( this.playerElement )
			this.playerElement.muted = this.muted;
	},

	/**
	 * Update Volume
	 *
	 * @param {Float} percent Value between 0 and 1 to set audio volume
	 */
	setPlayerElementVolume : function( percent ) {
		if ( this.getPlayerElement() ) {
			// Disable mute if positive volume
			if( percent != 0 ) {
				this.playerElement.muted = false;
			}
			this.playerElement.volume = percent;
		}
	},

	/**
	 * get Volume
	 *
	 * @return {Float}
	 * 	Audio volume between 0 and 1.
	 */
	getPlayerElementVolume: function() {
		if ( this.getPlayerElement() ) {
			return this.playerElement.volume;
		}
	},
	/**
	 * get the native muted state
	 */
	getPlayerElementMuted: function(){
		if ( this.getPlayerElement() ) {
			return this.playerElement.muted;
		}
	},

};

} )( mediaWiki, jQuery );
