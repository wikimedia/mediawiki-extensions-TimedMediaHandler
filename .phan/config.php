<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['autoload_internal_extension_signatures'] = [
	'pcntl' => $IP . '/.phan/internal_stubs/pcntl.phan_php',
];

return $cfg;
