/**
 * The Core timed Text interface object
 *
 * handles class mappings for:
 * 	menu display ( jquery.ui themeable )
 * 	timed text loading request
 *  timed text edit requests
 * 	timed text search & seek interface ( version 2 )
 *
 * @author: Michael Dale
 *
 */

( function ( mw, $ ) {
	'use strict';

	// Merge in timed text related attributes:
	mw.mergeConfig( 'EmbedPlayer.SourceAttributes', [
		'srclang',
		'kind',
		'label'
	] );

	/**
	 * Timed Text Object
	 * @param {HTMLElement} embedPlayer Host player for timedText interfaces
	 * @return {mw.TimedText}
	 */
	mw.TimedText = function ( embedPlayer ) {
		return this.init( embedPlayer );
	};

	mw.TimedText.prototype = {

		/**
		* Preferences config order is presently:
		* 1) user cookie
		* 2) defaults provided in this config var:
		*/
		config: {
			// Layout for basic "timedText" type can be 'ontop', 'off', 'below'
			layout: 'ontop',

			// Set the default local ( should be grabbed from the browser )
			userLanguage: mw.config.get( 'wgUserLanguage' ) || 'en',

			// Set the default kind of timedText to display ( un-categorized timed-text is by default "subtitles" )
			userKind: 'subtitles'
		},

		// The default display mode is 'ontop'
		defaultDisplayMode: 'ontop',

		// Save last layout mode
		lastLayout: 'ontop',

		// The bind prefix:
		bindPostFix: '.timedText',

		// Default options are empty
		options: {},

		/**
		 * The list of enabled sources
		 */
		enabledSources: [],

		// First loading flag - To set the layout at first load
		firstLoad: true,

		/**
		 * The current language key
		 */
		currentLangKey: null,

		/**
		 * The direction of the current language
		 */
		currentLangDir: null,

		/**
		 * Stores the last text string per kind to avoid dom checks for updated text
		 */
		prevText: [],

		/**
		* Text sources ( a set of textSource objects )
		*/
		textSources: [],

		/**
		* Valid "Track" categories
		*/
		validCategoriesKeys: [
			'CC',
			'SUB',
			'TAD',
			'KTV',
			'TIK',
			'AR',
			'NB',
			'META',
			'TRX',
			'LRC',
			'LIN',
			'CUE'
		],

		/**
		 * @constructor
		 * @param {Object} embedPlayer Host player for timedText interfaces
		 * @return {mw.TimedText}
		 */
		init: function ( embedPlayer ) {
			var preferenceConfig,
				self = this;
			mw.log( 'TimedText: init() ' );
			this.embedPlayer = embedPlayer;
			// don't display captions on native player:
			if ( embedPlayer.useNativePlayerControls() ) {
				return this;
			}

			// Load user preferences config:
			preferenceConfig = $.cookie( 'TimedText.Preferences' );
			if ( preferenceConfig !== 'false' && preferenceConfig !== null ) {
				this.config = JSON.parse( preferenceConfig );
			}
			// remove any old bindings on change media:
			$( this.embedPlayer ).on( 'onChangeMedia' + this.bindPostFix, function () {
				self.destroy();
			} );

			// Remove any old bindings before we add the current bindings:
			self.destroy();

			// Add player bindings
			self.addPlayerBindings();
			return this;
		},
		destroy: function () {
			// remove any old player bindings;
			$( this.embedPlayer ).off( this.bindPostFix );
			// Clear out enabled sources:
			this.enabledSources = [];
			// Clear out text sources:
			this.textSources = [];
		},
		/**
		 * Add timed text related player bindings
		 */
		addPlayerBindings: function () {
			var self = this,
				embedPlayer = this.embedPlayer;

			// Check for timed text support:
			self.addInterface();

			$( embedPlayer ).on( 'timeupdate' + this.bindPostFix, function ( event, jEvent, id ) {
				// regain scope
				self = $( '#' + id )[ 0 ].timedText;
				// monitor text updates
				self.monitor();
			} );

			$( embedPlayer ).on( 'firstPlay' + this.bindPostFix, function ( event, id ) {
				// regain scope
				self = $( '#' + id )[ 0 ].timedText;
				// Will load and setup timedText sources (if not loaded already loaded )
				self.setupTextSources();
				// Hide the caption menu if presently displayed
				$( '#textMenuContainer_' + self.embedPlayer.id ).hide();
			} );

			// Re-Initialize when changing media
			$( embedPlayer ).on( 'onChangeMedia' + this.bindPostFix, function () {
				self.destroy();
				self.updateLayout();
				self.setupTextSources();
				$( '#textMenuContainer_' + embedPlayer.id ).hide();
			} );

			// Resize the timed text font size per window width
			$( embedPlayer ).on( 'onCloseFullScreen' + this.bindPostFix + ' onOpenFullScreen' + this.bindPostFix, function () {
				// Check if we are in fullscreen or not, if so add an additional bottom offset of
				// double the default bottom padding.
				var textOffset = self.embedPlayer.controlBuilder.inFullScreen ?
						mw.config.get( 'TimedText.BottomPadding' ) * 2 :
						mw.config.get( 'TimedText.BottomPadding' ),
					textCss = self.getInterfaceSizeTextCss( {
						width: embedPlayer.getInterface().width(),
						height: embedPlayer.getInterface().height()
					} );

				mw.log( 'TimedText::set text size for: : ' + embedPlayer.getInterface().width() + ' = ' + textCss[ 'font-size' ] );
				if ( embedPlayer.controlBuilder.isOverlayControls() && !embedPlayer.getInterface().find( '.control-bar' ).is( ':hidden' ) ) {
					textOffset += self.embedPlayer.controlBuilder.getHeight();
				}
				embedPlayer.getInterface().find( '.track' )
					.css( textCss )
					.css( {
					// Get the text size scale then set it to control bar height + TimedText.BottomPadding;
						bottom: textOffset + 'px'
					} );
			} );

			// Update the timed text size
			$( embedPlayer ).on( 'updateLayout' + this.bindPostFix, function () {
				// If the the player resize action is an animation, animate text resize,
				// else instantly adjust the css.
				var textCss = self.getInterfaceSizeTextCss( {
					width: embedPlayer.getPlayerWidth(),
					height: embedPlayer.getPlayerHeight()
				} );
				mw.log( 'TimedText::updateLayout: ' + textCss[ 'font-size' ] );
				embedPlayer.getInterface().find( '.track' ).css( textCss );
			} );

			// Setup display binding
			$( embedPlayer ).on( 'onShowControlBar' + this.bindPostFix, function ( event, layout, id ) {
				// update embedPlayer ref:
				var embedPlayer = $( '#' + id )[ 0 ];
				if ( embedPlayer.controlBuilder.isOverlayControls() ) {
					// Move the text track if present
					embedPlayer.getInterface().find( '.track' )
						.stop()
						.animate( layout, 'fast' );
				}
			} );

			$( embedPlayer ).on( 'onHideControlBar' + this.bindPostFix, function ( event, layout, id ) {
				var embedPlayer = $( '#' + id )[ 0 ];
				if ( embedPlayer.controlBuilder.isOverlayControls() ) {
					// Move the text track down if present
					embedPlayer.getInterface().find( '.track' )
						.stop()
						.animate( layout, 'fast' );
				}
			} );

			$( embedPlayer ).on( 'AdSupport_StartAdPlayback' + this.bindPostFix, function () {
				var $textButton;
				if ( $( '#textMenuContainer_' + embedPlayer.id ).length ) {
					$( '#textMenuContainer_' + embedPlayer.id ).hide();
				}
				$textButton = embedPlayer.getInterface().find( '.timed-text' );
				if ( $textButton.length ) {
					$textButton.off( 'click' );
				}
				self.lastLayout = self.getLayoutMode();
				self.setLayoutMode( 'off' );
			} );

			$( embedPlayer ).on( 'AdSupport_EndAdPlayback' + this.bindPostFix, function () {
				var $textButton = embedPlayer.getInterface().find( '.timed-text' );
				if ( $textButton.length ) {
					self.bindTextButton( $textButton );
				}
				self.setLayoutMode( self.lastLayout );
			} );

		},
		addInterface: function () {
			var self = this;
			// By default we include a button in the control bar.
			$( self.embedPlayer ).on( 'addControlBarComponent' + this.bindPostFix, function ( event, controlBar ) {
				if ( controlBar.supportedComponents.timedText !== false &&
					self.includeCaptionButton() ) {
					controlBar.supportedComponents.timedText = true;
					controlBar.components.timedText = self.getTimedTextButton();
				}
			} );
		},
		includeCaptionButton: function () {
			return mw.config.get( 'TimedText.ShowInterface' ) === 'always' ||
				this.embedPlayer.getTextTracks().length;
		},
		/**
		 * Get the current language key
		 * @return {string}
		 */
		getCurrentLangKey: function () {
			return this.currentLangKey;
		},
		/**
		 * Get the current language direction
		 * @return {string}
		 */
		getCurrentLangDir: function () {
			if ( !this.currentLangDir ) {
				this.currentLangDir = this.getSourceByLanguage( this.getCurrentLangKey() ).dir;
			}
			return this.currentLangDir;
		},

		/**
		 * The timed text button to be added to the interface
		 * @return {Object}
		 */
		getTimedTextButton: function () {
			var self = this;
			/**
			* The closed captions button
			*/
			return {
				w: 30,
				position: 6.9,
				o: function () {
					var $textButton = $( '<div>' )
						.attr( 'title', mw.msg( 'mwe-embedplayer-timed_text' ) )
						.addClass( 'ui-state-default ui-corner-all ui-icon_link rButton timed-text' )
						.append(
							$( '<span>' )
								.addClass( 'ui-icon ui-icon-comment' )
						)
						// Captions binding:
						.buttonHover();
					self.bindTextButton( $textButton );
					return $textButton;

				}
			};
		},
		bindTextButton: function ( $textButton ) {
			var self = this;
			$textButton.off( 'click.textMenu' ).on( 'click.textMenu', function () {
				self.showTextMenu();
				return true;
			} );
		},

		/**
		* Get the fullscreen text css
		* @param {Object} size Width and height
		* @return {Object}
		*/
		getInterfaceSizeTextCss: function ( size ) {
			// mw.log(' win size is: ' + $( window ).width() + ' ts: ' + textSize );
			return {
				'font-size': this.getInterfaceSizePercent( size ) + '%'
			};
		},

		/**
		* Show the text interface library and show the text interface near the player.
		*/
		showTextMenu: function () {
			var $menuButton,
				embedPlayer = this.embedPlayer,
				loc = embedPlayer.getInterface().find( '.rButton.timed-text' ).offset();
			mw.log( 'TimedText::showTextMenu:: ' + embedPlayer.id + ' location: ', loc );
			// TODO: Fix menu animation
			$menuButton = this.embedPlayer.getInterface().find( '.timed-text' );
			// Check if a menu has already been built out for the menu button:
			if ( $menuButton[ 0 ].m ) {
				$menuButton.embedMenu( 'show' );
			} else {
				// Bind the text menu:
				this.buildMenu( true );
			}
		},
		getTextMenuContainer: function () {
			var textMenuId = 'textMenuContainer_' + this.embedPlayer.id;
			if ( !$( '#' + textMenuId ).length ) {
				// Setup the menu:
				this.embedPlayer.getInterface().append(
					$( '<div>' )
						.addClass( 'ui-widget ui-widget-content ui-corner-all' )
						.attr( 'id', textMenuId )
						.css( {
							position: 'absolute',
							height: '180px',
							width: '180px',
							'font-size': '12px',
							display: 'none',
							overflow: 'auto'
						} )

				);
			}
			return $( '#' + textMenuId );
		},
		/**
		 * Gets a text size percent relative to about 30 columns of text for 400
		 * pixel wide player, at 100% text size.
		 *
		 * @param {Object} size The size of the target player area width and height
		 * @return {number}
		 */
		getInterfaceSizePercent: function ( size ) {
			// This is a ugly hack we should read "original player size" and set based
			// on some standard ish normal 31 columns 15 rows
			var textSize,
				sizeFactor = 4;
			if ( size.height / size.width < 0.7 ) {
				sizeFactor = 6;
			}
			textSize = size.width / sizeFactor;
			if ( textSize < 95 ) {
				textSize = 95;
			}
			if ( textSize > 150 ) {
				textSize = 150;
			}
			return textSize;
		},

		/**
		* Setups available text sources
		*   loads text sources
		* 	auto-selects a source based on the user language
		* @param {Function} callback Function to be called once text sources are setup.
		*/
		setupTextSources: function ( callback ) {
			var self = this;
			mw.log( 'TimedText::setupTextSources' );
			// Load textSources
			self.loadTextSources( function () {
				// Enable a default source and issue a request to "load it"
				self.autoSelectSource();

				// Load and parse the text value of enabled text sources:
				self.loadEnabledSources();

				if ( callback ) {
					callback();
				}
			} );
		},

		/**
		* Binds the timed text menu
		* and updates its content from "getMainMenu"
		*
		* @param {boolean} autoShow If the menu should be displayed
		*/
		buildMenu: function ( autoShow ) {
			var self = this;

			// Setup text sources ( will callback inline if already loaded )
			self.setupTextSources( function () {
				var ctrlObj,
					$menuButton = self.embedPlayer.getInterface().find( '.timed-text' ),
					positionOpts = {};

				if ( self.embedPlayer.supports.overlays ) {
					positionOpts = {
						directionV: 'up',
						offsetY: self.embedPlayer.controlBuilder.getHeight(),
						directionH: 'left',
						offsetX: -28
					};
				}

				if ( !self.embedPlayer.getInterface() ) {
					mw.log( 'TimedText:: interface called before interface ready, just wait for interface' );
					return;
				}
				$menuButton = self.embedPlayer.getInterface().find( '.timed-text' );
				ctrlObj = self.embedPlayer.controlBuilder;
				// NOTE: Button target should be an option or config
				$menuButton.embedMenu( {
					content: self.getMainMenu(),
					zindex: mw.config.get( 'EmbedPlayer.FullScreenZIndex' ) + 2,
					crumbDefaultText: ' ',
					autoShow: autoShow,
					keepPosition: true,
					showSpeed: 0,
					height: 100,
					width: 300,
					targetMenuContainer: self.getTextMenuContainer(),
					positionOpts: positionOpts,
					backLinkText: mw.msg( 'mwe-timedtext-back-btn' ),
					createMenuCallback: function () {
						var $interface = self.embedPlayer.getInterface(),
							$textContainer = self.getTextMenuContainer(),
							textHeight = 130,
							top = $interface.height() - textHeight - ctrlObj.getHeight() - 6;

						if ( top < 0 ) {
							top = 0;
						}
						// check for audio
						if ( self.embedPlayer.isAudio() ) {
							top = self.embedPlayer.controlBuilder.getHeight() + 4;
						}
						$textContainer.css( {
							top: top,
							height: textHeight,
							position: 'absolute',
							left: $menuButton[ 0 ].offsetLeft - 165,
							bottom: ctrlObj.getHeight()
						} );
						ctrlObj.showControlBar( true );
					},
					closeMenuCallback: function () {
						ctrlObj.restoreControlsHover();
					}
				} );
			} );
		},

		/**
		* Monitor video time and update timed text filed[s]
		*/
		monitor: function () {
			// mw.log( ' timed Text monitor: ' + this.enabledSources.length );
			var embedPlayer = this.embedPlayer,
				// Setup local reference to currentTime:
				currentTime = embedPlayer.currentTime,
				source = this.enabledSources[ 0 ];

			if ( source ) {
				this.updateSourceDisplay( source, currentTime );
			}
		},

		/**
		 * Load all the available text sources from the inline embed
		 * @param {Function} callback Function to call once text sources are loaded
		 */
		loadTextSources: function ( callback ) {
			var self = this;
			// check if text sources are already loaded ( not em )
			if ( this.textSources.length ) {
				callback( this.textSources );
				return;
			}
			this.textSources = [];
			// load inline text sources:
			$.each( this.embedPlayer.getTextTracks(), function ( inx, textSource ) {
				self.textSources.push( new mw.TextSource( textSource ) );
			} );
			// return the callback with sources
			callback( self.textSources );
		},

		/**
		* Get the layout mode
		*
		* Takes into consideration:
		* 	Playback method overlays support ( have to put subtitles below video )
		*
		* @return {string}
		*/
		getLayoutMode: function () {
			// Re-map "ontop" to "below" if player does not support
			if ( this.config.layout === 'ontop' && !this.embedPlayer.supports.overlays ) {
				this.config.layout = 'below';
			}
			return this.config.layout;
		},

		/**
		* Auto selects a source given the local configuration
		*
		* NOTE: presently this selects a "single" source.
		* In the future we could support multiple "enabled sources"
		*
		* @return {boolean}
		*/
		autoSelectSource: function () {
			var setDefault, setLocalPref, setEnglish, i, source, setFirst,
				self = this;
			// If a source is enabled then don't auto select
			if ( this.enabledSources.length ) {
				return false;
			}
			this.enabledSources = [];

			setDefault = false;
			// Check if any source is marked default:
			$.each( this.textSources, function ( inx, source ) {
				if ( source.default ) {
					self.enableSource( source );
					setDefault = true;
					return false;
				}
			} );
			if ( setDefault ) {
				return true;
			}

			setLocalPref = false;
			// Check if any source matches our "local" pref
			$.each( this.textSources, function ( inx, source ) {
				if (
					self.config.userLanguage === source.srclang.toLowerCase() &&
					self.config.userKind === source.kind
				) {
					self.enableSource( source );
					setLocalPref = true;
					return false;
				}
			} );
			if ( setLocalPref ) {
				return true;
			}

			setEnglish = false;
			// If no userLang, source try enabling English:
			if ( this.enabledSources.length === 0 ) {
				for ( i = 0; i < this.textSources.length; i++ ) {
					source = this.textSources[ i ];
					if ( source.srclang.toLowerCase() === 'en' ) {
						self.enableSource( source );
						setEnglish = true;
						return false;
					}
				}
			}
			if ( setEnglish ) {
				return true;
			}

			setFirst = false;
			// If still no source try the first source we get;
			if ( this.enabledSources.length === 0 ) {
				for ( i = 0; i < this.textSources.length; i++ ) {
					source = this.textSources[ i ];
					self.enableSource( source );
					setFirst = true;
					return false;
				}
			}
			if ( setFirst ) {
				return true;
			}

			return false;
		},
		/**
		 * Enable a source and update the currentLangKey
		 * @param {Object} source
		 */
		enableSource: function ( source ) {
			var sourceEnabled,
				self = this;
			// check if we have any source set yet:
			if ( !self.enabledSources.length ) {
				self.enabledSources.push( source );
				self.currentLangKey = source.srclang;
				self.currentLangDir = null;
				return;
			}
			sourceEnabled = false;
			// Make sure the source is not already enabled
			$.each( this.enabledSources, function ( inx, enabledSource ) {
				if ( source.id === enabledSource.id ) {
					sourceEnabled = true;
				}
			} );
			if ( !sourceEnabled ) {
				self.enabledSources.push( source );
				self.currentLangKey = source.srclang;
				self.currentLangDir = null;
			}
		},

		/**
		 * Get the current source sub captions
		 * @param {function} callback function called once source is loaded
		 * @return {boolean}
		 */
		loadCurrentSubSource: function ( callback ) {
			var i, source;
			mw.log( 'loadCurrentSubSource:: enabled source:' + this.enabledSources.length );
			for ( i = 0; i < this.enabledSources.length; i++ ) {
				source = this.enabledSources[ i ];
				if ( source.kind === 'SUB' ) {
					// eslint-disable-next-line no-loop-func
					source.load( function () {
						callback( source );
						return;
					} );
				}
			}
			return false;
		},

		/**
		 * Get sub captions by language key:
		 *
		 * @param {string} langKey Key of captions to load
		 * @param {function} callback function called once language key is loaded
		 */
		getSubCaptions: function ( langKey, callback ) {
			var i, source;
			for ( i = 0; i < this.textSources.length; i++ ) {
				source = this.textSources[ i ];
				if ( source.srclang.toLowerCase() === langKey ) {
					source = this.textSources[ i ];
					// eslint-disable-next-line no-loop-func
					source.load( function () {
						callback( source.captions );
					} );
				}
			}
		},

		/**
		* Issue a request to load all enabled Sources
		*  Should be called anytime enabled Source list is updated
		*/
		loadEnabledSources: function () {
			var self = this;
			mw.log( 'TimedText:: loadEnabledSources ' + this.enabledSources.length );
			$.each( this.enabledSources, function ( inx, enabledSource ) {
				// check if the source requires ovelray ( ontop ) layout mode:
				if ( enabledSource.isOverlay() && self.config.layout === 'ontop' ) {
					self.setLayoutMode( 'ontop' );
				}
				enabledSource.load( function () {
					// Trigger the text loading event:
					$( self.embedPlayer ).trigger( 'loadedTextSource', enabledSource );
				} );
			} );
		},
		/**
		* Checks if a source is "on"
		* @param {Object} source
		* @return {boolean}
		*/
		isSourceEnabled: function ( source ) {
			var isEnabled = false;
			// no source is "enabled" if subtitles are "off"
			if ( this.getLayoutMode() === 'off' ) {
				return false;
			}
			$.each( this.enabledSources, function ( inx, enabledSource ) {
				if ( source.id ) {
					if ( source.id === enabledSource.id ) {
						isEnabled = true;
					}
				}
				if ( source.src ) {
					if ( source.src === enabledSource.src ) {
						isEnabled = true;
					}
				}
			} );
			return isEnabled;
		},

		/**
		 * Marks the active captions in the menu
		 * @param {Object} source
		 */
		markActive: function ( source ) {
			var $captionRows, iconClass,
				$menu = $( '#textMenuContainer_' + this.embedPlayer.id );
			if ( $menu.length ) {
				$captionRows = $menu.find( '.captionRow' );
				if ( $captionRows.length ) {
					$captionRows.each( function () {
						$( this ).removeClass( 'ui-icon-bullet ui-icon-radio-on' );
						iconClass = ( $( this ).data( 'caption-id' ) === source.id ) ? 'ui-icon-bullet' : 'ui-icon-radio-on';
						$( this ).addClass( iconClass );
					} );
				}
			}
		},

		/**
		 * Marks the active layout mode in the menu
		 * @param {string} layoutMode
		 */
		markLayoutActive: function ( layoutMode ) {
			var $layoutRows, iconClass,
				$menu = $( '#textMenuContainer_' + this.embedPlayer.id );
			if ( $menu.length ) {
				$layoutRows = $menu.find( '.layoutRow' );
				if ( $layoutRows.length ) {
					$layoutRows.each( function () {
						$( this ).removeClass( 'ui-icon-bullet ui-icon-radio-on' );
						iconClass = ( $( this ).data( 'layoutMode' ) === layoutMode ) ? 'ui-icon-bullet' : 'ui-icon-radio-on';
						$( this ).addClass( iconClass );
					} );
				}
			}
		},

		/**
		* Get a source object by language, returns "false" if not found
		* @param {string} langKey The language key filter for selected source
		* @return {Object|boolean}
		*/
		getSourceByLanguage: function ( langKey ) {
			var i, source;
			for ( i = 0; i < this.textSources.length; i++ ) {
				source = this.textSources[ i ];
				if ( source.srclang === langKey ) {
					return source;
				}
			}
			return false;
		},

		/**
		* Builds the core timed Text menu and
		* returns the binded jquery object / dom set
		*
		* Assumes text sources have been setup: ( self.setupTextSources() )
		*
		* calls a few sub-functions:
		* Basic menu layout:
		*		Chose Language
		*			All Subtiles here ( if we have categories list them )
		*		Layout
		*			Below video
		*			Ontop video ( only available to supported plugins )
		* TODO features:
		*		[ Search Text ]
		*			[ This video ]
		*			[ All videos ]
		*		[ Chapters ] seek to chapter
		* @return {jQuery}
		*/
		getMainMenu: function () {
			var self = this,
				// Set the menut to avaliable languages:
				$menu = self.getLanguageMenu();

			if ( self.textSources.length === 0 ) {
				$menu.append(
					$.getLineItem( mw.msg( 'mwe-timedtext-no-subs' ), 'close' )
				);
			} else {
				// Layout Menu option if not in an iframe and we can expand video size:
				$menu.append(
					$.getLineItem(
						mw.msg( 'mwe-timedtext-layout-off' ),
						( self.getLayoutMode() === 'off' ) ? 'bullet' : 'radio-on',
						function () {
							self.setLayoutMode( 'off' );
						},
						'layoutRow',
						{ layoutMode: 'off' }
					)
				);
			}
			// Allow other modules to add to the timed text menu:
			$( self.embedPlayer ).trigger( 'TimedText_BuildCCMenu', [ $menu, self.embedPlayer.id ] );

			// Test if only one menu item move its children to the top level
			if ( $menu.children( 'li' ).length === 1 ) {
				$menu.find( 'li > ul > li' ).detach().appendTo( $menu );
				$menu.find( 'li' ).eq( 0 ).remove();
			}

			return $menu;
		},

		/**
		* Utility function to assist in menu build out:
		* Get menu line item (li) html: <li><a> msgKey </a></li>
		*
		* @param {String} msgKey Msg key for menu item
		*/

		/**
		* Get line item (li) from source object
		* @param {Object} source Source to get menu line item from
		* @return {jQuery}
		*/
		getLiSource: function ( source ) {
			var langKey,
				self = this,
				// See if the source is currently "on"
				sourceIcon = ( this.isSourceEnabled( source ) ) ? 'bullet' : 'radio-on';
			if ( source.title ) {
				return $.getLineItem( source.title, sourceIcon, function () {
					self.selectTextSource( source );
				}, 'captionRow', { 'caption-id': source.id } );
			}
			if ( source.srclang ) {
				langKey = source.srclang.toLowerCase();
				return $.getLineItem(
					mw.msg( 'mwe-timedtext-key-language', langKey, self.getLanguageName( langKey ) ),
					sourceIcon,
					function () {
						// select the current text source:
						self.selectTextSource( source );
					},
					'captionRow',
					{ 'caption-id': source.id }
				);
			}
		},

		/**
		 * Get language name from language key
		 * @param {string} langKey Language key
		 * @return {string|boolean}
		 */
		getLanguageName: function ( langKey ) {
			if ( mw.Language.names[ langKey ] ) {
				return mw.Language.names[ langKey ];
			}
			return false;
		},

		/**
		* set the layout mode
		* @param {Object} layoutMode The selected layout mode
		*/
		setLayoutMode: function ( layoutMode ) {
			var self = this;
			mw.log( 'TimedText:: setLayoutMode: ' + layoutMode + ' ( old mode: ' + self.config.layout + ' )' );
			if ( ( layoutMode !== self.config.layout ) || self.firstLoad ) {
				// Update the config and redraw layout
				self.config.layout = layoutMode;
				// Update the display:
				self.updateLayout();
				self.firstLoad = false;
			}
			self.markLayoutActive( layoutMode );
		},

		toggleCaptions: function () {
			mw.log( 'TimedText:: toggleCaptions was:' + this.config.layout );
			if ( this.config.layout === 'off' ) {
				this.setLayoutMode( this.defaultDisplayMode );
			} else {
				this.setLayoutMode( 'off' );
			}
		},
		/**
		* Updates the timed text layout ( should be called when config.layout changes )
		*/
		updateLayout: function () {
			var $playerTarget = this.embedPlayer.getInterface();
			mw.log( 'TimedText:: updateLayout ' );
			if ( $playerTarget ) {
				// remove any existing caption containers:
				$playerTarget.find( '.captionContainer,.captionsOverlay' ).remove();
			}
			this.refreshDisplay();
		},

		/**
		* Select a new source
		*
		* @param {Object} source Source object selected
		*/
		selectTextSource: function ( source ) {
			var $playerTarget,
				self = this;
			mw.log( 'TimedText:: selectTextSource: select lang: ' + source.srclang );

			// enable last non-off layout:
			self.setLayoutMode( self.lastLayout );

			// For some reason we lose binding for the menu ~sometimes~ re-bind
			this.bindTextButton( this.embedPlayer.getInterface().find( 'timed-text' ) );

			this.currentLangKey = source.srclang;
			this.currentLangDir = null;

			// Update the config language if the source includes language
			if ( source.srclang ) {
				this.config.userLanguage = source.srclang;
			}

			if ( source.kind ) {
				this.config.userKind = source.kind;
			}

			// (@@todo update kind & setup kind language buckets? )

			// Remove any other sources selected in sources kind
			this.enabledSources = [];

			this.enabledSources.push( source );

			// Set any existing text target to "loading"
			if ( !source.loaded ) {
				$playerTarget = this.embedPlayer.getInterface();
				$playerTarget.find( '.track' ).text( mw.msg( 'mwe-timedtext-loading-text' ) );
				// Load the text:
				source.load( function () {
					// Refresh the interface:
					self.refreshDisplay();
				} );
			} else {
				self.refreshDisplay();
			}

			self.markActive( source );

			// Trigger the event
			$( this.embedPlayer ).trigger( 'TimedText_ChangeSource' );
		},

		/**
		* Refresh the display, updates the timedText layout, menu, and text display
		* also updates the cookie preference.
		*
		* Called after a user option change
		*/
		refreshDisplay: function () {
			// Update the configuration object
			$.cookie( 'TimedText.Preferences', JSON.stringify( this.config ) );

			// Empty out previous text to force an interface update:
			this.prevText = [];

			// Refresh the Menu (if it has a target to refresh)
			mw.log( 'TimedText:: bind menu refresh display' );
			this.buildMenu();
			this.resizeInterface();

			// add an empty catption:
			this.displayTextTarget( $( '<span> ' ).text( '' ) );

			// Issues a "monitor" command to update the timed text for the new layout
			this.monitor();
		},

		/**
		* Builds the language source list menu
		* Check if the "track" tags had the "kind" attribute.
		*
		* The kind attribute forms "categories" of text tracks like "subtitles",
		*  "audio description", "chapter names". We check for these categories
		*  when building out the language menu.
		*
		* @return {jQuery}
		*/
		getLanguageMenu: function () {
			var i, source, categoryKey, $langMenu, $catChildren,
				self = this,
				// See if we have categories to worry about
				// associative array of SUB etc categories. Each kind contains an array of textSources.
				categorySourceList = {},
				sourcesWithCategoryCount = 0,
				// ( All sources should have a kind (depreciate )
				sourcesWithoutCategory = [];

			for ( i = 0; i < this.textSources.length; i++ ) {
				source = this.textSources[ i ];
				if ( source.kind ) {
					categoryKey = source.kind;
					// Init Category menu item if it does not already exist:
					if ( !categorySourceList[ categoryKey ] ) {
						// Set up catList pointer:
						categorySourceList[ categoryKey ] = [];
						sourcesWithCategoryCount++;
					}
					// Append to the source kind key menu item:
					categorySourceList[ categoryKey ].push(
						self.getLiSource( source )
					);
				} else {
					sourcesWithoutCategory.push( self.getLiSource( source ) );
				}
			}
			$langMenu = $( '<ul>' );
			// Check if we have multiple categories ( if not just list them under the parent menu item)
			if ( sourcesWithCategoryCount > 1 ) {
				for ( categoryKey in categorySourceList ) {
					$catChildren = $( '<ul>' );
					for ( i = 0; i < categorySourceList[ categoryKey ].length; i++ ) {
						$catChildren.append(
							categorySourceList[ categoryKey ][ i ]
						);
					}
					// Append a cat menu item for each kind list
					// Give grep a chance to find the usages:
					// mwe-timedtext-textcat-cc, mwe-timedtext-textcat-sub, mwe-timedtext-textcat-tad,
					// mwe-timedtext-textcat-ktv, mwe-timedtext-textcat-tik, mwe-timedtext-textcat-ar,
					// mwe-timedtext-textcat-nb, mwe-timedtext-textcat-meta, mwe-timedtext-textcat-trx,
					// mwe-timedtext-textcat-lrc, mwe-timedtext-textcat-lin, mwe-timedtext-textcat-cue
					$langMenu.append(
						$.getLineItem( mw.msg( 'mwe-timedtext-textcat-' + categoryKey.toLowerCase() ) ).append(
							$catChildren
						)
					);
				}
			} else {
				for ( categoryKey in categorySourceList ) {
					for ( i = 0; i < categorySourceList[ categoryKey ].length; i++ ) {
						$langMenu.append(
							categorySourceList[ categoryKey ][ i ]
						);
					}
				}
			}
			// Add any remaning sources that did nto have a category
			for ( i = 0; i < sourcesWithoutCategory.length; i++ ) {
				$langMenu.append( sourcesWithoutCategory[ i ] );
			}

			return $langMenu;
		},

		/**
		 * Updates a source display in the interface for a given time
		 * @param {object} source Source to update
		 * @param {number} time Caption time used to add and remove active captions.
		 */
		updateSourceDisplay: function ( source, time ) {
			var activeCaptions, addedCaption,
				self = this;
			if ( this.timeOffset ) {
				time = time + parseInt( this.timeOffset );
			}

			// Get the source text for the requested time:
			activeCaptions = source.getCaptionForTime( time );
			addedCaption = false;
			// Show captions that are on:
			$.each( activeCaptions, function ( capId, caption ) {
				var $cap = self.embedPlayer.getInterface().find( '.track[data-capId="' + capId + '"]' );
				if ( caption.content !== $cap.html() ) {
					// remove old
					$cap.remove();
					// add the updated value:
					self.addCaption( source, capId, caption );
					addedCaption = true;
				}
			} );

			// hide captions that are off:
			self.embedPlayer.getInterface().find( '.track' ).each( function ( inx, caption ) {
				if ( !activeCaptions[ $( caption ).attr( 'data-capId' ) ] ) {
					if ( addedCaption ) {
						$( caption ).remove();
					} else {
						$( caption ).fadeOut( mw.config.get( 'EmbedPlayer.MonitorRate' ), function () { $( this ).remove(); } );
					}
				}
			} );
		},
		addCaption: function ( source, capId, caption ) {
			var $textTarget;

			if ( this.getLayoutMode() === 'off' ) {
				return;
			}

			// use capId as a class instead of id for easy selections and no conflicts with
			// multiple players on page.
			$textTarget = $( '<div>' )
				.addClass( 'track' )
				.attr( 'data-capId', capId )
				.hide();

			// Update text ( use "html" instead of "text" so that subtitle format can
			// include html formating
			// TOOD we should scrub this for non-formating html
			$textTarget.append(
				$( '<span>' )
					.addClass( 'ttmlStyled' )
					.css( 'pointer-events', 'auto' )
					.css( this.getCaptionCss() )
					.append(
						$( '<span>' )
							// Prevent background (color) overflowing TimedText
							// http://stackoverflow.com/questions/9077887/avoid-overlapping-rows-in-inline-element-with-a-background-color-applied
							.css( 'position', 'relative' )
							.html( caption.content )
					)
			);

			// Add/update the lang option
			$textTarget.attr( 'lang', source.srclang.toLowerCase() );

			// Update any links to point to a new window
			$textTarget.find( 'a' ).attr( 'target', '_blank' );

			// Add TTML or other complex text styles / layouts if we have ontop captions:
			if ( this.getLayoutMode() === 'ontop' ) {
				if ( caption.css ) {
					$textTarget.css( caption.css );
				} else {
					$textTarget.css( this.getDefaultStyle() );
				}
			}
			// Apply any custom style ( if we are ontop of the video )
			this.displayTextTarget( $textTarget );

			// apply any interface size adjustments:
			$textTarget.css( this.getInterfaceSizeTextCss( {
				width: this.embedPlayer.getInterface().width(),
				height: this.embedPlayer.getInterface().height()
			} )
			);

			// Update the style of the text object if set
			if ( caption.styleId ) {
				$textTarget.find( 'span.ttmlStyled' ).css(
					source.getStyleCssById( caption.styleId )
				);
			}
			$textTarget.fadeIn( 'fast' );
		},
		displayTextTarget: function ( $textTarget ) {
			var embedPlayer = this.embedPlayer,
				$interface = embedPlayer.getInterface(),
				controlBarHeight = embedPlayer.controlBuilder.getHeight();

			if ( this.getLayoutMode() === 'off' ) {
				// sync player size per audio player:
				if ( embedPlayer.isAudio() ) {
					$interface.find( '.overlay-win' ).css( 'top', controlBarHeight );
					$interface.css( 'height', controlBarHeight );
				}
				return;
			}

			if ( this.getLayoutMode() === 'ontop' ) {
				this.addTextOverlay(
					$textTarget
				);
			} else if ( this.getLayoutMode() === 'below' ) {
				this.addTextBelowVideo( $textTarget );
			} else {
				mw.log( 'Possible Error, layout mode not recognized: ' + this.getLayoutMode() );
			}

			// sync player size per audio player:
			if ( embedPlayer.isAudio() && embedPlayer.getInterface().height() < 80 ) {
				$interface.find( '.overlay-win' ).css( 'top', 80 );
				$interface.css( 'height', 80 );

				$interface.find( '.captionsOverlay' )
					.css( 'bottom', embedPlayer.controlBuilder.getHeight() );
			}

		},
		getDefaultStyle: function () {
			var baseCss,
				defaultBottom = 15;
			if ( this.embedPlayer.controlBuilder.isOverlayControls() && !this.embedPlayer.getInterface().find( '.control-bar' ).is( ':hidden' ) ) {
				defaultBottom += this.embedPlayer.controlBuilder.getHeight();
			}
			baseCss = {
				position: 'absolute',
				bottom: defaultBottom,
				width: '100%',
				display: 'block',
				opacity: 0.8,
				'text-align': 'center'
			};
			baseCss = $.extend( baseCss, this.getInterfaceSizeTextCss( {
				width: this.embedPlayer.getInterface().width(),
				height: this.embedPlayer.getInterface().height()
			} ) );
			return baseCss;
		},
		addTextOverlay: function ( $textTarget ) {
			var $captionsOverlayTarget = this.embedPlayer.getInterface().find( '.captionsOverlay' ),
				layoutCss = {
					left: 0,
					top: 0,
					bottom: 0,
					right: 0,
					position: 'absolute',
					direction: this.getCurrentLangDir(),
					'z-index': mw.config.get( 'EmbedPlayer.FullScreenZIndex' )
				};

			if ( $captionsOverlayTarget.length === 0 ) {
				// TODO make this look more like addBelowVideoCaptionsTarget
				$captionsOverlayTarget = $( '<div>' )
					.addClass( 'captionsOverlay' )
					.css( layoutCss )
					.css( 'pointer-events', 'none' );
				this.embedPlayer.getVideoHolder().append( $captionsOverlayTarget );
			}
			// Append the text:
			$captionsOverlayTarget.append( $textTarget );

		},
		/**
		 * Applies the default layout for a text target
		 * @param {jQuery} $textTarget
		 */
		addTextBelowVideo: function ( $textTarget ) {
			var $playerTarget = this.embedPlayer.getInterface();
			// Get the relative positioned player class from the controlBuilder:
			this.embedPlayer.controlBuilder.keepControlBarOnScreen = true;
			if ( !$playerTarget.find( '.captionContainer' ).length || this.embedPlayer.useNativePlayerControls() ) {
				this.addBelowVideoCaptionContainer();
			}
			$playerTarget.find( '.captionContainer' ).html(
				$textTarget.css( {
					color: 'white'
				} )
			);
		},
		addBelowVideoCaptionContainer: function () {
			var self = this,
				$playerTarget = this.embedPlayer.getInterface();
			mw.log( 'TimedText:: addBelowVideoCaptionContainer' );
			if ( $playerTarget.find( '.captionContainer' ).length ) {
				return;
			}
			// Append after video container
			this.embedPlayer.getVideoHolder().after(
				$( '<div>' ).addClass( 'captionContainer block' )
					.css( {
						width: '100%',
						height: mw.config.get( 'TimedText.BelowVideoBlackBoxHeight' ) + 'px',
						'background-color': '#000',
						'text-align': 'center',
						'padding-top': '5px'
					} )
			);

			self.embedPlayer.triggerHelper( 'updateLayout' );
		},
		/**
		 * Resize the interface for layoutMode == 'below' ( if not in full screen)
		 */
		resizeInterface: function () {
			var self = this;
			if ( !self.embedPlayer.controlBuilder ) {
				// too soon
				return;
			}
			if ( !self.embedPlayer.controlBuilder.inFullScreen && self.originalPlayerHeight ) {
				self.embedPlayer.triggerHelper( 'resizeIframeContainer', [ { height: self.originalPlayerHeight } ] );
			} else {
				// removed resize on container content, since syncPlayerSize calls now handle keeping player aspect.
				self.embedPlayer.triggerHelper( 'updateLayout' );
			}
		},
		/**
		 * Build css for caption using this.options
		 * @return {Object}
		 */
		getCaptionCss: function () {
			return {};
		}
	};

}( mediaWiki, jQuery ) );
