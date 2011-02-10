<?php

	// Register all the EmbedPlayer modules 
	return array(
			"mw.EmbedPlayer" => array( 
				'scripts' => array( 
					"players/mw.EmbedPlayer.js", 
					"skins/mw.PlayerControlBuilder.js",
				),
				'dependencies' => array(
					// mwEmbed support: 
					'mwEmbedSupport',
				
					// jQuery dependencies: 
					'jquery.hoverIntent',
					'jquery.cookie',
					'jquery.ui.mouse',
					'jquery.menu',
					'jquery.ui.slider'					
				),
				'styles' => "skins/mw.style.EmbedPlayer.css",
				'messageFile' => 'EmbedPlayer.i18n.php',		
			),
				
			"mw.EmbedPlayerKplayer"	=> array( 'scripts'=> "players/mw.EmbedPlayerKplayer.js" ),
			"mw.EmbedPlayerGeneric"	=> array( 'scripts'=> "players/mw.EmbedPlayerGeneric.js" ),
			"mw.EmbedPlayerJava" => array( 'scripts'=> "players/mw.EmbedPlayerJava.js"),
			"mw.EmbedPlayerNative"	=> array( 'scripts'=> "players/mw.EmbedPlayerNative.js" ),
			
			"mw.EmbedPlayerVlc" => array( 'scripts'=> "players/mw.EmbedPlayerVlc.js" ),
			
			"mw.IFramePlayerApiServer" => array( 'scripts' => "iframeApi/mw.IFramePlayerApiServer.js" ),
			"mw.IFramePlayerApiClient" => array( 'scripts' => "iframeApi/mw.IFramePlayerApiClient.js" ),
		
			"mw.PlayerSkinKskin" => array( 	'scripts' => "skins/kskin/mw.PlayerSkinKskin.js",
											'styles' => "skins/kskin/mw.style.PlayerSkinKskin.css"),
			
			"mw.PlayerSkinMvpcf" => array( 	'scripts'=> "skins/mvpcf/mw.PlayerSkinMvpcf.js", 
											'styles'=> "skins/mvpcf/mw.style.PlayerSkinMvpcf.css"),
	);
?>