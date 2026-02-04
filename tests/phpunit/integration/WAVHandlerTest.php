<?php

use MediaWiki\TimedMediaHandler\Handlers\WAVHandler\WAVHandler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\WAVHandler\WAVHandler
 */
class WAVHandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new WAVHandler;
	}

	/**
	 * @todo not sure if "WAV" is a proper codec but that's
	 * what we get from getid3 :D
	 */
	public static function providerSamples(): array {
		return [
			[
				'bunny.stereo.audio.pcm16.wav',
				'audio/wav',
				[
					'webType' => 'audio/wav',
					'streamTypes' => [ 'WAV' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.surround.audio.pcm16.wav',
				'audio/wav',
				[
					'webType' => 'audio/wav',
					'streamTypes' => [ 'WAV' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => false,
				],
			],
		];
	}

}
