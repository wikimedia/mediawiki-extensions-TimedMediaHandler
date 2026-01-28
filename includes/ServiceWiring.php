<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets;

/** @phpcs-require-sorted-array */
return [
	'TimedMediaHandler.TimedMediaThumbnail' => static function (
		MediaWikiServices $services
	): TimedMediaThumbnail {
		return new TimedMediaThumbnail(
			$services->getMainConfig(),
			$services->getRepoGroup()
		);
	},
	'TimedMediaHandler.TranscodePresets' => static function (
		MediaWikiServices $services
	): TranscodePresets {
		return new TranscodePresets( $services->getMainConfig() );
	},
];
