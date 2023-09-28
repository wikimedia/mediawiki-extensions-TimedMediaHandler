<?php

use MediaWiki\TimedMediaHandler\Handlers\MP4Handler\MP4Handler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\MP4Handler\MP4Handler
 */
class MP4HandlerTest extends TimedMediaHandlerTestCase {

	/**
	 * @todo resolve these borked fragmented test files
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->markTestSkipped( 'Fragmented MP4 parsing currently broken, needs work' );
	}

	protected function getHandler(): TimedMediaHandler {
		return new MP4Handler;
	}

	public static function providerSamples(): array {
		return [
			// Fragmented MP4s for HLS track downloads
			// These currently don't work for import, mostly because
			// getID3 doesn't support fragmented MP4s. We can work
			// around that in future by parsing the metadata ourselves
			// perhaps.
			[
				'bunny.stereo.audio.opus.mp4',
				'audio/mp4',
				[
					'webType' => 'audio/mp4; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
			[
				'bunny.surround.audio.opus.mp4',
				'audio/mp4',
				[
					'webType' => 'audio/mp4; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => false,
				],
			],
			[
				'stream.240p.video.vp9.mp4',
				'video/mp4',
				[
					'webType' => 'video/mp4; codecs="vp09.00.10.08"',
					'streamTypes' => [ 'VP9' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => false,
				],
			],
			[
				'stream.stereo.audio.opus.mp4',
				'audio/mp4',
				[
					'webType' => 'audio/mp4; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
				],
			],
		];
	}

}
