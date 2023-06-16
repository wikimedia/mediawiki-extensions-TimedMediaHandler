const Button = videojs.getComponent( 'Button' );

/**
 * VideoJS Button component to link a Url
 *
 * @extends videojs.Button
 */
class InfoButton extends Button {
	/**
	 *
	 * @param {videojs.Player} player a videojs Player object
	 * @param {Object} options - Options object
	 * @param {string} options.link - URL that the button should navigate to
	 */
	constructor( player, options ) {
		super( player, options );
		/**
		 * @property {string}
		 * @private
		 */
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

module.exports = InfoButton;
