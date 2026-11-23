<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\HLS;

use MediaWiki\TimedMediaHandler\HLS\DashManifest;
use MediaWiki\TimedMediaHandler\HLS\DashRepresentation;
use MediaWiki\TimedMediaHandler\HLS\DashToHls;
use MediaWikiUnitTestCase;
use RuntimeException;

/**
 * @covers \MediaWiki\TimedMediaHandler\HLS\DashToHls
 */
class DashToHlsTest extends MediaWikiUnitTestCase {

	private static function playlistName( DashRepresentation $rep ): string {
		return "media-{$rep->id}.3x1080p.vp9.fmp4.m3u8";
	}

	/** @return DashRepresentation[] */
	private static function representations(): array {
		return DashManifest::parse( file_get_contents( __DIR__ . '/fixtures/3x1080p.vp9.fmp4.mpd' ) );
	}

	private const SEGMENT_SIZES = [
		'chunk-0-00001.3x1080p.vp9.fmp4.mp4' => 50000,
		'chunk-0-00002.3x1080p.vp9.fmp4.mp4' => 50000,
		'chunk-0-00003.3x1080p.vp9.fmp4.mp4' => 25000,
		'chunk-1-00001.3x1080p.vp9.fmp4.mp4' => 150000,
		'chunk-1-00002.3x1080p.vp9.fmp4.mp4' => 160000,
		// Not counted in the peak: the last segment
		'chunk-1-00003.3x1080p.vp9.fmp4.mp4' => 1000,
		'chunk-2-00001.3x1080p.vp9.fmp4.mp4' => 450000,
		'chunk-2-00002.3x1080p.vp9.fmp4.mp4' => 500000,
		'chunk-2-00003.3x1080p.vp9.fmp4.mp4' => 2000,
	];

	public function testMultivariantPlaylist() {
		$hls = new DashToHls( self::representations(), self::playlistName( ... ), self::SEGMENT_SIZES );
		$this->assertSame(
			"#EXTM3U\n" .
			"#EXT-X-VERSION:6\n" .
			"#EXT-X-INDEPENDENT-SEGMENTS\n" .
			'#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID="audio",NAME="audio-0",DEFAULT=YES,AUTOSELECT=YES,' .
				'CHANNELS="1",URI="media-0.3x1080p.vp9.fmp4.m3u8"' . "\n" .
			// Peak 1280000 bits / 4 s + 400000 bits / 4 s,
			// average 2488000 bits / 10.007 s + 1000000 bits / 10.001 s
			'#EXT-X-STREAM-INF:BANDWIDTH=420000,AVERAGE-BANDWIDTH=348617,CODECS="vp09.00.20.08,Opus",' .
				'RESOLUTION=426x240,FRAME-RATE=30.000,AUDIO="audio"' . "\n" .
			"media-1.3x1080p.vp9.fmp4.m3u8\n" .
			'#EXT-X-STREAM-INF:BANDWIDTH=1100000,AVERAGE-BANDWIDTH=861059,CODECS="vp09.00.30.08,Opus",' .
				'RESOLUTION=854x480,FRAME-RATE=30.000,AUDIO="audio"' . "\n" .
			"media-2.3x1080p.vp9.fmp4.m3u8\n",
			$hls->multivariantPlaylist()
		);
	}

	public function testMediaPlaylists() {
		$hls = new DashToHls( self::representations(), self::playlistName( ... ), self::SEGMENT_SIZES );
		$playlists = $hls->mediaPlaylists();
		$this->assertSame( [
			'media-0.3x1080p.vp9.fmp4.m3u8',
			'media-1.3x1080p.vp9.fmp4.m3u8',
			'media-2.3x1080p.vp9.fmp4.m3u8',
		], array_keys( $playlists ) );
		$this->assertSame(
			"#EXTM3U\n" .
			"#EXT-X-VERSION:6\n" .
			"#EXT-X-TARGETDURATION:4\n" .
			"#EXT-X-PLAYLIST-TYPE:VOD\n" .
			"#EXT-X-INDEPENDENT-SEGMENTS\n" .
			"#EXT-X-MAP:URI=\"init-1.3x1080p.vp9.fmp4.mp4\"\n" .
			"#EXTINF:4.007,\n" .
			"chunk-1-00001.3x1080p.vp9.fmp4.mp4\n" .
			"#EXTINF:4.000,\n" .
			"chunk-1-00002.3x1080p.vp9.fmp4.mp4\n" .
			"#EXTINF:2.000,\n" .
			"chunk-1-00003.3x1080p.vp9.fmp4.mp4\n" .
			"#EXT-X-ENDLIST\n",
			$playlists['media-1.3x1080p.vp9.fmp4.m3u8']
		);
	}

	public function testVideoOnly() {
		$video = array_values( array_filter( self::representations(), static fn ( $rep ) => $rep->isVideo() ) );
		$hls = new DashToHls( $video, self::playlistName( ... ), self::SEGMENT_SIZES );
		$this->assertSame(
			"#EXTM3U\n" .
			"#EXT-X-VERSION:6\n" .
			"#EXT-X-INDEPENDENT-SEGMENTS\n" .
			'#EXT-X-STREAM-INF:BANDWIDTH=320000,AVERAGE-BANDWIDTH=248626,CODECS="vp09.00.20.08",' .
				"RESOLUTION=426x240,FRAME-RATE=30.000\nmedia-1.3x1080p.vp9.fmp4.m3u8\n" .
			'#EXT-X-STREAM-INF:BANDWIDTH=1000000,AVERAGE-BANDWIDTH=761068,CODECS="vp09.00.30.08",' .
				"RESOLUTION=854x480,FRAME-RATE=30.000\nmedia-2.3x1080p.vp9.fmp4.m3u8\n",
			$hls->multivariantPlaylist()
		);
	}

	public function testUnknownSegmentSize() {
		$hls = new DashToHls( self::representations(), self::playlistName( ... ), [] );
		$this->expectException( RuntimeException::class );
		$hls->multivariantPlaylist();
	}
}
