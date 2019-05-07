<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT Text object, maps roughly to an HTML text node.
 */
class TextNode extends LeafNode {
	/** @var string */
	public $text = '';

	public function __construct( $text = '' ) {
		$this->text = strval( $text );
	}

	public function appendText( $str ) {
		$this->text .= $str;
	}
}
