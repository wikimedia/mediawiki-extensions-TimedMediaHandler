'use strict';
/**
 * Load media players for a jQuery collection
 * Not chainable
 *
 * @return {jQuery} The media element classes
 * for each of the html elements in the collection
 * @private
 */
function loadMediaPlayer() {
	const MediaElement = require( './ext.tmh.player.element.js' );

	return this.map( function () {
		const mediaElement = new MediaElement( this );
		mediaElement.load();
		return mediaElement;
	} );
}

$.fn.loadMediaPlayer = loadMediaPlayer;

/**
 * Main loader for content
 *
 * @param {jQuery} $content areas to which to apply the hook loaders
 * @private
 */
function loadMediaPlayers( $content ) {
	$content.find( '[data-mw-tmh]' ).loadMediaPlayer();
}

mw.hook( 'wikipage.content' ).add( loadMediaPlayers );
mw.hook( 'wikipage.indicators' ).add( loadMediaPlayers );

/**
 * Loader for iframe mode
 */
$( () => {
	// eslint-disable-next-line no-jquery/no-global-selector
	const $iframeElements = $( '#videoContainer [data-mw-tmh]' );
	if ( !$iframeElements.length ) {
		return;
	}
	// The iframe mode
	mw.loader.using( 'ext.tmh.player.inline' ).then( () => {
		const InlinePlayer = require( 'ext.tmh.player.inline' );
		$iframeElements.each( ( index, mediaElement ) => {
			const inlinePlayer = new InlinePlayer( mediaElement, { fill: true, iframe: true } );
			inlinePlayer.infuse();
			// .then add further customization here
		} );
	} );
} );

// exported object
module.exports = {
	MediaElement: require( './ext.tmh.player.element.js' )
};
