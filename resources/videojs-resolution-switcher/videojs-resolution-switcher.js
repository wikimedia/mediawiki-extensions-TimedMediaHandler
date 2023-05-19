/**
 * forked from:
 *
 * videojs-resolution-switcher - 2015-7-26
 * Copyright (c) 2016 Kasper Moskwiak
 * Modified by Pierre Kraft and Derk-Jan Hartman
 * Licensed under the Apache-2.0 license.
 *
 * Rewritten to ES6 in 2023 by Derk-Jan Hartman
 */
'use strict';
// eslint-disable-next-line no-redeclare
const Plugin = videojs.getPlugin( 'plugin' );
const ResolutionMenuButton = require( './ResolutionMenuButton.js' );
const resolutionSwitchterDefaults = {
	ui: true
};

/**
 * Initialize the plugin.
 *
 * @param {Object} [options] configuration for the plugin
 */
class VideoJsResolutionSwitcherPlugin extends Plugin {

	constructor( player, options ) {
		super( player );

		this.settings = videojs.obj.merge( resolutionSwitchterDefaults, options );
		this.groupedSrc = {};
		this.currentSources = {};
		this.currentResolutionState = {};

		this.player.ready( () => {
			const controlBar = this.player.controlBar;
			if ( this.settings.ui ) {
				const menuButton = new ResolutionMenuButton( this, this.settings );
				controlBar.resolutionSwitcher = controlBar.el().insertBefore(
					menuButton.el(),
					controlBar.getChild( 'fullscreenToggle' ).el()
				);
				controlBar.resolutionSwitcher.dispose = function () {
					this.parentNode.removeChild( this );
				};
			}
			// eslint-disable-next-line no-underscore-dangle
			if ( this.player.options_.sources.length > 1 ) {
				// tech: Html5 and Flash
				// Create resolution switcher for videos form <source> tag inside <video>
				// eslint-disable-next-line no-underscore-dangle
				this.updateSrc( this.player.options_.sources );
			}
		} );
	}

	/**
	 * Updates player sources or returns current source URL
	 *
	 * @param {Array}  [src] array of sources
	 * [{src: '', type: '', label: '', res: ''}]
	 * @return {Object | string | Array} videojs player object
	 * if used as setter or current source URL, object, or array of sources
	 */
	updateSrc( src ) {
		const player = this.player;
		// Return current src if src is not given
		if ( !src ) {
			return player.src();
		}

		// Only add those sources which we can (maybe) play
		src = src.filter( function ( source ) {
			try {
				return ( player.canPlayType( source.type ) !== '' );
			} catch ( e ) {
				// If a Tech doesn't yet have canPlayType just add it
				return true;
			}
		} );
		if ( src.length === 0 ) {
			// Return current src if no playable sources.
			return player.src();
		}

		// Sort sources
		this.currentSources = src.sort( this.compareResolutions );
		this.groupedSrc = this.bucketSources( this.currentSources );
		// Pick one by default
		const chosen = this.chooseSrc( this.groupedSrc, this.currentSources );
		this.currentResolutionState = {
			label: chosen.label,
			sources: chosen.sources
		};

		this.player.trigger( 'updateSources' );
		this.setSourcesSanitized( chosen.sources, chosen.label );
		this.player.trigger( 'resolutionchange' );
	}

	/**
	 * Returns current resolution or sets one when label is specified
	 *
	 * @param {string}   [label]         label name
	 * @param {Function} [customSourcePicker] custom function to choose source.
	 * Takes 2 arguments: sources, label. Must return player object.
	 * @return {Object}   current resolution object
	 * {label: '', sources: []} if used as getter or
	 * player object if used as setter
	 */
	currentResolution( label, customSourcePicker ) {
		const player = this.player;
		if ( label === undefined ) {
			return this.currentResolutionState;
		}

		// Lookup sources for label
		if ( !this.groupedSrc || !this.groupedSrc.label || !this.groupedSrc.label[ label ] ) {
			return;
		}
		const sources = this.groupedSrc.label[ label ];
		// Remember player state
		const currentTime = player.currentTime();
		const isPaused = player.paused();

		// Hide bigPlayButton
		// eslint-disable-next-line no-underscore-dangle
		if ( !isPaused && player.options_.bigPlayButton ) {
			player.bigPlayButton.hide();
		}

		// Change player source and wait for loadeddata event, then play video
		// If player preload is 'none' and then loadeddata not fired.
		// So, we need timeupdate event for seek handle
		let handleSeekEvent = 'loadeddata';
		if ( player.preload() === 'metadata' ) {
			handleSeekEvent = 'loadedmetadata';
		}
		if ( player.preload() === 'none' ) {
			handleSeekEvent = 'timeupdate';
		}
		this.setSourcesSanitized(
			sources,
			label,
			customSourcePicker || this.settings.customSourcePicker
		);
		player.one( handleSeekEvent, function () {
			player.currentTime( currentTime );
			// eslint-disable-next-line no-underscore-dangle
			player.handleTechSeeked_();
			if ( !isPaused ) {
				// Start playing and hide loadingSpinner (flash issue ?)
				player.play();
				// eslint-disable-next-line no-underscore-dangle
				player.handleTechSeeked_();
			}
			player.trigger( 'resolutionchange' );
		} );
		return player;
	}

