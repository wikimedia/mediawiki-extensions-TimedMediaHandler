<?php

	// Register all the timedText modules
	return [
		'mw.TimedText.loader' => [
			'scripts' => 'TimedText.loader.js',
		],
		'mw.TimedText' => [
			'scripts' => 'resources/mw.TimedText.js',
			'styles' => 'resources/mw.style.TimedText.css',
			'dependencies' => [
				'mw.EmbedPlayer',
				'mw.TextSource',
				'mw.MwEmbedSupport',
			],
			'messages' => NewMwEmbedResourceManager::readJSONFileMessageKeys(
				__DIR__ . '/i18n/en.json' ),
		],
		'mw.TextSource' => [
			'scripts' => 'resources/mw.TextSource.js',
			'dependencies' => [
				'mediawiki.UtilitiesTime',
				'mw.ajaxProxy',
			]
		]
	];
