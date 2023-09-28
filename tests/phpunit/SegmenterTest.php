<?php

use MediaWiki\TimedMediaHandler\HLS\Segmenter;

/**
 * @covers \MediaWiki\TimedMediaHandler\HLS\Segmenter
 * @covers \MediaWiki\TimedMediaHandler\HLS\MP3Segmenter
 * @covers \MediaWiki\TimedMediaHandler\HLS\MP4Segmenter
 */
class SegmenterTest extends MediaWikiMediaTestCase {

	protected function getFilePath() {
		return __DIR__ . '/media';
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	protected function filePath( $filename ) {
		return $this->getFilePath() . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 * @dataProvider providerTracks
	 * @param string $filename name of media track file
	 */
	public function testTracks( $filename ) {
		$interval = 10;
		$path = $this->filePath( $filename );
		$expected = file_get_contents( "$path.m3u8" );
		$segmenter = Segmenter::segment( $path );
		$segmenter->consolidate( $interval );
		// @fixme test this $segmenter->rewrite();
		$playlist = $segmenter->playlist( $interval, $filename );
		$this->assertEquals( $expected, $playlist, ".m3u8 playlist generation from media track" );
	}

	public function providerTracks() {
		return [
			[ 'stream.360p.video.vp9.mp4' ],
			[ 'stream.240p.video.vp9.mp4' ],
			[ 'stream.144p.video.mjpeg.mov' ],
			[ 'stream.stereo.audio.opus.mp4' ],
			[ 'stream.stereo.audio.mp3' ],
		];
	}
}
