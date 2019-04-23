<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT Voice object, maps roughly to an HTML span with speaker info.
 */
class VoiceNode extends InternalNode {
	public function __construct( $voice = '' ) {
		$this->annotation = strval( $voice );
	}

	public function getVoice() {
		return $this->annotation;
	}
}
