<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

/**
 * A fairly lax SubRip (.srt) subtitle file reader.
 * Should accept a lot of sloppy syntax we have in our files.
 */
class SrtReader extends Reader {
	protected $cues = [];
	protected $errors = [];

	protected $input = '';
	protected $len = 0;

	protected $states = [];
	protected $pos = 0;
	protected $line = 0;
	protected $lineStart = 0;

	protected $cue = null;
	protected $tag = '';
	protected $tagSource = '';
	protected $text = '';
	protected $stack = [];
	protected $current = null;

	/**
	 * @inheritDoc
	 */
	public function read( $input ) {
		// Trim BOM if present.
		$bom = "\xEF\xBB\xBF";
		if ( substr( $input, 0, 3 ) === $bom ) {
			$input = substr( $input, 3 );
		}
		$this->parse( $input );
	}

	public function getCues() {
		return $this->cues;
	}

	public function getErrors() {
		return $this->errors;
	}

	protected function saveState() {
		$this->states[] = [
			'pos' => $this->pos,
			'line' => $this->line,
			'lineStart' => $this->lineStart
		];
	}

	protected function restoreState() {
		if ( !count( $this->states ) ) {
			throw new \Exception( 'No saved state to discard' );
		}
		$state = array_pop( $this->states );
		$this->pos = $state['pos'];
		$this->line = $state['line'];
		$this->lineStart = $state['lineStart'];
	}

	protected function discardState() {
		if ( !count( $this->states ) ) {
			throw new \Exception( 'No saved state to discard' );
		}
		array_pop( $this->states );
	}

	protected function eof() {
		return ( $this->pos >= $this->len );
	}

	protected function peek() {
		if ( $this->pos < $this->len ) {
			return $this->input{$this->pos};
		} else {
			return '';
		}
	}

	protected function consume() {
		if ( $this->pos < $this->len ) {
			$c = $this->input{$this->pos++};
			if ( $c === "\n" ) {
				$this->line++;
				$this->lineStart = $this->pos;
			}
			return $c;
		} else {
			return '';
		}
	}

	protected function consumeWhile( $callback ) {
		$str = '';
		while ( $this->pos < $this->len ) {
			$c = $this->input{$this->pos};
			if ( !$callback( $c ) ) {
				break;
			}
			$this->pos++;
			if ( $c === "\n" ) {
				$this->line++;
				$this->lineStart = $this->pos;
			}
			$str .= $c;
		}
		return $str;
	}

	protected function consumeLine() {
		return $this->consumeWhile( function ( $c ) {
			return $c !== "\n" && $c !== '';
		} );
	}

	protected function consumeAlphanum() {
		return $this->consumeWhile( 'ctype_alnum' );
	}

	protected function consumeHexDigits() {
		return $this->consumeWhile( 'ctype_xdigit' );
	}

	protected function consumeDigits() {
		return $this->consumeWhile( 'ctype_digit' );
	}

	protected function consumeSpace() {
		return $this->consumeWhile( function ( $c ) {
			return $c === ' ' || $c === "\x09";
		} );
	}

	protected function consumeWhitespace() {
		return $this->consumeWhile( 'ctype_space' );
	}

	protected function consumePlaintext() {
		// Most lines contain no markup.
		//
		// To micro-optimize, grab the whole line
		// and use strpos to search delimiters,
		// which is faster than iterating the string.

		$newline = strpos( $this->input, "\n", $this->pos );
		if ( $newline === false ) {
			$length = $this->len - $this->pos;
		} else {
			$length = $newline - $this->pos;
		}
		$line = substr( $this->input, $this->pos, $length );

		$amp = strpos( $line, '&' );
		if ( $amp !== false ) {
			$length = min( $length, $amp );
		}

		$lt = strpos( $line, '<' );
		if ( $lt !== false ) {
			$length = min( $length, $lt );
		}

		$this->pos += $length;
		return substr( $line, 0, $length );
	}

	protected function consumeEntity() {
		$this->saveState();
		$entity = '';

		// Gime an amp!
		$c = $this->peek();
		if ( $c !== '&' ) {
			$this->restoreState();
			return '';
		}
		$entity .= $c;
		$this->consume();

		$c = $this->peek();
		if ( $c === '#' ) {
			// Numeric char reference...
			$entity .= $c;
			$this->consume();

			$c = $this->peek();
			if ( $c === 'x' || $c === 'X' ) {
				// Hex?
				$entity .= $c;
				$this->consume();

				$digits = $this->consumeHexDigits();
				if ( $digits === '' ) {
					$this->restoreState();
					return '';
				}
				$entity .= $digits;
			} else {
				// Decimal?
				$digits = $this->consumeDigits();
				if ( $digits === '' ) {
					$this->restoreState();
					return '';
				}
				$entity .= $digits;
			}
		} else {
			// Named char reference.
			$name = $this->consumeAlphanum();
			if ( $name === '' ) {
				$this->restoreState();
				return '';
			}
			$entity .= $name;
		}
		// And finally the semicolon!
		$c = $this->peek();
		if ( $c !== ';' ) {
			$this->restoreState();
			return '';
		}
		$entity .= $c;
		$this->consume();

		$this->discardState();
		return html_entity_decode( $entity, ENT_QUOTES | ENT_HTML5, 'utf-8' );
	}

