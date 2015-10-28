/*!
* Javascript to support transcode table on image page
*/
/*global OO*/
( function ( mw, $, OO ) {
	$( document ).ready( function () {
		function errorPopup( event ) {
			var tKey = $( event.target ).attr( 'data-transcodekey' ),
				messageDialog = new OO.ui.MessageDialog(),
				windowManager = new OO.ui.WindowManager();

			event.preventDefault();
			$( 'body' ).append( windowManager.$element );
			windowManager.addWindows( [ messageDialog ] );

			// Configure the message dialog when it is opened with the window manager's openWindow() method.
			windowManager.openWindow( messageDialog, {
				title: mw.msg( 'timedmedia-reset' ),
				message: $( [
						document.createTextNode( mw.msg( 'timedmedia-reset-explanation' ) ),
						document.createElement( 'br' ),
						document.createElement( 'br' ),
						document.createTextNode( mw.msg( 'timedmedia-reset-areyousure' ) )
					] ),
				actions: [
					{
						action: 'reset',
						label: mw.msg( 'timedmedia-reset-button-reset' ),
						flags: [ 'primary', 'destructive' ]
					},
					{
						action: 'cancel',
						label: mw.msg( 'timedmedia-reset-button-cancel' ),
						flags: 'safe'
					}
				]
			} ).then( function ( opened ) {
				opened.then( function ( closing, data ) {
					var api;
					if ( data && data.action === 'reset' ) {
						api = new mw.Api();
						api.postWithEditToken( {
							action: 'transcodereset',
							transcodekey: tKey,
							title: mw.config.get( 'wgPageName' )
						} ).done( function () {
							// Refresh the page
							location.reload();
						} ).fail( function ( code, data ) {
							var errorText;
							if ( data.error && data.error.info ) {
								errorText = data.error.info;
							} else {
								errorText = mw.msg( 'timedmedia-reset-error' );
							}
							windowManager.openWindow( messageDialog, {
								message: errorText,
								actions: [
									{
										action: 'ok',
										label: mw.msg( 'timedmedia-reset-button-dismiss' ),
										flags: 'safe'
									}
								]
							} );
						} );
					}
				} );
			} );
		}

		$( '.mw-filepage-transcodereset a' ).click( errorPopup );
	} );
} )( mediaWiki, jQuery, OO );
