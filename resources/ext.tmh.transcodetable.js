/*!
* Javascript to support transcode table on image page
*/
( function ( mw, $, OO ) {
	$( function () {
		function errorPopup( event ) {
			var tKey = $( event.target ).attr( 'data-transcodekey' ),
				message = $( [
					document.createTextNode( mw.msg( 'timedmedia-reset-explanation' ) ),
					document.createElement( 'br' ),
					document.createElement( 'br' ),
					document.createTextNode( mw.msg( 'timedmedia-reset-areyousure' ) )
				] );

			event.preventDefault();

			OO.ui.confirm( message, {
				title: mw.msg( 'timedmedia-reset' ),
				actions: [
					{
						action: 'accept',
						label: mw.msg( 'timedmedia-reset-button-reset' ),
						flags: [ 'primary', 'destructive' ]
					},
					{
						action: 'cancel',
						label: mw.msg( 'timedmedia-reset-button-cancel' ),
						flags: 'safe'
					}
				]
			} ).done( function ( confirmed ) {
				var api;
				if ( confirmed ) {
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
						OO.ui.alert( errorText, {
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
		}

		$( '.mw-filepage-transcodereset a' ).on( 'click', errorPopup );
	} );
}( mediaWiki, jQuery, OO ) );
