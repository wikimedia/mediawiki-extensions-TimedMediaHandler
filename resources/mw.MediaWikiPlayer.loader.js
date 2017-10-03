( function ( mw, $ ) {
	// Add MediaWikiSupportPlayer dependency on players with a mediaWiki title
	$( mw ).on( 'EmbedPlayerUpdateDependencies', function ( event, embedPlayer, dependencySet ) {
		if ( $( embedPlayer ).attr( 'data-mwtitle' ) ) {
			$.merge( dependencySet, [ 'mw.MediaWikiPlayerSupport' ] );
		}
	} );
}( mediaWiki, jQuery ) );
