<?php
namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\TimedMediaHandler\HLS\Multivariant;
use MediaWikiMediaTestCase;

/**
 * @covers \MediaWiki\TimedMediaHandler\HLS\Multivariant
 */
class MultivariantTest extends MediaWikiMediaTestCase {

	protected function getFilePath(): string {
		return __DIR__ . '/media';
	}

	protected function filePath( string $filename ): string {
		return $this->getFilePath() . DIRECTORY_SEPARATOR . $filename;
	}

	protected function readFile( string $filename ): string {
		$path = $this->filePath( $filename );
		$data = file_get_contents( $path );
		if ( $data === false ) {
			throw new Exception( "Error reading file $path" );
		}
		return $data;
	}

	/**
	 * @dataProvider providerQuote
	 * @param string $raw input string
	 * @param string $expected quoted output string
	 */
	public function testQuote( string $raw, string $expected ): void {
		$quoted = Multivariant::quote( $raw );
		$this->assertEquals( $expected, $quoted, "Multivarant::quote" );
	}

	public static function providerQuote() {
		return [
			[ "", "\"\"" ],
			[ "abc", "\"abc\"" ],
			[ "foo bar", "\"foo bar\"" ],
			[ "tab\tsize", "\"tab\tsize\"" ],
			[ "&quot;", "\"&quot;\"" ],
			[ "no \"quotes\"?", "\"no 'quotes'?\"" ],
			[ "no\nnewlines", "\"no newlines\"" ],
			[ "no\rcarriage returns", "\"no carriage returns\"" ],
			[ "no\r\ncrlfs", "\"no crlfs\"" ],
		];
	}

	/**
	 * @dataProvider providerTracks
	 * @param string $filename name of media track file
	 * @param array $tracks
	 * @param string $fileNameExpectedData
	 */
	public function testTracks( string $filename, array $tracks, string $fileNameExpectedData ): void {
		$interval = 10;
		$path = $this->filePath( $filename );
		$multivariant = new Multivariant( $filename, $tracks );
		$playlist = $multivariant->playlist();
		$expected = $this->readFile( $fileNameExpectedData );
		$this->assertEquals( $expected, $playlist, ".m3u8 playlist generation from media track" );
	}

	public static function providerTracks() {
		$vp9lo = '240p.video.vp9.mp4';
		$vp9hi = '360p.video.vp9.mp4';
		$mjpeg = '144p.video.mjpeg.mov';
		$mp3   = 'stereo.audio.mp3';
		$opus  = 'stereo.audio.opus.mp4';

		return [
			[
				'stream',
				[ $vp9hi ],
				'variant.vp9hi.m3u8',
			],
			[
				'stream',
				[ $vp9hi, $mjpeg ],
				'variant.vp9hi-mjpeg.m3u8',
			],
			[
				'stream',
				[ $vp9hi, $vp9lo, $mjpeg ],
				'variant.vp9hi-vp9lo-mjpeg.m3u8',
			],
			[
				'stream',
				[ $vp9hi, $opus ],
				'variant.vp9hi-opus.m3u8',
			],
			[
				'stream',
				[ $vp9hi, $opus, $mp3 ],
				'variant.vp9hi-opus-mp3.m3u8',
			],
			[
				'stream',
				[ $vp9hi, $vp9lo, $mjpeg, $opus, $mp3 ],
				'variant.vp9hi-vp9lo-mjpeg-opus-mp3.m3u8',
			],
			// Test special chars in the filename
			// This code trusts that you validated your filenames ahead of time;
			// but it must correctly URL-encode its output links!
			[
				'stream("AT&T_bar_?")',
				[ $vp9hi, $opus ],
				'urlencoding.m3u8',
			],
		];
	}
}
