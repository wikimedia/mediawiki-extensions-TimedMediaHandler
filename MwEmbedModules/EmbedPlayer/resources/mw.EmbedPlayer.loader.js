/**
* EmbedPlayer loader
*/
( function ( mw ) {
	/**
	* Add a DOM ready check for player tags
	* @param {jQuery}
	*/
	var embedPlayerInit = function ( $content ) {
		var $selected = $content.find( mw.config.get( 'EmbedPlayer.RewriteSelector' ) );
		if ( $selected.length ) {
			var inx = 0;
			var checkSetDone = function () {
				if ( inx < $selected.length ) {
					// put in timeout to avoid browser lockup, and function stack
					$selected.eq( inx ).embedPlayer( function () {
						setTimeout( function () {
							checkSetDone();
						}, 5 );
					} );
				}
				inx++;
			};

			checkSetDone();
		}
	};
	mw.hook( 'wikipage.content' ).add( embedPlayerInit );

}( mediaWiki ) );
