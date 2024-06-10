/**
 * @type {videojs.Plugin}
 * @private
 */
const Plugin = videojs.getPlugin( 'plugin' );
/** Ensure the button is registered and loaded */
require( './mw-info-button.js' );

/**
 * Add an {@link InfoButton} to the interface of the player.
 *
 * When the button is clicked, take the user to the MediaWiki file
 * description page of the media item.
 * It relies on the data-mwtitle attribute on the media element to
 * find the page.
 *
 * @extends videojs.Plugin
 */
class InfoButtonPlugin extends Plugin {
	/**
	 * @param {videojs.Player} player
	 * @param {Object} [options] configuration for the plugin
	 */
	constructor( player, options ) {
		super( player, options );

		player.ready( () => {
			const title = player.options().mwTitle;

			if ( !title ) {
				return;
			}

			// We do not need the info button on a File page, unless it's the embedding mode
			// we are already at the destination of the button
			if ( player.options().iframe || mw.config.get( 'wgTitle' ) !== title.getMainText() ) {
				player.controlBar.infoButton = player.controlBar.addChild(
					'InfoButton',
					{ link: title.getUrl() }
				);
			}
		} );
	}
}

// register the plugin
videojs.registerPlugin( 'infoButton', InfoButtonPlugin );

module.exports = InfoButtonPlugin;
