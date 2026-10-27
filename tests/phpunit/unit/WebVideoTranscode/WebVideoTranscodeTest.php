<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\WebVideoTranscode;

use MediaWiki\Config\HashConfig;
use MediaWiki\FileRepo\File\LocalFile;
use MediaWiki\TimedMediaHandler\Handlers\WebMHandler\WebMHandler;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

/**
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode
 */
class WebVideoTranscodeTest extends \MediaWikiUnitTestCase {
	public static function provideGetDashChunkNames() {
		return [
			[
				'3x1080p.vp9.mpd',
				100,
				1.0,
				false,
				[
					'init-0.3x1080p.vp9.webm',
					'chunk-0-00001.3x1080p.vp9.webm',
				]
			],
			[
				'3x1080p.vp9.mpd',
				426,
				4.01,
				false,
				[
					'init-0.3x1080p.vp9.webm',
					'chunk-0-00001.3x1080p.vp9.webm',
					'chunk-0-00002.3x1080p.vp9.webm',
				]
			],
			[
				'3x1080p.vp9.mpd',
				854,
				5,
				false,
				[
					'init-0.3x1080p.vp9.webm',
					'chunk-0-00001.3x1080p.vp9.webm',
					'chunk-0-00002.3x1080p.vp9.webm',
					'init-1.3x1080p.vp9.webm',
					'chunk-1-00001.3x1080p.vp9.webm',
					'chunk-1-00002.3x1080p.vp9.webm',
				]
			],
			[
				'3x1080p.vp9.mpd',
				854,
				5,
				true,
				[
					'init-0.3x1080p.vp9.webm',
					'chunk-0-00001.3x1080p.vp9.webm',
					'chunk-0-00002.3x1080p.vp9.webm',
					'init-1.3x1080p.vp9.webm',
					'chunk-1-00001.3x1080p.vp9.webm',
					'chunk-1-00002.3x1080p.vp9.webm',
					'init-2.3x1080p.vp9.webm',
					'chunk-2-00001.3x1080p.vp9.webm',
					'chunk-2-00002.3x1080p.vp9.webm',
				]
			],
		];
	}

	/**
	 * @dataProvider provideGetDashChunkNames
	 */
	public function testGetDashChunkNames(
		string $key,
		int $width,
		float $duration,
		bool $hasAudio,
		array $expected
	) {
		$handler = $this->createMock( WebMHandler::class );
		$handler->method( 'hasAudio' )->willReturn( $hasAudio );
		$handler->method( 'getLength' )->willReturn( $duration );
		$file = $this->createMock( LocalFile::class );
		$file->method( 'getHandler' )->willReturn( $handler );
		$file->method( 'getWidth' )->willReturn( $width );
		$config = new HashConfig( [] );
		$options = ( new TranscodePresets( $config ) )->findByKey( $key );
		$result = iterator_to_array( WebVideoTranscode::getDashChunkNames( $file, $key, $options ) );
		$this->assertSame( $expected, $result );
	}
}
