<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT Text object, maps roughly to an HTML text node.
 */
class TextNode extends LeafNode {
	/** @var string */
	public $text = '';

	/**
	 * @param string $text
	 */
	public function __construct( $text = '' ) {
		$this->text = strval( $text );
	}

	/**
	 * @param string $str
	 *
	 * @return void
	 */
	public function appendText( $str ) {
		$this->text .= $str;
	}
}
