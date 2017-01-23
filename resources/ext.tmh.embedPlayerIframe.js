( function ( mw, $ ) {
	var iframePlayerInit = function () {
		// rewrite player, normally done by mw.EmbedPlayer.loader upon wikipage.content hook
		var $players = $( mw.config.get( 'EmbedPlayer.RewriteSelector' ) );
		$players.embedPlayer();
	};

	// only enable fullscreen if enabled in iframe
	mw.setConfig( 'EmbedPlayer.EnableFullscreen', document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || false );

	mw.setConfig( 'EmbedPlayer.IsIframeServer', true );

	$( iframePlayerInit );
}( mediaWiki, jQuery ) );
