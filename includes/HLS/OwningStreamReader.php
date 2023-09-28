<?php
/**
 * Base class for streaming media segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

/**
 * Base file class that fcloses on destruct.
 */
class OwningStreamReader extends StreamReader {
	public function __destruct() {
		fclose( $this->file );
	}
}
