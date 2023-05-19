const Button = videojs.getComponent( 'Button' );

/**
 * @extends Button
 */
class InfoButton extends Button {
	constructor( player, options ) {
		super( player, options );
		this.link = options.link;
		this.controlText( 'More information' );
		this.addClass( 'mw-info-button' );
	}
	handleClick() {
		window.navigator.url = window.open( this.link, '_blank' );
	}
}

// Register the component with Video.js, so it can be used in players.
videojs.registerComponent( 'InfoButton', InfoButton );

/**
 * Initialize the plugin.
 *
 * @param {Object} [options] configuration for the plugin
 */
const infoButtonPlugin = function ( /* options */ ) {
	const player = this;

	player.ready( () => {
		const title = mw.Title.makeTitle(
			mw.config.get( 'wgNamespaceIds' ).file,
			player.el().getAttribute( 'data-mwtitle' )
		);

		if ( mw.config.get( 'wgTitle' ) !== player.el().getAttribute( 'data-mwtitle' ) ) {
			player.controlBar.infoButton = player.controlBar.addChild( 'InfoButton', { link: title.getUrl() } );
		}
	} );
};

// register the plugin
videojs.registerPlugin( 'infoButton', infoButtonPlugin );
