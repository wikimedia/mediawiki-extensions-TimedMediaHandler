<?php
/**
 * Exception helper for streaming media segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;

/**
 * Exception thrown when asked to read a certain number of bytes.
 */
class ShortReadException extends Exception {
	/**
	 * The read short bytes, if any. May be empty.
	 */
	public string $bytes;

	/**
	 * @param string $bytes
	 */
	public function __construct( $bytes ) {
		$this->bytes = $bytes;
		parent::__construct( 'Short file read' );
	}
}
