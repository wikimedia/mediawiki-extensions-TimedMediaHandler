/**
* EmbedPlayer loader
*/
( function () {
	var assignedIndex = 0,
		config = mw.config.get( 'wgTimedMediaHandler' );

	/**
	* Add a DOM ready check for player tags
	* @param {jQuery}
	*/
	function embedPlayerInit( $content ) {
		var inx, checkSetDone,
			$selected = $content.find( config[ 'EmbedPlayer.RewriteSelector' ] );

		if ( $selected.length ) {
			$selected.each( function ( index, playerElement ) {
				var $playerElement = $( playerElement ),
					$parent = $playerElement.parent();
				if ( !$playerElement.hasClass( 'kskin' ) ) {
					// Hack for parsoid-style output without the styles
					// Needed for NWE preview mode, which is parsoid-rendered.
					// Note none of this is needed for videojs mode in future.
					$parent.css( {
						width: $( playerElement ).attr( 'width' ) + 'px',
						height: $( playerElement ).attr( 'height' ) + 'px',
						display: 'block'
					} ).addClass( 'mediaContainer' );
					$playerElement
						.addClass( 'kskin' );
				}

				if ( !playerElement.id ) {
					// Parsoid doesn't give ids to videos in galleries, which confuses
					// mwembed's spinners. Workaround needed for NWE preview mode.
					playerElement.id = 'mwvid_noid' + ( ++assignedIndex );
				}
			} );

			inx = 0;
			checkSetDone = function () {
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
	}
	mw.hook( 'wikipage.content' ).add( embedPlayerInit );

}() );
