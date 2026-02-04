<?php
namespace MediaWiki\TimedMediaHandler\Test\Integration;

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
					'commonMeta' => [
						'Software' => [ 'Lavf58.45.100' ],
						'Keywords' => [ 'Tractor' ],
						'DateTimeDigitized' => [ 'July 26 2013' ],
						'Copyright' => [ 'Public domain' ]
					],
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
					'commonMeta' => [ 'Software' => [ 'Lavf55.33.101' ] ],
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
					'commonMeta' => [
						'Software' => [
							'Lavf59.27.100',
							'Lavc59.37.100 libopus'
						],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ]
					],
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
					'commonMeta' => [
						'Software' => [
							'Lavf59.27.100',
							'Lavc59.37.100 libvorbis'
						],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ]
					],
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
					'commonMeta' => [
						'Software' => [
							'Lavf59.27.100',
							'Lavc59.37.100 libopus'
						],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ]
					],
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
					'commonMeta' => [
						'Software' => [
							'Lavf59.27.100',
							'Lavc59.37.100 libvorbis'
						],
						'ObjectName' => [ 'Big Buck Bunny, Sunflower version' ]
					],
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
					'commonMeta' => [ 'Software' => [ 'Lavf52.71.0' ] ],
				],
			],
			[
				'av1-sdr.webm',
				'video/webm',
				[
					// AV1 low-res 4:2:0 8-bit BT.709 studio range
					'webType' => 'video/webm; codecs="av01.0.00M.08"',
					'streamTypes' => [ 'AV1' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => [
						'Software' => [
							'Lavf61.7.100',
							'Lavc61.19.100 libsvtav1'
						],
					],
				]
			],
			/*
			// Correctly handling high bit depth, high dynamic range, or wide color gamut
			// requires additions to upstream GetID3 to process the codec private data.
			// Currently only 8-bit SDR rec.709 files will be correctly labeled.
			[
				'av1-hdr.webm',
				'video/webm',
				[
					// AV1 low-res 4:2:0 10-bit BT.2100/PQ studio range
					'webType' => 'video/webm; codecs="av01.0.00M.10.0.110.09.16.09.0"',
					'streamTypes' => [ 'AV1' ],
					'hasVideo' => true,
					'hasAudio' => false,
					'audioChannels' => 0,
					'commonMeta' => [
						'Software' => [
							'Lavf61.7.100',
							'Lavc61.19.100 libsvtav1'
						],
					],
				]
			]
			*/
		];
	}

}
