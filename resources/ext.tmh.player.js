'use strict';
/**
 * Load video players for a jQuery collection
 * Not chainable
 *
 * @return {JQuery} The media element classes
 * for each of the html elements in the collection
 * @private
 */
function loadVideoPlayer() {
	const MediaElement = require( './ext.tmh.player.element.js' );

	return this.map( function () {
		const mediaElement = new MediaElement( this );
		mediaElement.load();
		return mediaElement;
	} );
}

$.fn.loadVideoPlayer = loadVideoPlayer;

/**
 * Main loader for content
 *
 * @param {JQuery} $content areas to which to apply the hook loaders
 * @private
 */
function loadVideoPlayers( $content ) {
	$content.find( 'video, audio' ).loadVideoPlayer();
}

mw.hook( 'wikipage.content' ).add( loadVideoPlayers );
mw.hook( 'wikipage.indicators' ).add( loadVideoPlayers );

/**
 * Loader for iframe mode
 */
$( function () {
	// eslint-disable-next-line no-jquery/no-global-selector
	const $iframeElements = $( '#videoContainer video, #videoContainer audio' );
	if ( !$iframeElements.length ) {
		return;
	}
	// The iframe mode
	mw.loader.using( 'ext.tmh.player.inline' ).then( function () {
		const InlinePlayer = require( 'ext.tmh.player.inline' );
		$iframeElements.each( function ( index, mediaElement ) {
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
