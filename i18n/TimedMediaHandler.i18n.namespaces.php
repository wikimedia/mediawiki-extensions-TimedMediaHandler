<?php
/**
 * Namespace translations for TimedMediaHandler
 *
 * @file
 * @ingroup Extensions
 */

global $wgTimedMediaHandlerNamespaceNames, $wgTimedMediaHandlerNamespaceAliases;
$wgTimedMediaHandlerNamespaceNames = [];
$wgTimedMediaHandlerNamespaceAliases = [];

/** English */
$wgTimedMediaHandlerNamespaceNames['en'] = [
	'timedtext'      => 'TimedText',
	'timedtext_talk' => 'TimedText_talk',
];

// Be aware that fallback languages are not processed in the setup
// of these namespaces, because we can't safely load the fallback lists
// yet at namespace setup time.
//
// Add entries for all languages that need to use the fallbacks

// T373239
$wgTimedMediaHandlerNamespaceNames['ary'] = [
	'timedtext'      => 'سوتيتر',
	'timedtext_talk' => 'لمداكرة_د_سوتيتر',
];

// T122127
$wgTimedMediaHandlerNamespaceNames['glk'] = [
	'timedtext'      => 'همزمت_وؤت',
	'timedtext_talk' => 'همزمت_وؤتˇ_گب',
];

// T315715
$wgTimedMediaHandlerNamespaceNames['is'] = [
	'timedtext'      => 'Skjátexti',
	'timedtext_talk' => 'Skjátextaspjall',
];

$wgTimedMediaHandlerNamespaceNames['nb'] = [
	'timedtext'      => 'Undertekster',
	'timedtext_talk' => 'Undertekstdiskusjon',
];

$wgTimedMediaHandlerNamespaceAliases['nb'] = [
	'timedtext' => [ 'Undertekst' ],
];

$wgTimedMediaHandlerNamespaceNames['nn'] = [
	'timedtext'      => 'Undertekstar',
	'timedtext_talk' => 'Undertekstdiskusjon',
];

$wgTimedMediaHandlerNamespaceAliases['nn'] = [
	'timedtext' => [ 'Undertekst' ],
];

// Fallback chains are not handled automatically
$wgTimedMediaHandlerNamespaceNames['no'] = $wgTimedMediaHandlerNamespaceNames['nb'];
$wgTimedMediaHandlerNamespaceAliases['no'] = $wgTimedMediaHandlerNamespaceAliases['nb'];

// T406997
$wgTimedMediaHandlerNamespaceNames['ps'] = [
	'timedtext'      => 'مهال‌ليک',
	'timedtext_talk' => 'د مهال‌ليک خبرې اترې',
];

// T391946
$wgTimedMediaHandlerNamespaceNames['uk'] = [
	'timedtext'      => 'Субтитри',
	'timedtext_talk' => 'Обговорення_субтитрів',
];
