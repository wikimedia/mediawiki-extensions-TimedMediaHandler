<?php

use MediaWiki\TimedMediaHandler\Handlers\WebMHandler\WebMHandler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\WebMHandler\WebMHandler
 */
class WebMHandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new WebMHandler;
	}

	public static function providerSamples(): array {
		return [
			[
				'VP9-tractor.webm',
				'video/webm',
				[
					'webType' => 'video/webm; codecs="vp9"',
					'streamTypes' => [ 'VP9' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => false,
				],
			],
			[
				'bear-vp9-opus.webm',
				'video/webm',
				[
					'webType' => 'video/webm; codecs="vp9, opus"',
					'streamTypes' => [ 'VP9', 'Opus' ],
					'hasVideo' => true,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.stereo.audio.opus.webm',
				'audio/webm',
				[
					'webType' => 'audio/webm; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.stereo.audio.vorbis.webm',
				'audio/webm',
				[
					'webType' => 'audio/webm; codecs="vorbis"',
					'streamTypes' => [ 'Vorbis' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.surround.audio.opus.webm',
				'audio/webm',
				[
					'webType' => 'audio/webm; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => false,
				],
			],
			[
				'bunny.surround.audio.vorbis.webm',
				'audio/webm',
				[
					'webType' => 'audio/webm; codecs="vorbis"',
					'streamTypes' => [ 'Vorbis' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => false,
				],
			],
			[
				'shuttle10seconds.1080x608.webm',
				'video/webm',
				[
					'webType' => 'video/webm; codecs="vp8"',
					'streamTypes' => [ 'VP8' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => false,
				],
			],
		];
	}

}
