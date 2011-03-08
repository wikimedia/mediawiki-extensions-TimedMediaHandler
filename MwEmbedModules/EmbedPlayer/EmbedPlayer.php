<?php

	// Register all the EmbedPlayer modules 
	return array(
			"MediaElement" => array( 'scripts' => 'resources/MediaElement.js' ),
			"MediaPlayer" => array( 'scripts' => 'resources/MediaPlayer.js' ),
			"MediaPlayers" => array( 
				'scripts' => 'resources/MediaPlayers.js',
				'dependencies' => 'MediaPlayer'
			),
			"MediaSource" => array( 'scripts' => 'resources/MediaSource.js' ),
			"mw.EmbedTypes" => array( 
				'scripts' => 'resources/mw.EmbedTypes.js', 
				'dependencies' => 'MediaPlayers'
			),

			"mw.EmbedPlayer" => array( 
				'scripts' => array( 
					"resources/mw.EmbedPlayer.js", 
					"resources/skins/mw.PlayerControlBuilder.js",
				),
				'dependencies' => array(
					// mwEmbed support module 
					'mwEmbedSupport',
					'mediawiki.client',
					'mediawiki.UtilitiesTime',
					'mediawiki.Uri',
				
					// Sub classes:
					'MediaElement',
					'MediaPlayers',
					'MediaSource',
					'mw.EmbedTypes',
				
					// jQuery dependencies: 
					'jquery.client',
					'jquery.hoverIntent',
					'jquery.cookie',
					'jquery.ui.mouse',
					'jquery.menu',
					'jquery.ui.slider'					
				),
				'styles' => "resources/skins/EmbedPlayer.css",
				'messageFile' => 'EmbedPlayer.i18n.php',		
			),
				
			"mw.EmbedPlayerKplayer"	=> array( 'scripts'=> "resources/mw.EmbedPlayerKplayer.js" ),
			"mw.EmbedPlayerGeneric"	=> array( 'scripts'=> "resources/mw.EmbedPlayerGeneric.js" ),
			"mw.EmbedPlayerJava" => array( 'scripts'=> "resources/mw.EmbedPlayerJava.js"),
			"mw.EmbedPlayerNative"	=> array( 'scripts'=> "resources/mw.EmbedPlayerNative.js" ),
			
			"mw.EmbedPlayerVlc" => array( 'scripts'=> "resources/mw.EmbedPlayerVlc.js" ),
			
			"mw.IFramePlayerApiServer" => array( 'scripts' => "resources/iframeApi/mw.IFramePlayerApiServer.js" ),
			"mw.IFramePlayerApiClient" => array( 'scripts' => "resources/iframeApi/mw.IFramePlayerApiClient.js" ),
		
			"mw.PlayerSkinKskin" => array( 	'scripts' => "resources/skins/kskin/mw.PlayerSkinKskin.js",
											'styles' => "resources/skins/kskin/PlayerSkinKskin.css"),
			
			"mw.PlayerSkinMvpcf" => array( 	'scripts'=> "resources/skins/mvpcf/mw.PlayerSkinMvpcf.js", 
											'styles'=> "resources/skins/mvpcf/PlayerSkinMvpcf.css"),
	);
?>