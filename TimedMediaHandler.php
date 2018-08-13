<?php

/**
 * TimedMediaHandler extension
 *
 * This PHP entry point is deprecated. Please use wfLoadExtension() and the extension.json file
 * instead. See https://www.mediawiki.org/wiki/Manual:Extension_registration for more details.
 *
 * @file
 * @ingroup Extensions
 * @license GPL-2.0-or-later
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'TimedMediaHandler' );

	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['TimedMediaHandler'] = __DIR__ . '/i18n';
	$wgMessagesDirs['TimedMediaHandler.api'] = __DIR__ . '/i18n/api';
	$wgMessagesDirs['MwEmbed.NewMwEmbedSupport'] = __DIR__ . '/i18n/MwEmbedSupport';
	$wgMessagesDirs['MwEmbed.EmbedPlayer'] = __DIR__ . '/i18n/EmbedPlayer';
	$wgMessagesDirs['MwEmbed.TimedText'] = __DIR__ . '/i18n/TimedText';

	$wgExtensionMessagesFiles['TimedMediaHandlerMagic'] =
		__DIR__ . '/TimedMediaHandler.i18n.magic.php';
	$wgExtensionMessagesFiles['TimedMediaHandlerAliases'] =
		__DIR__ . '/TimedMediaHandler.i18n.alias.php';

	wfWarn(
		'Deprecated PHP entry point used for TimedMediaHandler extension. Please use wfLoadExtension '.
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
}

die( 'This version of the TimedMediaHandler extension requires MediaWiki 1.29+.' );
