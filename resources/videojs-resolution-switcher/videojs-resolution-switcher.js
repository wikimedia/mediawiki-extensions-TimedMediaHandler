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

/** @type {videojs.Plugin} */
const Plugin = videojs.getPlugin( 'plugin' );
const ResolutionMenuButton = require( './ResolutionMenuButton.js' );
const resolutionSwitchterDefaults = {
	ui: true
};

/**
 * Represents a source and the properties of that source
 * This is a videojs structure that we added label and res fields to
 *
 * @typedef SourceObject object
 * @property {string} src url the source
 * @property {string} type Type of the source. Like video/webm
 * @property {(string|number)} res Resolution specification
 * @property {string} label Display label for this Source element
 */

/**
 * @typedef Resolution object
 * @property {(string|number)} [res] The resolution
 * @property {string} [label] Label identifying this resolution
 * @property {SourceObject[]} sources set of sources for
 * this specific resolution
 */

/**
 * Bucketized sources by label, res and type
 *
 * @typedef BucketObject object
 * @property {{string, SourceObject}} label Dictionary of labels to sources with that label
 * @property {{string, SourceObject}} res Dictionary of res to sources with that res
 * @property {{string, SourceObject}} type Dictionary of type to sources with that type
 */

/**
 * Plugin to add a resolution selector button to the interface
 *
 * The selector is populated with the sources used to initialize
 * the player. These are sorted by resolution and we hide that
 * some resolutions technically have multiple source options (ogg vs webm)
 */
class VideoJsResolutionSwitcherPlugin extends Plugin {

	/**
	 * @param {videojs.Player} player
	 * @param {Object} [options]
	 */
	constructor( player, options ) {
		super( player );

		this.settings = videojs.obj.merge( resolutionSwitchterDefaults, options );
		/** @type {BucketObject} */
		this.groupedSrc = {};
		/** @type {SourceObject[]} */
		this.currentSources = {};
		/** @type {Resolution} */
		this.currentResolutionState = {};

		this.player.ready( () => {
			const controlBar = this.player.controlBar;
			if ( this.settings.ui ) {
				const menuButton = new ResolutionMenuButton( this, this.settings );
				controlBar.resolutionSwitcher = controlBar.el().insertBefore(
					menuButton.el(),
					controlBar.getChild( 'fullscreenToggle' ) ?
						controlBar.getChild( 'fullscreenToggle' ).el() : null
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
	 * @param {SourceObject[]} src array of sources
	 * [{src: '', type: '', label: '', res: ''}]
	 */
	updateSrc( src ) {
		const player = this.player;

		// Only add those sources which we can (maybe) play
		src = src.filter( ( source ) => {
			try {
				return ( player.canPlayType( source.type ) !== '' );
			} catch ( e ) {
				// If a Tech doesn't yet have canPlayType just add it
				return true;
			}
		} );

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
	 * @return {(videojs.Player|Resolution)} Current resolution object
	 * {label: '', sources: []} if used as getter or
	 * player object if used as setter
	 */
	currentResolution( label ) {
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
			label
		);
		player.one( handleSeekEvent, () => {
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
	 * @return {BucketObject} grouped sources
	 * { label: { key: [] }, res: { key: [] }, type: { key: [] } }
	 */
	getGroupedSrc() {
		return this.groupedSrc;
	}

	/**
	 * Do the actual setting of the Source object on the video.js player
	 * but only pass the src, type and res properties of the sources
	 *
	 * @private
	 * @param {SourceObject[]} sources
	 * @param {string} label
	 */
	setSourcesSanitized( sources, label ) {
		this.currentResolutionState = {
			label: label,
			sources: sources
		};
		// If the source fails, try any of the other sources
		this.player.one( 'error', ( errorEvent ) => {
			const error = this.player.error();
			if ( error.code >= 3 ) {
				// MEDIA_ERR_DECODE OR MEDIA_ERR_SRC_NOT_SUPPORTED
				errorEvent.stopImmediatePropagation();
				this.player.errorDisplay.close();
				const newSources = this.currentSources.filter( ( asource ) => asource.src !== this.player.currentSrc() );
				this.updateSrc( newSources );
				this.player.play();
			}
		} );
		this.player.src( sources.map( ( src ) => ( { src: src.src, type: src.type, res: src.res } ) ) );
	}

	/**
	 * Method used for sorting list of sources
	 *
	 * @private
	 * @param   {SourceObject} a - source object with res property
	 * @param   {SourceObject} b - source object with res property
	 * @return {number} result of comparison
	 */
	compareResolutions( a, b ) {
		if ( !a.res || !b.res ) {
			return 0;
		}
		return ( +b.res ) - ( +a.res );
	}

	/**
	 * Group sources by their label, resolution and type
	 *
	 * @private
	 * @param  {SourceObject[]} src Array of sources
	 * @return {BucketObject} grouped sources
	 * { label: { key: [] }, res: { key: [] }, type: { key: [] } }
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

	/**
	 * @private
	 * @param {BucketObject} resolutions
	 * @param {string} key
	 * @param {SourceObject} source
	 */
	initResolutionKey( resolutions, key, source ) {
		if ( resolutions[ key ][ source[ key ] ] === undefined ) {
			resolutions[ key ][ source[ key ] ] = [];
		}
	}

	/**
	 * @private
	 * @param {BucketObject} resolutions
	 * @param {string} key
	 * @param {SourceObject} source
	 */
	appendSourceToKey( resolutions, key, source ) {
		resolutions[ key ][ source[ key ] ].push( source );
	}

	/**
	 * Choose src if option.default is specified
	 *
	 * @param  {BucketObject} groupedSrc {res: { key: [] }}
	 * @param  {SourceObject[]} src Array of sources
	 *          sorted by resolution used to find high and low res
	 * @return {Resolution} {res: string, label: string, sources: SourceObject[]}
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

		return { res: selectedRes, label: selectedLabel, sources: groupedSrc.res[ selectedRes ] || [] };
	}
}
VideoJsResolutionSwitcherPlugin.VERSION = '2.0.0';

// register the plugin
videojs.registerPlugin( 'videoJsResolutionSwitcher', VideoJsResolutionSwitcherPlugin );

module.exports = VideoJsResolutionSwitcherPlugin;
