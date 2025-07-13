<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

// TODO Fix these issues, suppressed to allow upgrading
$cfg['suppress_issue_types'][] = 'PhanThrowTypeAbsent';

$cfg['autoload_internal_extension_signatures'] = [
	'pcntl' => $IP . '/.phan/internal_stubs/pcntl.phan_php',
];

return $cfg;
