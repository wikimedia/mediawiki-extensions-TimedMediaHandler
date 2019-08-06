<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

// T191668
$cfg['suppress_issue_types'][] = 'PhanParamTooMany';

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
