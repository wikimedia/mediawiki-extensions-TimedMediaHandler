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
const MenuItem = videojs.getComponent( 'MenuItem' );

/**
 * @class ResolutionMenuItem
 * @extends MenuItem
 */
class ResolutionMenuItem extends MenuItem {
	constructor( plugin, options ) {
		options.selectable = true;
		super( plugin.player, options );
		this.plugin = plugin;
		this.player = plugin.player;
		this.src = options.src;

		this.player.on( 'resolutionchange', () => this.update() );
	}

	handleClick( /* event */ ) {
		// eslint-disable-next-line no-underscore-dangle
		this.plugin.currentResolution( this.options_.label );
	}

	update() {
		const selection = this.plugin.currentResolution();
		// eslint-disable-next-line no-underscore-dangle
		this.selected( this.options_.label === selection.label );
	}

	dispose() {
		this.player.off( 'resolutionchange', () => this.update() );
		super.dispose();
	}
}
MenuItem.registerComponent( 'ResolutionMenuItem', ResolutionMenuItem );

module.exports = ResolutionMenuItem;
