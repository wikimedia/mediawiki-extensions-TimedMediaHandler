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
	 * @param string $bytes The read short bytes, if any. May be empty
	 */
	public function __construct( public readonly string $bytes ) {
		parent::__construct( 'Short file read' );
	}
}
