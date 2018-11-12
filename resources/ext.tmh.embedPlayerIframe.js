( function () {
	var config = mw.config.get( 'wgTimedMediaHandler' ),
		iframePlayerInit = function () {
			// rewrite player, normally done by mw.EmbedPlayer.loader upon wikipage.content hook
			var $players = $( config[ 'EmbedPlayer.RewriteSelector' ] );
			$players.embedPlayer();
		};

	// only enable fullscreen if enabled in iframe
	config[ 'EmbedPlayer.EnableFullscreen' ] = document.fullscreenEnabled || document.webkitFullscreenEnabled || document.mozFullScreenEnabled || false;
	config[ 'EmbedPlayer.IsIframeServer' ] = true;

	$( iframePlayerInit );
}() );
