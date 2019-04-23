<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

abstract class Writer {
	/**
	 * @param Cue[] $cues
	 * @return string
	 */
	abstract public function write( $cues );
}
