<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class ParseError {
	protected $line;
	protected $input;
	protected $error;

	public function __construct( $line, $input, $error ) {
		$this->line = intval( $line );
		$this->input = strval( $input );
		$this->error = strval( $error );
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
