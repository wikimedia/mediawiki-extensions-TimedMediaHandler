<?php

namespace MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * WebVTT internal node object parent class.
 * Internal nodes can contain other nodes,
 * and map roughly to HTML elements.
 */
class InternalNode extends Node {
	/** @var string[] list of classes, if any */
	public $classes = [];

	/** @var string list of annotations, if any */
	public $annotation = '';

	/** @var Node[] list of contained nodes */
	public $nodes = [];

	public function appendNode( Node $node ) {
		$this->nodes[] = $node;
	}
}
