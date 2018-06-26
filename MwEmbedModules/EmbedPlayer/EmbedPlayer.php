<?php
	global $wgVideoPlayerSkinModule;
	// Register all the EmbedPlayer modules
	return [
			'jquery.embedPlayer' => [ 'scripts' => 'resources/jquery.embedPlayer.js' ],
			'mw.EmbedPlayer.loader' => [
				'scripts' => 'resources/mw.EmbedPlayer.loader.js',
				'dependencies' => 'jquery.embedPlayer',
			],
			'mw.MediaElement' => [
				'scripts' => 'resources/mw.MediaElement.js',
				'dependencies' => [
					'ext.tmh.OgvJsSupport',
				],
			],
			'mw.MediaPlayer' => [ 'scripts' => 'resources/mw.MediaPlayer.js' ],
			'mw.MediaPlayers' => [
				'scripts' => 'resources/mw.MediaPlayers.js',
				'dependencies' => 'mw.MediaPlayer'
			],
			'mw.MediaSource' => [
				'scripts' => 'resources/mw.MediaSource.js',
				'dependencies' => 'mw.MwEmbedSupport',
			],
			'mw.EmbedTypes' => [
				'scripts' => 'resources/mw.EmbedTypes.js',
				'dependencies' =>  [
					'mw.MediaPlayers',
					'mediawiki.Uri',
					'jquery.client',
				]
			],
			'mw.EmbedPlayer' => [
				'scripts' => [
					'resources/mw.processEmbedPlayers.js',
					'resources/mw.EmbedPlayer.js',
					'resources/skins/mw.PlayerControlBuilder.js',
				],
				'dependencies' => [
					// mwEmbed support module
					'mediawiki.client',
					'mediawiki.UtilitiesTime',
					'mediawiki.Uri',
					'mediawiki.absoluteUrl',
					'mediawiki.jqueryMsg',

					// Browser fullscreen api support:
					'fullScreenApi',

					// Kinda need this
					'mw.MwEmbedSupport',

					// We always end up loading native player
					'mw.EmbedPlayerNative',

					// Sub classes:
					'mw.MediaElement',
					'mw.MediaPlayers',
					'mw.MediaSource',
					'mw.EmbedTypes',

					// jQuery dependencies:
					'jquery.client',
					'jquery.hoverIntent',
					'jquery.cookie',
					'jquery.ui.mouse',
					'jquery.debouncedresize',
					'jquery.embedMenu',
					'jquery.ui.slider',
					'jquery.ui.touchPunch',

					// Set to mw.PlayerSkinKskin or mw.PlayerSkinMvpcf in config
					$wgVideoPlayerSkinModule
				],
				'styles' => 'resources/skins/EmbedPlayer.css',
				'messageDir' => 'i18n',
			],

			'mw.EmbedPlayerKplayer'	=> [ 'scripts' => 'resources/mw.EmbedPlayerKplayer.js' ],
			'mw.EmbedPlayerGeneric'	=> [ 'scripts' => 'resources/mw.EmbedPlayerGeneric.js' ],
			'mw.EmbedPlayerNative'	=> [ 'scripts' => 'resources/mw.EmbedPlayerNative.js' ],
			'mw.EmbedPlayerVLCApp'	=> [
				'scripts' => 'resources/mw.EmbedPlayerVLCApp.js',
				'dependencies' => [ 'mediawiki.Uri' ]
			],
			'mw.EmbedPlayerIEWebMPrompt' => [
				'scripts' => 'resources/mw.EmbedPlayerIEWebMPrompt.js',
				'styles' => 'resources/mw.EmbedPlayerIEWebMPrompt.css',
			],
			'mw.EmbedPlayerOgvJs' => [
				'scripts' => 'resources/mw.EmbedPlayerOgvJs.js',
				'dependencies' => [
					'jquery.spinner',
					'ext.tmh.OgvJsSupport',
				],
			],
			'mw.EmbedPlayerImageOverlay' => [ 'scripts' => 'resources/mw.EmbedPlayerImageOverlay.js' ],

			'mw.EmbedPlayerVlc' => [ 'scripts' => 'resources/mw.EmbedPlayerVlc.js' ],

			'mw.PlayerSkinKskin' => [
				'scripts' => 'resources/skins/kskin/mw.PlayerSkinKskin.js',
				'styles' => 'resources/skins/kskin/PlayerSkinKskin.css'
			],

			'mw.PlayerSkinMvpcf' => [
				'scripts' => 'resources/skins/mvpcf/mw.PlayerSkinMvpcf.js',
				'styles' => 'resources/skins/mvpcf/PlayerSkinMvpcf.css'
			],
	];
?>
