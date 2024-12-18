<?php

use MediaWiki\TimedMediaHandler\HLS\Multivariant;

/**
 * @covers \MediaWiki\TimedMediaHandler\HLS\Multivariant
 */
class MultivariantTest extends MediaWikiMediaTestCase {

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
	 * @param string $filename
	 * @return string
	 */
	protected function readFile( $filename ) {
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
	public function testQuote( $raw, $expected ) {
		$quoted = Multivariant::quote( $raw );
		$this->assertEquals( $expected, $quoted, "Multivarant::quote" );
	}

	public function providerQuote() {
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
	 * @param string $expected
	 */
	public function testTracks( $filename, $tracks, $expected ) {
		$interval = 10;
		$path = $this->filePath( $filename );
		$multivariant = new Multivariant( $filename, $tracks );
		$playlist = $multivariant->playlist();
		$this->assertEquals( $expected, $playlist, ".m3u8 playlist generation from media track" );
	}

	public function providerTracks() {
		$vp9lo = '240p.video.vp9.mp4';
		$vp9hi = '360p.video.vp9.mp4';
		$mjpeg = '144p.video.mjpeg.mov';
		$mp3   = 'stereo.audio.mp3';
		$opus  = 'stereo.audio.opus.mp4';

		return [
			[
				'stream',
				[ $vp9hi ],
				$this->readFile( 'variant.vp9hi.m3u8' ),
			],
			[
				'stream',
				[ $vp9hi, $mjpeg ],
				$this->readFile( 'variant.vp9hi-mjpeg.m3u8' ),
			],
			[
				'stream',
				[ $vp9hi, $vp9lo, $mjpeg ],
				$this->readFile( 'variant.vp9hi-vp9lo-mjpeg.m3u8' ),
			],
			[
				'stream',
				[ $vp9hi, $opus ],
				$this->readFile( 'variant.vp9hi-opus.m3u8' ),
			],
			[
				'stream',
				[ $vp9hi, $opus, $mp3 ],
				$this->readFile( 'variant.vp9hi-opus-mp3.m3u8' ),
			],
			[
				'stream',
				[ $vp9hi, $vp9lo, $mjpeg, $opus, $mp3 ],
				$this->readFile( 'variant.vp9hi-vp9lo-mjpeg-opus-mp3.m3u8' ),
			],
			// Test special chars in the filename
			// This code trusts that you validated your filenames ahead of time;
			// but it must correctly URL-encode its output links!
			[
				'stream("AT&T_bar_?")',
				[ $vp9hi, $opus ],
				$this->readFile( 'urlencoding.m3u8' ),
			],
		];
	}
}
