<?php

	// Register all the timedText modules
	return [
		'mw.TimedText' => [
			'scripts' => 'resources/mw.TimedText.js',
			'styles' => 'resources/mw.style.TimedText.css',
			'dependencies' => [
				'mw.EmbedPlayer',
				'mw.TextSource',
				'mw.MwEmbedSupport',
			],
			'messageDir' => 'i18n',
		],
		'mw.TextSource' => [
			'scripts' => 'resources/mw.TextSource.js',
			'dependencies' => [
				'mediawiki.UtilitiesTime',
				'mw.ajaxProxy',
			]
		]
	];
