( function ( mw, $ ) {
	// only enable fullscreen if enabled in iframe
	mw.setConfig( 'EmbedPlayer.EnableFullscreen', document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || false );

	mw.setConfig( 'EmbedPlayer.IsIframeServer', true );

	var iframePlayerInit = function () {
		// rewrite player, normally done by mw.EmbedPlayer.loader upon wikipage.content hook
		var $players = $( mw.config.get( 'EmbedPlayer.RewriteSelector' ) );
		$players.embedPlayer( function () {
			$( '#videoContainer' ).css( {
				visibility: 'visible'
			} );
			$( '#bgimage' ).remove();
		} );
	};

	$( iframePlayerInit );
}( mediaWiki, jQuery ) );
