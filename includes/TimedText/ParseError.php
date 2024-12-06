<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class ParseError {
	protected int $line;
	protected string $input;
	protected string $error;

	public function __construct( int $line, string $input, string $error ) {
		$this->line = $line;
		$this->input = $input;
		$this->error = $error;
	}

	public function getLine(): int {
		return $this->line;
	}

	public function getInput(): string {
		return $this->input;
	}

	public function getError(): string {
		return $this->error;
	}
}
