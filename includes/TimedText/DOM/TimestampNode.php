<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT Timestamp object, maps to a processor directive
 */
class TimestampNode extends LeafNode {
	/** @var float timestamp in seconds */
	public $timestamp = 0.0;

	public function __construct( $timestamp = 0.0 ) {
		$this->timestamp = floatval( $timestamp );
	}
}
