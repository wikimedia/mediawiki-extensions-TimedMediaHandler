/* eslint-disable no-implicit-globals */
var SubsCapsButton = videojs.getComponent( 'SubsCapsButton' );

var MwCreateSubtitlesMenuItem = videojs.getComponent( 'MwCreateSubtitlesMenuItem' );
var MwSubtitlesButton = videojs.extend( SubsCapsButton, {
	createItems: function ( items, menuitem ) {
		items = SubsCapsButton.prototype.createItems.call( this, items, menuitem );

		// eslint-disable-next-line no-underscore-dangle
		if ( items.length <= this.hideThreshold_ ) {
			// For now always show the CC menu, so we can present this entry
			// If the only other items are the hideable ones, then reset the menu.
			// eslint-disable-next-line no-underscore-dangle
			items = [ new MwCreateSubtitlesMenuItem( this.player_, { kind: this.label_ } ) ];
			// eslint-disable-next-line no-underscore-dangle
			this.hideThreshold_ = 0;
		} else {
			// eslint-disable-next-line no-underscore-dangle
			items.splice( 1, 0, new MwCreateSubtitlesMenuItem( this.player_, { kind: this.label_ } ) );
			// eslint-disable-next-line no-underscore-dangle
			this.hideThreshold_ += 1;
		}
		return items;
	}
} );

// We override the default SubsCapsButton
// This saves having to modify the layout etc. but might be fragile
videojs.registerComponent( 'SubsCapsButton', MwSubtitlesButton );

module.exports = MwSubtitlesButton;
