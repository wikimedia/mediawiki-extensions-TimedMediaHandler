/**
 * Use the function to initialise a MediaDialog and videoJS player
 * Depends on jQuery and OOUI
 */

/**
 * Currently expects to be run on jQuery element
 *
 * @param {HTMLMediaElement} element
 * @return {jQuery.Promise<void>} promise which resolves after the dialog closes
 * @private
 */
function initMediaPlayerDialog( element ) {
	const MediaDialog = require( './ext.tmh.player.dialog.js' ),
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
	return $.Deferred( ( deferred ) => {
		const NS_FILE = mw.config.get( 'wgNamespaceIds' ).file;
		const resource = element.getAttribute( 'resource' );
		const resourceTitle = resource ?
			decodeURIComponent( resource.slice( resource.lastIndexOf( '/' ) + 1 ) ) :
			$video.data( 'mwtitle' );
		let title;
		if ( resourceTitle ) {
			title = mw.Title.newFromText( resourceTitle, NS_FILE );
		}

		const windowManager = OO.ui.getWindowManager();
		const dialog = new MediaDialog( {
			size: !isAudio ? 'larger' : 'medium',
			$video: $video,
			title: title
		} );

		$( document.body ).append( windowManager.$element );
		windowManager.addWindows( [ dialog ] );

		const win = windowManager.openWindow( dialog, {
			title: title.getMainText()
		} );

		win.opened.then( () => {
			dialog.play();
		} );

		win.closed.then( () => {
			dialog.stop();
			deferred.resolve();
		} );
	} ).promise();
}

/**
 * jQuery version of initMediaPlayerDialog
 *
 * @return {jQuery.Promise<void>}
 */
$.fn.showVideoPlayerDialog = function showVideoPlayerDialog() {
	return initMediaPlayerDialog( this.get( 0 ) );
};

module.exports = {
	MediaDialog: require( './ext.tmh.player.dialog.js' ),

	initMediaPlayerDialog: initMediaPlayerDialog
};
