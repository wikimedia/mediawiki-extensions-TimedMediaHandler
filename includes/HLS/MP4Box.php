<?php
/**
 * .m3u8 playlist generation for HLS (HTTP Live Streaming)
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

/**
 * Wrapper for reading from an MP4 box that contains a series of additional boxes.
 * Forces end of stream at the end of the input.
 * Warning: could get confused if you close the underlying reader.
 */
class MP4Box extends MP4Reader {
	/**
	 * @param resource $file
	 * @param int $start
	 * @param int $size
	 * @param string $type
	 */
	public function __construct(
		$file,
		public readonly int $start,
		public readonly int $size,
		public readonly string $type,
	) {
		parent::__construct( $file );
	}

	public function end(): int {
		return $this->start + $this->size;
	}

	public function remaining(): int {
		return $this->end() - $this->pos();
	}

	/**
	 * Won't read beyond the end of this box.
	 */
	public function read( int $length ): string {
		if ( $length > $this->remaining() ) {
			throw new ShortReadException( parent::read( $this->remaining() ) );
		}
		return parent::read( $length );
	}
}