	/**
	 * Returns grouped sources by label, resolution and type
	 *
	 * @return {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
	 */
	getGroupedSrc() {
		return this.groupedSrc;
	}

	setSourcesSanitized( sources, label, customSourcePicker ) {
		this.currentResolutionState = {
			label: label,
			sources: sources
		};
		if ( typeof customSourcePicker === 'function' ) {
			return customSourcePicker( this, sources, label );
		}
		// If the source fails, try any of the other sources
		this.player.one( 'error', ( errorEvent ) => {
			const error = this.player.error();
			if ( error.code >= 3 ) {
				// MEDIA_ERR_DECODE OR MEDIA_ERR_SRC_NOT_SUPPORTED
				errorEvent.stopImmediatePropagation();
				this.player.errorDisplay.close();
				const newSources = this.currentSources.filter( function ( asource ) {
					return asource.src !== this.currentSrc();
				} );
				this.updateSrc( newSources );
				this.player.play();
			}
		} );
		this.player.src( sources.map( function ( src ) {
			return { src: src.src, type: src.type, res: src.res };
		} ) );
	}

	/**
	 * Method used for sorting list of sources
	 *
	 * @param   {Object} a - source object with res property
	 * @param   {Object} b - source object with res property
	 * @return {number} result of comparison
	 */
	compareResolutions( a, b ) {
		if ( !a.res || !b.res ) {
			return 0;
		}
		return ( +b.res ) - ( +a.res );
	}

	/**
	 * Group sources by label, resolution and type
	 *
	 * @param   {Array}  src Array of sources
	 * @return {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
	 */
	bucketSources( src ) {
		const resolutions = {
			label: {},
			res: {},
			type: {}
		};
		src.forEach( ( source ) => {
			this.initResolutionKey( resolutions, 'label', source );
			this.initResolutionKey( resolutions, 'res', source );
			this.initResolutionKey( resolutions, 'type', source );

			this.appendSourceToKey( resolutions, 'label', source );
			this.appendSourceToKey( resolutions, 'res', source );
			this.appendSourceToKey( resolutions, 'type', source );
		} );
		return resolutions;
	}

	initResolutionKey( resolutions, key, source ) {
		if ( resolutions[ key ][ source[ key ] ] === undefined ) {
			resolutions[ key ][ source[ key ] ] = [];
		}
	}

	appendSourceToKey( resolutions, key, source ) {
		resolutions[ key ][ source[ key ] ].push( source );
	}

	/**
	 * Choose src if option.default is specified
	 *
	 * @param   {Object} groupedSrc {res: { key: [] }}
	 * @param   {Array}  src Array of sources sorted by resolution used to find high and low res
	 * @return {Object} {res: string, sources: []}
	 */
	chooseSrc( groupedSrc, src ) {
		let selectedRes = this.settings.default;
		let selectedLabel = '';

		if ( selectedRes === 'high' ) {
			selectedRes = src[ 0 ].res;
			selectedLabel = src[ 0 ].label;
		} else if ( selectedRes === 'low' || selectedRes === null || !groupedSrc.res[ selectedRes ] ) {
			// Select low-res if default is low or not set
			selectedRes = src[ src.length - 1 ].res;
			selectedLabel = src[ src.length - 1 ].label;
		} else if ( groupedSrc.res[ selectedRes ] ) {
			selectedLabel = groupedSrc.res[ selectedRes ][ 0 ].label;
		}

		return { res: selectedRes, label: selectedLabel, sources: groupedSrc.res[ selectedRes ] };
	}
}
VideoJsResolutionSwitcherPlugin.VERSION = '2.0.0';

// register the plugin
videojs.registerPlugin( 'videoJsResolutionSwitcher', VideoJsResolutionSwitcherPlugin );

module.exports = VideoJsResolutionSwitcherPlugin;
