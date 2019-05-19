<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

abstract class Reader {
	/**
	 * @param string $input
	 */
	abstract public function read( $input );

	/**
	 * @return DOM\Cue[]
	 */
	abstract public function getCues();

	/**
	 * @return ParseError[]
	 */
	abstract public function getErrors();
}
