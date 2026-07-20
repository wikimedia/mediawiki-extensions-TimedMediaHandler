<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT internal node object parent class.
 * Internal nodes can contain other nodes,
 * and map roughly to HTML elements.
 */
class InternalNode extends Node {
	/** @var Node[] list of contained nodes */
	public $nodes = [];

	public function appendNode( Node $node ) {
		$this->nodes[] = $node;
	}
}
