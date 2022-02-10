/* eslint-disable no-implicit-globals */
/**
 * Use the function to initialise a MediaDialog and videoJS player
 * Depends on jQuery and OOUI
 */

/**
 * Currently expects to be run on jQuery element
 *
 * @param {HTMLMediaElement} element
 * @return {jQuery.Promise} promise which resolves after the dialog closes
 */
function initMediaPlayerDialog( element ) {
	var MediaDialog = require( './ext.tmh.player.dialog.js' ),
		$video = $( element ).clone(),
		isAudio = element.nodeName.toLowerCase() === 'audio';

	if ( isAudio ) {
		$video.attr( 'poster', mw.config.get( 'wgExtensionAssetsPath' ) +
			'/TimedMediaHandler/resources/poster-audio.svg' );
	} else {
		// Do not show a poster when opening a dialog with autoplay
		// This just causes yet another UI change
		$video.removeAttr( 'poster' );
	}
	return $.Deferred( function ( deferred ) {
		var NS_FILE = mw.config.get( 'wgNamespaceIds' ).file,
			windowManager = OO.ui.getWindowManager(),
			dialog = new MediaDialog( {
				size: isAudio ? 'medium' : 'larger',
				$video: $video
			} ),
			title,
			win;

		$( document.body ).append( windowManager.$element );
		windowManager.addWindows( [ dialog ] );
		if ( $video.data( 'mwtitle' ) ) {
			title = mw.Title.newFromText( $video.data( 'mwtitle' ), NS_FILE ).getMainText();
		}
		win = windowManager.openWindow( dialog, {
			title: title
		} );

		win.opened.then( function () {
			dialog.play();
		} );

		win.closed.then( function () {
			dialog.stop();
			deferred.resolve();
		} );
	} ).promise();
}

/**
 * jQuery version of initMediaPlayerDialog
 *
 * @return {jQuery.Promise}
 */
$.fn.showVideoPlayerDialog = function showVideoPlayerDialog() {
	return initMediaPlayerDialog( this.get( 0 ) );
};

module.exports = {
	MediaDialog: require( './ext.tmh.player.dialog.js' ),

	initMediaPlayerDialog: initMediaPlayerDialog
};
