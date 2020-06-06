<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['autoload_internal_extension_signatures'] = [
	'pcntl' => $IP . '/.phan/internal_stubs/pcntl.phan_php',
];

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		'../../extensions/BetaFeatures',
	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'includes/handlers/OggHandler/File_Ogg',
		'../../extensions/BetaFeatures',
	]
);

return $cfg;