	protected function consumeTimestamp() {
		$accumulator = 0;
		do {
			$digits = $this->consumeDigits();
			if ( $digits === '' ) {
				$this->recordError( 'Expected digit in timestamp' );
				return false;
			}
			$accumulator += $digits;
			$this->consumeSpace();

			$c = $this->peek();
			if ( $c === ':' ) {
				$this->consume();
				$this->consumeSpace();
				$accumulator *= 60;
				continue;
			} elseif ( $c === ',' || $c === '.' ) {
				$this->consume();
				$millis = $this->consumeDigits();
				if ( $millis === '' ) {
					$this->recordError( 'Expected digit in millis' );
					return false;
				}
				$accumulator += ( $millis / 1000.0 );
				return $accumulator;
			} else {
				return $accumulator;
			}
		} while ( true );
	}

	protected function consumeArrow() {
		$this->saveState();
		do {
			$c = $this->peek();
			if ( $c === '-' ) {
				$this->consume();
			} elseif ( $c === '>' ) {
				$this->consume();
				$this->discardState();
				return '-->';
			} else {
				$this->restoreState();
				return false;
			}
		} while ( true );
	}

	protected function recordError( $msg ) {
		$newlinePos = strpos( $this->input, "\n", $this->lineStart );
		if ( $newlinePos === false ) {
			$lineLength = $this->len - $this->lineStart;
		} else {
			$lineLength = $newlinePos - $this->lineStart;
		}
		$this->errors[] = new ParseError(
			$this->line,
			substr( $this->input, $this->lineStart, $lineLength ),
			$msg
		);
	}

	protected function pushStack( DOM\Node $node ) {
		$this->current->appendNode( $node );
		if ( $node instanceof DOM\InternalNode ) {
			array_push( $this->stack, $node );
			$this->current = $node;
		}
	}

	protected function popStack() {
		array_pop( $this->stack );
		$this->current = $this->stack[count( $this->stack ) - 1];
	}

	protected function parse( $text ) {
		$this->len = strlen( $text );
		$this->input = $text;

		$this->states = [];
		$this->pos = 0;
		$this->line = 0;
		$this->lineStart = 0;

		// Build a parser state -> method dispatch map
		$map = [];
		$class = new \ReflectionClass( self::class );
		foreach ( $class->getMethods() as $method ) {
			$name = $method->getName();
			if ( substr( $name, 0, 5 ) === 'state' ) {
				$state = substr( $name, 5 );
				$map[$state] = [ $this, $name ];
			}
		}

		$state = 'Start';
		do {
			if ( isset( $map[$state] ) ) {
				$state = $map[$state]();
			} else {
				throw new \Exception( 'Invalid internal state ' . $state );
			}
		} while ( $state !== 'End' );
	}

	public function stateStart() {
		$c = $this->peek();
		if ( $c === '' ) {
			return 'End';
		} elseif ( \ctype_digit( $c ) ) {
			$this->cue = new DOM\Cue();
			$this->cue->id = $this->consumeDigits();

			$c = $this->peek();
			if ( $c === '' ) {
				return 'UnexpectedEnd';
			} elseif ( \ctype_space( $c ) ) {
				// It's supposed to be delimited by a line ending...
				// But some input files are messy and squish on one line.
				$this->consumeWhitespace();
				return 'Timestamp';
			} else {
				$this->recordError( 'Expected newline after cue id' );
				// @fixme consume until next double newline?
				$this->consumeLine();
				return 'Start';
			}
		} elseif ( \ctype_space( $c ) ) {
			// Extra whitespace or blank lines are icky
			$this->consumeWhitespace();
			return 'Start';
		} else {
			$this->recordError( 'Expected digit cue id' );
			$this->consumeLine();
			return 'Start';
		}
	}

	public function stateTimestamp() {
		$ts = $this->consumeTimestamp();
		if ( $ts === false ) {
			$this->recordError( 'Expected start timestamp' );
			$this->consumeLine();
			return 'Start';
		}
		$this->cue->start = $ts;

		$this->consumeWhitespace();

		$arrow = $this->consumeArrow();
		if ( $arrow === false ) {
			$this->recordError( 'Expected timestamp arrow' );
			$this->consumeLine();
			return 'Start';
		}

		$this->consumeWhitespace();

		$ts = $this->consumeTimestamp();
		if ( $ts === false ) {
			$this->recordError( 'Expected end timestamp' );
			$this->consumeLine();
			return 'End';
		}
		$this->cue->end = $ts;

		// Should end with a newline, but spaces happen.
		$this->consumeWhitespace();

		return 'TextStart';
	}

	public function stateTextStart() {
		$base = new DOM\InternalNode;
		$this->stack = [ $base ];
		$this->current = $base;
		$this->tag = '';
		$this->tagSource = '';
		$this->text = '';
		return 'Text';
	}

