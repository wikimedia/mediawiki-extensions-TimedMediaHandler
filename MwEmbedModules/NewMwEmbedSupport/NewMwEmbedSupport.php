<?php
return [
	'mw.MwEmbedSupport' => [
		'scripts' => [
			'mw.MwEmbedSupport.js',
		],
		'debugRaw' => false,
		'dependencies' => [
			// jQuery dependencies:
			'jquery.triggerQueueCallback',
			'Spinner',
			'jquery.loadingSpinner',
			'jquery.mwEmbedUtil',
			'mw.MwEmbedSupport.style',
		],
		'messageDir' => 'i18n',
	],
	'Spinner' => [
		'scripts' => 'jquery.loadingSpinner/Spinner.js',
		'dependencies' => [ 'mediawiki.util' ],
	],
	'iScroll' => [
		'scripts' => 'iscroll/src/iscroll.js',
	],
	'jquery.loadingSpinner' => [
		'scripts' => 'jquery.loadingSpinner/jquery.loadingSpinner.js',
	],
	'mw.MwEmbedSupport.style' => [
		// NOTE we add the loadingSpinner.css as a work around to the resource loader register
		// of modules as 'ready' even though only the 'script' part of the module was included.
		'styles'=> [
			'skins/common/MwEmbedCommonStyle.css'
		],
		'skinStyles' => [
			/* shared jQuery ui skin styles */
			'kaltura-dark' => 'skins/jquery.ui.themes/kaltura-dark/jquery-ui-1.7.2.css',
		],
	],
	'mediawiki.UtilitiesTime' => [
		'scripts' => 'mediawiki/mediawiki.UtilitiesTime.js'
	],
	'mediawiki.client' => [
		'scripts' => 'mediawiki/mediawiki.client.js'
	],
	'mediawiki.absoluteUrl' => [
		'scripts' => 'mediawiki/mediawiki.absoluteUrl.js',
		'dependancies' => [ 'mediawiki.Uri' ],
	],
	'mw.ajaxProxy' => [
		'scripts' => 'mediawiki/mediawiki.ajaxProxy.js'
	],
	'fullScreenApi'=> [
		'scripts' => 'fullScreenApi/fullScreenApi.js'
	],
	'jquery.embedMenu' => [
		'scripts' => 'jquery.embedMenu/jquery.embedMenu.js',
		'styles' => 'jquery.embedMenu/jquery.embedMenu.css'
	],
	'jquery.ui.touchPunch' => [
		'scripts' => 'jquery/jquery.ui.touchPunch.js',
		'dependencies' => [
			'jquery.ui.core',
			'jquery.ui.mouse'
		]
	],
	// Startup modules must set debugRaw to false
	'jquery.triggerQueueCallback' => [
		'scripts'=> 'jquery/jquery.triggerQueueCallback.js',
		'debugRaw' => false
	],
	'jquery.mwEmbedUtil' => [
		'scripts' => 'jquery/jquery.mwEmbedUtil.js',
		'debugRaw' => false,
	],
	'jquery.debouncedresize' => [
		'scripts' => 'jquery/jquery.debouncedresize.js'
	],
];
