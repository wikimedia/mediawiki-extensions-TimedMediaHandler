<?php

namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode
 */
class TranscodePriorityTest extends MediaWikiIntegrationTestCase {

	public static function provideIsTranscodePrioritized() {
		return [
			'short video' => [ '1080p.vp9.webm', 60, true ],
			'video at threshold' => [ '240p.vp9.webm', 900, true ],
			'long video' => [ '240p.vp9.webm', 901, false ],
			'long DASH' => [ '3x1080p.vp9.mpd', 3600, false ],
			'long audio' => [ 'mp3', 3600, true ],
			'long audio track of a video' => [ 'stereo.audio.mp3', 3600, true ],
		];
	}

	/**
	 * @dataProvider provideIsTranscodePrioritized
	 */
	public function testIsTranscodePrioritized( string $key, float $length, bool $expected ) {
		$this->overrideConfigValue( 'TmhPriorityLengthThreshold', 900 );
		$file = $this->createMock( File::class );
		$file->method( 'getLength' )->willReturn( $length );

		$this->assertSame( $expected, WebVideoTranscode::isTranscodePrioritized( $file, $key ) );
	}
}
