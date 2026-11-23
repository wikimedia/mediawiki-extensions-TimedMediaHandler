<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\HLS;

use MediaWiki\TimedMediaHandler\HLS\DashManifest;
use MediaWikiUnitTestCase;
use RuntimeException;

/**
 * @covers \MediaWiki\TimedMediaHandler\HLS\DashManifest
 * @covers \MediaWiki\TimedMediaHandler\HLS\DashRepresentation
 */
class DashManifestTest extends MediaWikiUnitTestCase {

	public function testParseFfmpegManifest() {
		// Written by ffmpeg 7.1 for 3x1080p.vp9.fmp4.mpd
		$reps = DashManifest::parse( file_get_contents( __DIR__ . '/fixtures/3x1080p.vp9.fmp4.mpd' ) );
		$this->assertCount( 3, $reps );

		[ $audio, $low, $high ] = $reps;
		$this->assertTrue( $audio->isAudio() );
		$this->assertSame( '0', $audio->id );
		$this->assertSame( 'opus', $audio->codecs );
		$this->assertSame( 1, $audio->channels );
		$this->assertNull( $audio->width );
		$this->assertSame( 'init-0.3x1080p.vp9.fmp4.mp4', $audio->initialization );
		$this->assertSame( [
			[ 'chunk-0-00001.3x1080p.vp9.fmp4.mp4', 4.001 ],
			[ 'chunk-0-00002.3x1080p.vp9.fmp4.mp4', 4.0 ],
			[ 'chunk-0-00003.3x1080p.vp9.fmp4.mp4', 2.0 ],
		], $audio->segments );

		$this->assertTrue( $low->isVideo() );
		$this->assertSame( 'vp09.00.20.08', $low->codecs );
		$this->assertSame( 426, $low->width );
		$this->assertSame( 240, $low->height );
		// Inherited from the AdaptationSet
		$this->assertSame( '30/1', $low->frameRate );
		$this->assertSame( [ 'chunk-1-00001.3x1080p.vp9.fmp4.mp4', 4.007 ], $low->segments[0] );

		$this->assertSame( 854, $high->width );
		$this->assertSame( [
			'init-2.3x1080p.vp9.fmp4.mp4',
			'chunk-2-00001.3x1080p.vp9.fmp4.mp4',
			'chunk-2-00002.3x1080p.vp9.fmp4.mp4',
			'chunk-2-00003.3x1080p.vp9.fmp4.mp4',
		], $high->getFileNames() );
	}

	private static function manifest( string $template, string $timeline ): string {
		return <<<XML
<?xml version="1.0" encoding="utf-8"?>
<MPD xmlns="urn:mpeg:dash:schema:mpd:2011" type="static">
	<Period id="0">
		<AdaptationSet id="0" contentType="video" mimeType="video/mp4" codecs="vp09.00.20.08">
			<SegmentTemplate timescale="10" $template>
				<SegmentTimeline>$timeline</SegmentTimeline>
			</SegmentTemplate>
			<Representation id="7" bandwidth="1000" width="426" height="240" />
		</AdaptationSet>
	</Period>
</MPD>
XML;
	}

	public function testRepeatAndTemplate() {
		$reps = DashManifest::parse( self::manifest(
			'initialization="i-$RepresentationID$.mp4" startNumber="5" ' .
				'media="c-$RepresentationID$-$Number%03d$.mp4"',
			'<S t="20" d="40" r="2" /><S d="15" />'
		) );
		$this->assertCount( 1, $reps );
		$this->assertSame( 'i-7.mp4', $reps[0]->initialization );
		$this->assertSame( [
			[ 'c-7-005.mp4', 4.0 ],
			[ 'c-7-006.mp4', 4.0 ],
			[ 'c-7-007.mp4', 4.0 ],
			[ 'c-7-008.mp4', 1.5 ],
		], $reps[0]->segments );
	}

	public static function provideInvalid() {
		return [
			'not XML' => [ 'not xml' ],
			'unsupported identifier' => [ self::manifest(
				'initialization="i.mp4" media="c-$Time$.mp4"', '<S d="10" />' ) ],
			'path in segment name' => [ self::manifest(
				'initialization="../init.mp4" media="c-$Number$.mp4"', '<S d="10" />' ) ],
			'open-ended repeat' => [ self::manifest(
				'initialization="i.mp4" media="c-$Number$.mp4"', '<S d="10" r="-1" />' ) ],
			'no timeline' => [ self::manifest(
				'initialization="i.mp4" media="c-$Number$.mp4" duration="40"', '' ) ],
		];
	}

	/**
	 * @dataProvider provideInvalid
	 */
	public function testInvalid( string $xml ) {
		$this->expectException( RuntimeException::class );
		DashManifest::parse( $xml );
	}
}
