<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

abstract class Writer {
	/**
	 * @param DOM\Cue[] $cues
	 * @return string
	 */
	abstract public function write( $cues );
}
