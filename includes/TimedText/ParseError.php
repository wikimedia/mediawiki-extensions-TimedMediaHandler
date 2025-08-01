<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class ParseError {
	public function __construct(
		protected readonly int $line,
		protected readonly string $input,
		protected readonly string $error,
	) {
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
