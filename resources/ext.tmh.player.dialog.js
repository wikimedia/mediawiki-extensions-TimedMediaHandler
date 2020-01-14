( function () {

	function MediaDialog( config ) {
		MediaDialog.super.call( this, config );
		this.$video = config.$video;
	}
	OO.inheritClass( MediaDialog, OO.ui.ProcessDialog );
	MediaDialog.static.name = 'tmhMediaDialog';
	MediaDialog.static.actions = [
		{ icon: 'close', flags: 'safe' }
	];

	MediaDialog.prototype.initialize = function () {
		MediaDialog.super.prototype.initialize.call( this );

		this.$element.addClass( 'mw-tmh-media-dialog' );

		this.content = new OO.ui.PanelLayout( {
			padded: false,
			expanded: true
		} );

		this.$video.css( {
			display: 'block',
			position: 'absolute',
			top: '0',
			bottom: '0',
			width: '100%',
			height: '100%',
			background: 'black' // VideoJS has a solid black background, so avoid flicker.
		} );

		this.content.$element.append( this.$video );
		this.$body.append( this.content.$element );
	};

	MediaDialog.prototype.getBodyHeight = function () {
		// Fixed 16:10 ratio for the dialog. This may change.
		return Math.round( this.content.$element.width() * 10 / 16 );
	};

	MediaDialog.prototype.getActionProcess = function ( action ) {
		var dialog = this;
		if ( action ) {
			return new OO.ui.Process( function () {
				dialog.close( { action: action } );
			} );
		}
		return MediaDialog.parent.prototype.getActionProcess.call( this, action );
	};

	MediaDialog.prototype.play = function () {
		var indicator = new OO.ui.ProgressBarWidget( {
			progress: false
		} );
		this.content.$element.append( indicator.$element );

		// We might cause a delayed load of videojs here.
		this.videojsDeferred = this.$video.transformVideoPlayer();

		// Start playback when ready...
		this.videojsDeferred.then( function ( $videojs ) {
			var player = $videojs[ 0 ];
			player.ready( function () {
				// Use a setTimeout to ensure all ready callbacks have run before
				// we start playback. This is important for the source selector
				// plugin, which may change sources before playback begins.
				//
				// This is used instead of an event like `canplay` or `loadeddata`
				// because some versions of EdgeHTML don't fire these events.
				// Support: Edge 18
				setTimeout( function () {
					$( indicator.$element ).detach();
					player.play();
				}, 0 );
			} );
		} );
	};

	MediaDialog.prototype.stop = function () {
		this.videojsDeferred.then( function ( $videojs ) {
			$videojs.each( function () {
				this.pause();
			} );
			$.disposeDetachedPlayers();
		} );
	};

	function showVideoPlayerDialog() {
		var $video = this.clone(),
			isAudio = $video[ 0 ].nodeName.toLowerCase() === 'audio';
		if ( isAudio ) {
			$video.attr( 'poster', mw.config.get( 'wgExtensionAssetsPath' ) +
				'/TimedMediaHandler/resources/poster-audio.svg' );
		}
		return $.Deferred( function ( deferred ) {
			var NS_FILE = mw.config.get( 'wgNamespaceIds' ).file,
				windowManager = OO.ui.getWindowManager(),
				dialog = new MediaDialog( {
					size: isAudio ? 'medium' : 'larger',
					$video: $video
				} ),
				win;

			$( document.body ).append( windowManager.$element );
			windowManager.addWindows( [ dialog ] );
			win = windowManager.openWindow( dialog, {
				title: ( new mw.Title( $video.data( 'mwtitle' ), NS_FILE ) ).getMainText()
			} );

			win.opened.then( function () {
				dialog.play();
			} );

			win.closed.then( function () {
				dialog.stop();
				deferred.resolve();
			} );
		} );
	}
	$.fn.showVideoPlayerDialog = showVideoPlayerDialog;

}() );
