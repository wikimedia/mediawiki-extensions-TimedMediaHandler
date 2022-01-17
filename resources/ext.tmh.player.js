/**
 * Load video players for a jQuery collection
 * Not chainable
 *
 * @return {jQuery} The media element classes
 * for each of the html elements in the collection
 */
/* eslint-disable no-implicit-globals */
function loadVideoPlayer() {
	var mediaElement,
		MediaElement = require( './ext.tmh.player.element.js' );

	return this.map( function () {
		mediaElement = new MediaElement( this );
		mediaElement.load();
		return mediaElement;
	} );
}

$.fn.loadVideoPlayer = loadVideoPlayer;

/**
 * Main loader for content
 */
mw.hook( 'wikipage.content' ).add( function ( $content ) {
	$content.find( 'video, audio' ).loadVideoPlayer();
} );

/**
 * Loader for iframe mode
 */
$( function () {
	// @fixme load and transform immediately for these?
	// The iframe mode
	// eslint-disable-next-line no-jquery/no-global-selector
	$( '#videoContainer video, #videoContainer audio' ).loadVideoPlayer();
} );

// exported object
module.exports = {
	configuration: require( './config.json' ),

	MediaElement: require( './ext.tmh.player.element.js' )
};
