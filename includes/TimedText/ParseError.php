<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class ParseError {
	/** @var int */
	protected $line;
	/** @var string */
	protected $input;
	/** @var string */
	protected $error;

	/**
	 * @param int $line
	 * @param string $input
	 * @param string $error
	 */
	public function __construct( $line, $input, $error ) {
		$this->line = (int)$line;
		$this->input = (string)$input;
		$this->error = (string)$error;
	}

	/**
	 * @return int
	 */
	public function getLine() {
		return $this->line;
	}

	/**
	 * @return string
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}
}
