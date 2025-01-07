<?php

namespace MediaWiki\TimedMediaHandler;

use MediaWiki\MediaWikiServices;

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
];
