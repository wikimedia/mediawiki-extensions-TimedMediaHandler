<?php
/**
 * Base class for streaming media segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use InvalidArgumentException;
use RuntimeException;

/**
 * Base class for reading/writing a media file with wrappers
 * for exception handling and possible multi usage.
 */
class StreamReader {
	/**
	 * @var resource
	 */
	protected $file;

	protected int $pos;

	/**
	 * @param resource $file
	 */
	public function __construct( $file ) {
		if ( get_resource_type( $file ) !== 'stream' ) {
			throw new InvalidArgumentException( 'Invalid file stream' );
		}
		$this->file = $file;
		$this->pos = $this->tell();
	}

	private function tell(): int {
		return ftell( $this->file );
	}

	public function pos(): int {
		return $this->pos;
	}

	/**
	 * Seek to given absolute file position.
	 */
	public function seek( int $pos ): void {
		$this->pos = $pos;

		if ( $this->pos === $this->tell() ) {
			return;
		}
		$retval = fseek( $this->file, $this->pos, SEEK_SET );

		if ( $retval < 0 ) {
			throw new RuntimeException( "Failed to seek to $this->pos bytes" );
		}
	}

	/**
	 * Read $len bytes or throw on EOF/short read.
	 * @throws ShortReadException on end of file
	 */
	public function read( int $len ): string {
		$this->seek( $this->pos );
		$bytes = fread( $this->file, $len );
		if ( $bytes === false ) {
			throw new RuntimeException( "Read error for $len bytes at $this->pos" );
		}
		if ( strlen( $bytes ) < $len ) {
			throw new ShortReadException( $bytes );
		}
		$this->pos += strlen( $bytes );
		return $bytes;
	}

	/**
	 * Write the given data to the stream.
	 */
	public function write( string $bytes ) {
		$this->seek( $this->pos );
		$len = strlen( $bytes );
		$nbytes = fwrite( $this->file, $bytes );
		if ( $nbytes === false ) {
			throw new RuntimeException( "Write error for $len bytes at $this->pos" );
		}
		if ( $nbytes < $len ) {
			throw new RuntimeException( "Short write; unexpected filesystem error" );
		}
		$this->pos += $nbytes;
	}
}
