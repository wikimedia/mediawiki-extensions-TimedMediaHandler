/* eslint-disable no-jquery/no-global-selector */
$( function () {
	$( '#timedmedia-tt-go' ).on( 'click', function () {
		window.location = mw.util.getUrl(
			mw.config.get( 'wgPageName' ) + '.' + $( '#timedmedia-tt-input' ).val() + '.srt',
			{ action: 'edit' }
		);
	} );
} );
