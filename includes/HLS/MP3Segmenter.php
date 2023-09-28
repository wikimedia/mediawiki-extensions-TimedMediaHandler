<?php
/**
 * .m3u8 playlist generation for HLS (HTTP Live Streaming)
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;

/**
 * Reads an MP3 file calculating the byte and time boundaries
 * of the separate addressable frames, allowing creation of a
 * streaming playlist.
 *
 * Optionally can rewrite the file to insert timestamp ID3 tags
 * which Apple's HLS says it wants in raw packet streams. Note
 * that initially the parse() result will make every addressible
 * frame (and any source ID3 tags ahead of it) an individual segment.
 * Before inserting timestamp tags with rewrite(), make a call to
 * consolidate($segmentLength).
 *
 * Because the relevant ISO standards are not redistributable,
 * artisanal 3rd-party documentation was sourced via web searches
 * such as:
 * - https://en.wikipedia.org/wiki/MP3
 * - http://www.mp3-tech.org/programmer/frame_header.html
 * - https://datatracker.ietf.org/doc/html/rfc8216
 * - https://id3.org/id3v2.4.0-frames
 * - https://id3.org/id3v2.4.0-structure
 * - https://web.archive.org/web/20081008034714/http://www.id3.org/id3v2.3.0
 */
class MP3Segmenter extends Segmenter {

	/**
	 * Internal layout of MP3 frame header bitfield
	 * @var array
	 */
	private static $bits = [
		'sync'        => [ 21, 11 ],
		'mpeg'        => [ 19, 2 ],
		'layer'       => [ 17, 2 ],
		'protection'  => [ 16, 1 ],
		'bitrate'     => [ 12, 4 ],
		'sampleRate'  => [ 10, 2 ],
		'padding'     => [ 9, 1 ],
		// below this not needed at present
		'private'     => [ 8, 1 ],
		'channelMode' => [ 6, 2 ],
		'modeExt'     => [ 4, 2 ],
		'copyright'   => [ 3, 1 ],
		'original'    => [ 2, 1 ],
		'emphasis'    => [ 0, 2 ],
	];

	/**
	 * 11-bit sync mask for MP3 frame header
	 * @var int
	 */
	private const SYNC_MASK = 0x7ff;

	/**
	 * Map of sample count per frame based on version/mode
	 * This is just in case we need to measure non-default sample rates!
	 * @var array
	 */
	private static $samplesPerFrame = [
		// invalid / layer 3 / 2 / 1

		// MPEG-2.5
		[ 0, 576, 1152, 384 ],
		// Reserved
		[ 0, 0, 0, 0 ],
		// MPEG-2
		[ 0, 576, 1152, 384 ],
		// MPEG-1
		[ 0, 1152, 384, 384 ],
	];

	/**
	 * Map of sample rates based on version/mode
	 * @var array
	 */
	private static $sampleRates = [
		// MPEG-2.5
		[ 11025, 12000, 8000, 1 ],
		// Reserved
		[ 1, 1, 1, 1 ],
		// MPEG-2
		[ 22050, 24000, 16000, 1 ],
		// MPEG-1
		[ 44100, 48000, 32000, 1 ],
	];

	/**
	 * Map of bit rates based on version/mode/code
	 * @var array
	 */
	private static $bitrates = [
		// MPEG-2
		[
			// invalid layer
			[ 0, 0, 0, 0,  0, 0, 0, 0,  0, 0, 0, 0,  0, 0, 0, 0, ],
			// layer 3
			[ 0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0 ],
			// layer 2
			[ 0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 0 ],
			// layer 1
			[ 0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0 ],
		],
		// MPEG-1
		[
			// invalid layer
			[ 0, 0, 0, 0,  0, 0, 0, 0,  0, 0, 0, 0,  0, 0, 0, 0, ],
			// layer 3
			[ 0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160, 192, 224, 256, 320, 0 ],
			// layer 2
			[ 0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384, 0 ],
			// layer 1
			[ 0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 316, 448, 0 ],
		]
	];

	/**
	 * Timestamp resolution for HLS ID3 timestamp tags
	 */
	private const KHZ_90 = 90000;

	/**
	 * Decode a binary field from the MP3 frame header
	 */
	private static function field( string $name, int $header ): int {
		[ $shift, $bits ] = self::$bits[$name];
		$mask = ( 1 << $bits ) - 1;
		return ( $header >> $shift ) & $mask;
	}

