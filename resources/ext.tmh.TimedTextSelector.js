( function ( $, mw ) {
	$( function () {
		$( '#timedmedia-tt-go' ).click( function () {
			window.location = mw.config.get( 'wgScript' ) + '?title=' + mw.util.wikiUrlencode( mw.config.get( 'wgPageName' ) ) + '.' + $( '#timedmedia-tt-input' ).val() + '.srt&action=edit';
		} );
	} );

} )( jQuery, mediaWiki );
