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
			'messages' => [
				"mwe-timedtext-back-btn",
				"mwe-timedtext-layout-off",
				"mwe-timedtext-loading-text",
				"mwe-timedtext-key-language",
				"mwe-timedtext-textcat-cc",
				"mwe-timedtext-textcat-sub",
				"mwe-timedtext-textcat-tad",
				"mwe-timedtext-textcat-ktv",
				"mwe-timedtext-textcat-tik",
				"mwe-timedtext-textcat-ar",
				"mwe-timedtext-textcat-nb",
				"mwe-timedtext-textcat-meta",
				"mwe-timedtext-textcat-trx",
				"mwe-timedtext-textcat-lrc",
				"mwe-timedtext-textcat-lin",
				"mwe-timedtext-textcat-cue",
				"mwe-timedtext-no-subs",
				"mwe-timedtext-language-subtitles-for-clip",
				"mwe-timedtext-language-no-subtitles-for-clip",
				"mwe-timedtext-upload-timed-text",
			],
		],
		'mw.TextSource' => [
			'scripts' => 'resources/mw.TextSource.js',
			'dependencies' => [
				'mediawiki.UtilitiesTime',
				'mw.ajaxProxy',
			]
		]
	];
