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
/** @type {videojs.MenuButton} */
const MenuButton = videojs.getComponent( 'MenuButton' );
const ResolutionMenuItem = require( './ResolutionMenuItem.js' );

/**
 * Resolution menu button
 */
class ResolutionMenuButton extends MenuButton {
	/**
	 *
	 * @param {videojs.Plugin} plugin The plugin responsible for adding this button
	 * @param {Object} options
	 */
	constructor( plugin, options ) {
		options.label = 'Quality';
		// Sets this.player_, this.options_ and initializes the component
		super( plugin.player, options );
		this.plugin = plugin;
		this.player = plugin.player;

		this.el().setAttribute( 'aria-label', 'Quality' );
		this.controlText( 'Quality' );
		this.addClass( 'vjs-resolution-button' );

		if ( options.dynamicLabel ) {
			// eslint-disable-next-line mediawiki/class-doc
			videojs.dom.addClass( this.label, 'vjs-resolution-button-label' );
			this.el().appendChild( this.label );
		} else {
			const staticLabel = document.createElement( 'span' );
			// eslint-disable-next-line mediawiki/class-doc
			videojs.dom.addClass( staticLabel, 'vjs-menu-icon' );
			this.el().appendChild( staticLabel );
		}
		this.player.on( 'updateSources', () => this.update() );
	}

	/**
	 * @return {ResolutionMenuItem[]}
	 */
	createItems() {
		const menuItems = [];
		const labels = ( this.sources && this.sources.label ) || {};

		// FIXME order is not guaranteed here.
		for ( const key in labels ) {
			menuItems.push( new ResolutionMenuItem(
				this.plugin,
				{
					label: key,
					src: labels[ key ],
					selected: key === (
						this.currentSelection ? this.currentSelection.label : false
					)
				} )
			);
		}
		return menuItems;
	}

	update() {
		// This method is called from the super's constructor
		// we might not have access to the plugin yet
		if ( this.plugin ) {
			this.sources = this.plugin.getGroupedSrc();
			this.currentSelection = this.plugin.currentResolution();
			if ( typeof this.label === 'undefined' ) {
				this.label = document.createElement( 'span' );
			}
			this.label.innerHTML = this.currentSelection ? this.currentSelection.label : '';
		}
		return super.update();
	}
}
MenuButton.registerComponent( 'ResolutionMenuButton', ResolutionMenuButton );

module.exports = ResolutionMenuButton;