	/**
	 * Decode an MP3 header bitfield
	 */
	private static function frameHeader( string $bytes ): ?array {
		if ( strlen( $bytes ) < 4 ) {
			return null;
		}
		$data = unpack( "Nval", $bytes );
		$header = $data['val'];

		// This includes "MPEG 2.5" support, so checks for 11 set bits
		// not 12 set bits as per original MPEG 1/2
		$sync = self::field( 'sync', $header );
		if ( $sync !== self::SYNC_MASK ) {
			return null;
		}

		$mpeg = self::field( 'mpeg', $header );
		$layer = self::field( 'layer', $header );
		$protection = self::field( 'protection', $header );

		$br = self::field( 'bitrate', $header );
		$bitrate = 1000 * self::$bitrates[$mpeg & 1][$layer][$br];
		if ( $bitrate === 0 ) {
			return null;
		}

		$sr = self::field( 'sampleRate', $header );
		$sampleRate = self::$sampleRates[$mpeg][$sr];
		if ( $sampleRate == 1 ) {
			return null;
		}

		$padding = self::field( 'padding', $header );

		$samples = self::$samplesPerFrame[$mpeg][$layer];
		$duration = $samples / $sampleRate;
		$nbits = $duration * $bitrate;
		$nbytes = $nbits / 8;
		$size = intval( $nbytes );

		if ( $protection == 0 ) {
			$size += 2;
		}
		if ( $padding == 1 ) {
			$size++;
		}

		return [
			'samples' => $samples,
			'sampleRate' => $sampleRate,
			'size' => $size,
			'duration' => $duration,
		];
	}

	private static function id3Header( string $bytes ): ?array {
		// ID3v2.3
		// https://web.archive.org/web/20081008034714/http://www.id3.org/id3v2.3.0
		// ID3v2/file identifier   "ID3"
		// ID3v2 version           $03 00
		// ID3v2 flags             %abc00000
		// ID3v2 size              4 * %0xxxxxxx
		$headerLen = 10;
		if ( strlen( $bytes ) < $headerLen ) {
			return null;
		}

		$data = unpack( "a3tag/nversion/Cflags/C4size", $bytes );
		if ( $data['tag'] !== 'ID3' ) {
			return null;
		}

		$size = $headerLen +
			( $data['size4'] |
				( $data['size3'] << 7 ) |
				( $data['size2'] << 14 ) |
				( $data['size1'] << 21 ) );
		return [
			'size' => $size,
		];
	}

	protected function parse(): void {
		$file = fopen( $this->filename, 'rb' );
		$stream = new OwningStreamReader( $file );

		$timestamp = 0.0;
		while ( true ) {
			$start = $stream->pos();
			$lookahead = 10;
			try {
				$bytes = $stream->read( $lookahead );
			} catch ( ShortReadException $e ) {
				// end of file
				break;
			}

			// Check for MP3 frame header sync pattern
			$header = self::frameHeader( $bytes );
			if ( $header ) {
				// Note we don't need the data at this time.
				$stream->seek( $start + $header['size'] );
				$timestamp += $header['duration'];
				$this->segments[] = [
					'start' => $start,
					'size' => $header['size'],
					'timestamp' => $timestamp,
					'duration' => $header['duration'],
				];
				continue;
			}

			// Check for ID3v2 tag
			$id3 = self::id3Header( $bytes );
			if ( $id3 ) {
				// For byte range purposes; count as zero duration
				$stream->seek( $start + $id3['size'] );
				$this->segments[] = [
					'start' => $start,
					'size' => $id3['size'],
					'timestamp' => $timestamp,
					'duration' => 0.0,
				];
				continue;
			}

			throw new Exception( "Not a valid MP3 or ID3 frame at $start" );
		}
	}

