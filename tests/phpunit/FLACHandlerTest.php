<?php

use MediaWiki\TimedMediaHandler\Handlers\FLACHandler\FLACHandler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\FLACHandler\FLACHandler
 */
class FLACHandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new FLACHandler;
	}

	public static function providerSamples(): array {
		return [
			[
				'bunny.stereo.audio.flac',
				'audio/flac',
				[
					'webType' => 'audio/flac',
					'streamTypes' => [ 'FLAC' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.surround.audio.flac',
				'audio/flac',
				[
					'webType' => 'audio/flac',
					'streamTypes' => [ 'FLAC' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => false,
				],
			],
		];
	}

}