	public function stateText() {
		$c = $this->peek();
		if ( $c === '<' ) {
			$this->consume();

			// Save any text spans we've been working on
			if ( $this->text !== '' ) {
				$this->current->appendNode( new DOM\TextNode( $this->text ) );
				$this->text = '';
			}

			$this->tag = '';
			$this->tagSource = $c;
			return 'TagStart';
		} elseif ( $c === '&' ) {
			$entity = $this->consumeEntity();
			if ( $entity === '' ) {
				$this->consume();
				$this->text .= '&';
			} else {
				$this->text .= $entity;
			}
			return 'Text';
		} elseif ( $c === '' ) {
			return 'TextEnd';
		} elseif ( $c === "\n" ) {
			$this->consume();
			return 'TextNewline';
		} else {
			$this->text .= $this->consumePlaintext();
			return 'Text';
		}
	}

	public function stateTextNewline() {
		$c = $this->peek();
		if ( $c === "\n" ) {
			// Second newline terminates the cue text.
			return 'TextEnd';
		} else {
			$this->text .= "\n";
			return 'Text';
		}
	}

	public function stateTagStart() {
		$c = $this->peek();
		if ( $c === '/' ) {
			$this->consume();
			$this->tagSource .= $c;
			return 'TagCloseMain';
		} else {
			return 'TagMain';
		}
	}

	public function stateTagMain() {
		$c = $this->consume();
		$this->tagSource .= $c;
		if ( $c === ' ' || $c === "\x09" ) {
			return 'TagSpace';
		} elseif ( $c === '/' ) {
			return 'TagSelfClose';
		} elseif ( $c === '>' ) {
			return 'TagEnd';
		} elseif ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		} else {
			$this->tag .= $c;
			return 'TagMain';
		}
	}

	public function stateTagSelfClose() {
		$c = $this->consume();
		$this->tagSource .= $c;
		if ( $c === '>' ) {
			switch ( strtolower( $this->tag ) ) {
			case 'br':
				$node = new DOM\TextNode( "\n" );
				break;
			default:
				$node = new DOM\TextNode( $this->tagSource );
			}
			$this->current->appendNode( $node );
			return 'Text';
		} elseif ( $c === ' ' || $c === "\x09" ) {
			// bleeeeh
			return 'TagSelfClose';
		} elseif ( $c === '' ) {
			return 'TextEnd';
		} else {
			$this->tag .= $c;
			return 'TagSelfClose';
		}
	}

	public function stateTagCloseMain() {
		$c = $this->consume();
		$this->tagSource .= $c;
		if ( $c === '>' ) {
			switch ( strtolower( $this->tag ) ) {
			case 'b':
				$match = $this->current instanceof DOM\BoldNode;
				break;
			case 'i':
				$match = $this->current instanceof DOM\ItalicNode;
				break;
			case 'u':
				$match = $this->current instanceof DOM\UnderlineNode;
				break;
			// case 'big':
			// case 'font':
			default:
				$match = false;
			}
			if ( $match ) {
				$this->popStack();
			} else {
				$node = new DOM\TextNode( $this->tagSource );
				$this->current->appendNode( $node );
			}
			return 'Text';
		} elseif ( $c === ' ' || $c === "\x09" ) {
			$node = new DOM\TextNode( $this->tagSource );
			$this->current->appendNode( $node );
			return 'Text';
		} elseif ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		} else {
			$this->tag .= $c;
			return 'TagCloseMain';
		}
	}

	public function stateTagSpace() {
		$c = $this->consume();
		$this->tagSource .= $c;
		// @todo accept some attributes
		if ( $c === '>' ) {
			return 'TagEnd';
		} elseif ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		} else {
			return 'TagSpace';
		}
	}

	public function stateTagEnd() {
		switch ( strtolower( $this->tag ) ) {
		case 'b':
			$node = new DOM\BoldNode;
			break;
		case 'i':
			$node = new DOM\ItalicNode;
			break;
		case 'u':
			$node = new DOM\UnderlineNode;
			break;
		case 'br':
			$node = new DOM\TextNode( "\n" );
			break;
		// case 'big':
		// case 'font':
		default:
			// Anything else don't recognize it...?
			$node = new DOM\TextNode( $this->tagSource );
		}
		$this->pushStack( $node );
		return 'Text';
	}

	public function stateTextEnd() {
		if ( $this->text !== '' ) {
			$this->current->appendNode( new DOM\TextNode( $this->text ) );
		}
		return 'CueDone';
	}

	public function stateCueDone() {
		$this->cue->nodes = $this->stack[0]->nodes;
		$this->cues[] = $this->cue;

		$this->stack = [];
		$this->current = null;
		$this->tag = '';
		$this->tagSource = '';
		$this->text = '';

		return 'Start';
	}

	public function stateUnexpectedEnd() {
		$this->recordError( 'Unexpected end of file' );
		return 'End';
	}
}