	/**
	 * Rewrite the file to include ID3 private tags with timestamp
	 * data for HLS at segment boundaries. This will modify the file
	 * in-place and change the segment offsets and sizes in the object.
	 *
	 * Beware that an i/o error during modification of the file could
	 * leave the file in an inconsistent state. Short read exceptions
	 * should be impossible unless the file is being modified from under
	 * us.
	 */
	public function rewrite(): void {
		$offset = 0;
		$id3s = [];
		$segments = [];
		foreach ( $this->segments as $i => $orig ) {
			$id3 = self::timestampTag( $orig['timestamp'] );
			$delta = strlen( $id3 );
			$id3s[$i] = $id3;
			$segments[$i] = [
				'start' => $orig['start'] + $offset,
				'size' => $orig['size'] + $delta,
				'timestamp' => $orig['timestamp'],
				'duration' => $orig['duration'],
			];
			$offset += $delta;
		}

		$file = fopen( $this->filename, 'rw+b' );
		$stream = new OwningStreamReader( $file );

		// Move each segment forward, starting at the lastmost to work in-place.
		$preserveKeys = true;
		foreach ( array_reverse( $this->segments, $preserveKeys ) as $i => $orig ) {
			$stream->seek( $orig['start'] );
			$bytes = $stream->read( $orig['size'] );

			$stream->seek( $segments[$i]['start'] );
			$stream->write( $id3s[$i] );
			$stream->write( $bytes );
		}

		$this->segments = $segments;
	}

	/**
	 * Generate an ID3 private tag with a timestamp for use in HLS
	 * streams of raw media data such as MP3 or AAC.
	 */
	protected static function timestampTag( float $timestamp ): string {
		/*
		https://datatracker.ietf.org/doc/html/rfc8216
		PRIV frame type

		should contain:

		The ID3 PRIV owner identifier MUST be
		"com.apple.streaming.transportStreamTimestamp".  The ID3 payload MUST
		be a 33-bit MPEG-2 Program Elementary Stream timestamp expressed as a
		big-endian eight-octet number, with the upper 31 bits set to zero.
		Clients SHOULD NOT play Packed Audio Segments without this ID3 tag.

		https://id3.org/id3v2.4.0-frames
		https://id3.org/id3v2.4.0-structure

		bit order is MSB first, big-endian

		header 10 bytes
		extended header (var, optional)
		frames (variable)
		pading (variable, optional)
		footer (10 bytes, optional)


		header:
			"ID3"
			version: 16 bits $04 00
			flags: 32 bits
			idv2 size: 32 bits (in chunks of 4 bytes, not counting header or footer)

		flags:
			bit 7 - unsyncrhonization (??)
			bit 6 - extended header
			bit 5 - experimental indicator
			bit 4 - footer present

		frame:
			id - 32 bits (four chars)
			size - 32 bits (in chunks of 4 bytes, excluding frame header)
			flags - 16 bits
			(frame data)

		priv payload:
			owner text string followed by \x00
			(binary data)

		The timestamps... I think... have 90 kHz integer resolution
		so convert from the decimal seconds in the HLS

		*/

		$owner = "com.apple.streaming.transportStreamTimestamp\x00";
		$pts = round( $timestamp * self::KHZ_90 );
		$thirtyThreeBits = pow( 2, 33 );
		$thirtyOneBits = pow( 2, 31 );
		if ( $pts >= $thirtyThreeBits ) {
			// make sure they won't get too big for 33 bits
			// this allows about a 24 hour media length
			throw new Exception( "Timestamp overflow in MP3 output stream: $pts >= $thirtyThreeBits" );
		}
		$pts_high = intval( floor( $pts / $thirtyOneBits ) );
		$pts_low = intval( $pts - ( $pts_high * $thirtyOneBits ) );

		// Private frame payload
		$frame_data = pack(
			'a*NN',
			$owner,
			$pts_high,
			$pts_low,
		);

		// Private frame header
		$frame_type = 'PRIV';
		$frame_flags = 0;
		$frame_length = strlen( $frame_data );
		if ( $frame_length > 127 ) {
			throw new Exception( "Should never happen: too large ID3 frame data" );
		}
		$frame = pack(
			'a4Nna*',
			$frame_type,
			$frame_length,
			$frame_flags,
			$frame_data
		);

		// ID3 tag
		$tag_type = 'ID3';
		$tag_version = 0x0400;
		$tag_flags = 0;
		// if >127 bytes may need to adjust
		$tag_length = strlen( $frame );
		if ( $tag_length > 127 ) {
			throw new Exception( "Should never happen: too large ID3 tag" );
		}
		$tag = pack(
			'a3nCNa*',
			$tag_type,
			$tag_version,
			$tag_flags,
			$tag_length,
			$frame
		);

		return $tag;
	}
}
