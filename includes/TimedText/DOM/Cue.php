<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

class Cue {
	/** @var string */
	public $id = '';

	/** @var float */
	public $start = 0.0;

	/** @var float */
	public $end = 0.0;

	/** @var Node[] */
	public $nodes = [];

	public function appendNode( Node $node ) {
		$this->nodes[] = $node;
	}

	public function appendText( $str ) {
		$this->appendNode( new TextNode( strval( $str ) ) );
	}
}
