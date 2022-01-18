<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class ParseError {
	/** @var int */
	protected $line;
	/** @var string */
	protected $input;
	/** @var string */
	protected $error;

	public function __construct( $line, $input, $error ) {
		$this->line = (int)$line;
		$this->input = (string)$input;
		$this->error = (string)$error;
	}

	public function getLine() {
		return $this->line;
	}

	public function getInput() {
		return $this->input;
	}

	public function getError() {
		return $this->error;
	}
}
