/* eslint-disable no-implicit-globals */
var SubsCapsButton = videojs.getComponent( 'SubsCapsButton' );

var MwCreateSubtitlesMenuItem = videojs.getComponent( 'MwCreateSubtitlesMenuItem' );
var MwSubtitlesButton = videojs.extend( SubsCapsButton, {
	createItems: function ( items, menuitem ) {
		items = SubsCapsButton.prototype.createItems.call( this, items, menuitem );
		// eslint-disable-next-line no-underscore-dangle
		items.splice( 1, 0, new MwCreateSubtitlesMenuItem( this.player_, { kind: this.label_ } ) );
		// For now always show the CC menu, so we can present this entry
		// this.hideThreshold_ += 1;
		return items;
	}
} );

// We override the default SubsCapsButton
// This saves having to modify the layout etc. but might be fragile
videojs.registerComponent( 'SubsCapsButton', MwSubtitlesButton );

module.exports = MwSubtitlesButton;
