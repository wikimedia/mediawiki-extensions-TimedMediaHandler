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
 * Adds MP4/ISO BMFF/QuickTime box decoding to the stream reader,
 * making it easy to read media files with mixes of hierarchical
 * boxes-within-boxes and straight data (usually all big-endian).
 *
 * Because the relevant ISO standards are not redistributable,
 * artisinal 3rd-party documentation was sourced via web searches.
 */
class MP4Reader extends StreamReader {

	/**
	 * Read big-endian int
	 */
	protected function readInt( int $bytes, int $padto, string $code ): int {
		$bytes = $this->read( $bytes );
		$padded = str_pad( $bytes, $padto, "\x00", STR_PAD_LEFT );
		$data = unpack( "{$code}val", $padded );
		return $data['val'];
	}

	/**
	 * Read a 64-bit unsigned big-endian integer.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function read64(): int {
		return $this->readInt( 8, 8, 'J' );
	}

	/**
	 * Read a 32-bit unsigned big-endian integer.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function read32(): int {
		return $this->readInt( 4, 4, 'N' );
	}

	/**
	 * Read a 24-bit unsigned big-endian integer.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function read24(): int {
		return $this->readInt( 3, 4, 'N' );
	}

	/**
	 * Read a 16-bit unsigned big-endian integer.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function read16(): int {
		return $this->readInt( 2, 2, 'n' );
	}

	/**
	 * Read an 8-bit unsigned integer.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function read8(): int {
		$byte = $this->read( 1 );
		return ord( $byte );
	}

	/**
	 * Read a 4-byte box type code.
	 * @throws Exception on i/o error
	 * @throws ShortReadException on end of file
	 */
	public function readType(): string {
		return $this->read( 4 );
	}

	/**
	 * Read box metadata and return it as a sub-stream object.
	 * @throws Exception on i/o error
	 * @throws ShortReadException if no matching box found
	 */
	public function readBox(): MP4Box {
		$start = $this->pos();
		$size = $this->read32();
		$type = $this->readType();
		$this->pos = $start + $size;
		return new MP4Box( $this->file, $start, $size, $type );
	}

	/**
	 * Search through a series of boxes, discarding sibling boxes
	 * until the requested type is found.
	 *
	 * Returns null on end of input.
	 *
	 * @throws Exception on i/o error
	 */
	public function findBox( string $type ): ?MP4Box {
		try {
			return $this->expectBox( $type );
		} catch ( ShortReadException $e ) {
			return null;
		}
	}

	/**
	 * Search through a series of boxes, discarding sibling boxes
	 * until the requested type is found.
	 *
	 * Same as findBox but throws if no match is found.
	 *
	 * @throws Exception on i/o error
	 * @throws ShortReadException if no matching box found
	 */
	public function expectBox( string $type ): MP4Box {
		while ( true ) {
			// will throw eventually
			$box = $this->readBox();
			if ( $box->type === $type ) {
				return $box;
			}
		}
	}
}
