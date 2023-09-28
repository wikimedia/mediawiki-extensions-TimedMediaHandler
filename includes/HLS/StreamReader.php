<?php
/**
 * Base class for streaming media segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;

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
			throw new Exception( 'Invalid file stream' );
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
	 * @throws Exception on error
	 */
	public function seek( int $pos ): void {
		$this->pos = $pos;

		if ( $this->pos === $this->tell() ) {
			return;
		}
		$retval = fseek( $this->file, $this->pos, SEEK_SET );

		if ( $retval < 0 ) {
			throw new Exception( "Failed to seek to $this->pos bytes" );
		}
	}

	/**
	 * Read $len bytes or throw on EOF/short read.
	 * @throws ShortReadException on end of file
	 * @throws Exception on error
	 */
	public function read( int $len ): string {
		$this->seek( $this->pos );
		$bytes = fread( $this->file, $len );
		if ( $bytes === false ) {
			throw new Exception( "Read error for $len bytes at $this->pos" );
		}
		if ( strlen( $bytes ) < $len ) {
			throw new ShortReadException( $bytes );
		}
		$this->pos += strlen( $bytes );
		return $bytes;
	}

	/**
	 * Write the given data to the stream.
	 * @throws Exception on error
	 */
	public function write( string $bytes ) {
		$this->seek( $this->pos );
		$len = strlen( $bytes );
		$nbytes = fwrite( $this->file, $bytes );
		if ( $nbytes === false ) {
			throw new Exception( "Write error for $len bytes at $this->pos" );
		}
		if ( $nbytes < $len ) {
			throw new Exception( "Short write; unexpected filesystem error" );
		}
		$this->pos += $nbytes;
	}
}
