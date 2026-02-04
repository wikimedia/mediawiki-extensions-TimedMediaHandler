<?php

use MediaWiki\TimedMediaHandler\Handlers\MP3Handler\MP3Handler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\MP3Handler\MP3Handler
 */
class MP3HandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new MP3Handler;
	}

	public static function providerSamples(): array {
		return [
			[
				'bunny.stereo.audio.mp3',
				'audio/mpeg',
				[
					'webType' => 'audio/mpeg',
					'streamTypes' => [ 'MP3' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
		];
	}

}
