/**
 * @type {videojs.SubsCapsButton}
 */
const SubsCapsButton = videojs.getComponent( 'SubsCapsButton' );
const MwCreateSubtitlesMenuItem = require( './mw-subtitles-create.js' );

/**
 * Replaces the standard subtitles and captions button with a variant
 * that in addition, shows the {@link MwCreateSubtitlesMenuItem} in it's menu
 *
 * @extends videojs.SubsCapsButton
 */
class MwSubtitlesButton extends SubsCapsButton {
	/**
	 * @param {videojs.TextTrackMenuItem[]} items
	 * @param {videojs.TextTrackMenuItem} menuitem
	 * @return {MwCreateSubtitlesMenuItem[]}
	 */
	createItems( items, menuitem ) {
		/* eslint-disable no-underscore-dangle */
		items = super.createItems( this, items, menuitem );
		const item = new MwCreateSubtitlesMenuItem( this.player_, { kind: this.label_ } );

		if ( items.length <= this.hideThreshold_ ) {
			// For now always show the CC menu, so we can present this entry
			// If the only other items are the hideable ones, then reset the menu.
			items = [ item ];
			this.hideThreshold_ = 0;
		} else {
			items.splice( 1, 0, item );
			this.hideThreshold_ += 1;
		}
		/* eslint-enable no-underscore-dangle */
		return items;
	}
}

// We override the default SubsCapsButton
// This saves having to modify the layout etc. but might be fragile
videojs.registerComponent( 'SubsCapsButton', MwSubtitlesButton );

module.exports = MwSubtitlesButton;
