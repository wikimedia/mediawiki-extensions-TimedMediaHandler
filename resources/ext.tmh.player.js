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
 *
 * @param {jQuery} $content areas to which to apply the hook loaders
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
	var $iframeElements = $( '#videoContainer video, #videoContainer audio' );
	if ( !$iframeElements.length ) {
		return;
	}
	// The iframe mode
	mw.loader.using( 'ext.tmh.player.inline' ).then( function () {
		$iframeElements.transformVideoPlayer( { fill: true } ).then( function ( $videojs ) {
			var player = $videojs[ 0 ];
			player.ready( function () {
				// Add further customizations here
			} );
		} );
	} );
} );

// exported object
module.exports = {
	configuration: require( './config.json' ),

	MediaElement: require( './ext.tmh.player.element.js' )
};
