<?php

use MediaWiki\TimedMediaHandler\Handlers\OggHandler\OggHandler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers MediaWiki\TimedMediaHandler\Handlers\OggHandler\OggHandler
 */
class OggHandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new OggHandler;
	}

	public static function providerSamples(): array {
		return [
			[
				'broken-file.ogg',
				'application/ogg',
				[
					// XXX: This behaviour is somewhat questionable. It perhaps should be
					// application/ogg in this case.
					'webType' => 'audio/ogg',
					'streamTypes' => [],
					'hasVideo' => false,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => [],
				]
			],
			[
				'bunny.stereo.audio.opus',
				'application/ogg',
				[
					'webType' => 'audio/ogg; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => [
						'Software' => [ 'Lavf59.27.100', 'Lavc59.37.100 libopus' ],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ],
					],
				],
			],
			[
				'bunny.stereo.audio.vorbis.ogg',
				'application/ogg',
				[
					'webType' => 'audio/ogg; codecs="vorbis"',
					'streamTypes' => [ 'Vorbis' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => [
						'Software' => [ 'Lavf59.27.100', 'Lavc59.37.100 libvorbis' ],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ],
					],
				]
			],
			[
				'bunny.surround.audio.opus',
				'application/ogg',
				[
					'webType' => 'audio/ogg; codecs="opus"',
					'streamTypes' => [ 'Opus' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => [
						'Software' => [ 'Lavf59.27.100', 'Lavc59.37.100 libopus' ],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ],
					],
				],
			],
			[
				'bunny.surround.audio.vorbis.ogg',
				'application/ogg',
				[
					'webType' => 'audio/ogg; codecs="vorbis"',
					'streamTypes' => [ 'Vorbis' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 6,
					'commonMeta' => [
						'Software' => [ 'Lavf59.27.100', 'Lavc59.37.100 libvorbis' ],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ],
					],
				]
			],
			[
				'doubleTag.oga',
				'application/ogg',
				[
					'webType' => 'audio/ogg; codecs="vorbis"',
					'streamTypes' => [ 'Vorbis' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 1,
					'commonMeta' => [
						'Artist' => [ 'Brian', 'Bawolff' ],
						'Software' => [ 'Lavf55.10.2' ]
					],
				],
			],
			[
				'test5seconds.electricsheep.300x400.ogv',
				'application/ogg',
				[
					'webType' => 'video/ogg; codecs="theora"',
					'streamTypes' => [ 'Theora' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => [
						'Software' => [ 'Lavf53.21.1' ],
						'ObjectName' => [ 'Electric Sheep' ],
						'UserComment' => [ '🐑' ]
					],
				],
			],
		];
	}

}
