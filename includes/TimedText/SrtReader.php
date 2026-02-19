<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

use LogicException;
use ReflectionClass;

/**
 * A fairly lax SubRip (.srt) subtitle file reader.
 * Should accept a lot of sloppy syntax we have in our files.
 */
class SrtReader extends Reader {
	/** @var DOM\Cue[] */
	protected $cues = [];
	/** @var ParseError[] */
	protected $errors = [];

	/** @var string */
	protected $input = '';
	/** @var int */
	protected $len = 0;

	/**
	 * @var int[][]
	 * @phan-var list<array{pos:int,line:int,lineStart:int}>
	 */
	protected $states = [];
	/** @var int */
	protected $pos = 0;
	/** @var int */
	protected $line = 0;
	/** @var int */
	protected $lineStart = 0;

	/** @var DOM\Cue|null */
	protected $cue;
	/** @var string */
	protected $tag = '';
	/** @var string */
	protected $tagSource = '';
	/** @var string */
	protected $text = '';
	/** @var DOM\InternalNode[] */
	protected $stack = [];
	/** @var DOM\InternalNode|null */
	protected $current;

	/** @inheritDoc */
	public function read( $input ) {
		// Trim BOM if present.
		$bom = "\xEF\xBB\xBF";

		if ( strncmp( $input, $bom, 3 ) === 0 ) {
			$input = substr( $input, 3 );
		}
		$this->parse( $input );
	}

	/** @inheritDoc */
	public function getCues() {
		return $this->cues;
	}

	/** @inheritDoc */
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
		if ( !$this->states ) {
			throw new LogicException( 'No saved state to discard' );
		}
		$state = array_pop( $this->states );
		$this->pos = $state['pos'];
		$this->line = $state['line'];
		$this->lineStart = $state['lineStart'];
	}

	protected function discardState() {
		if ( !count( $this->states ) ) {
			throw new LogicException( 'No saved state to discard' );
		}
		array_pop( $this->states );
	}

	protected function eof(): bool {
		return ( $this->pos >= $this->len );
	}

	protected function peek(): string {
		if ( $this->pos < $this->len ) {
			return $this->input[$this->pos];
		}
		return '';
	}

	protected function consume(): string {
		if ( $this->pos < $this->len ) {
			$c = $this->input[$this->pos++];
			if ( $c === "\n" ) {
				$this->line++;
				$this->lineStart = $this->pos;
			}
			return $c;
		}
		return '';
	}

	/**
	 * @param callable $callback
	 */
	protected function consumeWhile( $callback ): string {
		$str = '';
		while ( $this->pos < $this->len ) {
			$c = $this->input[$this->pos];
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

	protected function consumeLine(): string {
		return $this->consumeWhile( static function ( $c ) {
			return $c !== "\n" && $c !== '';
		} );
	}

	protected function consumeAlphanum(): string {
		return $this->consumeWhile( 'ctype_alnum' );
	}

	protected function consumeHexDigits(): string {
		return $this->consumeWhile( 'ctype_xdigit' );
	}

	protected function consumeDigits(): string {
		return $this->consumeWhile( 'ctype_digit' );
	}

	protected function consumeSpace(): string {
		return $this->consumeWhile( static function ( $c ) {
			return $c === ' ' || $c === "\x09";
		} );
	}

	protected function consumeWhitespace(): string {
		return $this->consumeWhile( 'ctype_space' );
	}

	protected function consumePlaintext(): string {
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

	protected function consumeEntity(): string {
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
			} else {
				// Decimal?
				$digits = $this->consumeDigits();
			}
			if ( $digits === '' ) {
				$this->restoreState();
				return '';
			}
			$entity .= $digits;
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

	/**
	 * @return float|false
	 */
	protected function consumeTimestamp() {
		$accumulator = 0.0;
		do {
			$digits = $this->consumeDigits();
			if ( $digits === '' ) {
				$this->recordError( 'Expected digit in timestamp' );
				return false;
			}
			$accumulator += (float)$digits;
			$this->consumeSpace();

			$c = $this->peek();
			if ( $c === ':' ) {
				$this->consume();
				$this->consumeSpace();
				$accumulator *= 60.0;
				continue;
			}

			if ( $c === ',' || $c === '.' ) {
				$this->consume();
				$millis = $this->consumeDigits();
				if ( $millis === '' ) {
					$this->recordError( 'Expected digit in millis' );
					return false;
				}
				$accumulator += ( (float)$millis / 1000.0 );
			}
			return $accumulator;
		} while ( true );
	}

	/** @return string|false */
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

	/**
	 * @param string $msg
	 */
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
			$this->stack[] = $node;
			$this->current = $node;
		}
	}

	protected function popStack() {
		array_pop( $this->stack );
		$this->current = $this->stack[count( $this->stack ) - 1];
	}

	/**
	 * @param string $text
	 */
	protected function parse( $text ) {
		$this->len = strlen( $text );
		$this->input = $text;

		$this->states = [];
		$this->pos = 0;
		$this->line = 0;
		$this->lineStart = 0;

		// Build a parser state -> method dispatch map
		$map = [];
		$class = new ReflectionClass( self::class );
		foreach ( $class->getMethods() as $method ) {
			$name = $method->getName();
			if ( strncmp( $name, 'state', 5 ) === 0 ) {
				$state = substr( $name, 5 );
				$map[$state] = [ $this, $name ];
			}
		}

		$state = 'Start';
		do {
			if ( isset( $map[$state] ) ) {
				$state = $map[$state]();
			} else {
				throw new LogicException( 'Invalid internal state ' . $state );
			}
		} while ( $state !== 'End' );
	}

	public function stateStart(): string {
		$c = $this->peek();
		if ( $c === '' ) {
			return 'End';
		}

		if ( \ctype_digit( $c ) ) {
			$this->cue = new DOM\Cue();
			$this->cue->id = $this->consumeDigits();

			$c = $this->peek();
			if ( $c === '' ) {
				return 'UnexpectedEnd';
			}

			if ( \ctype_space( $c ) ) {
				// It's supposed to be delimited by a line ending...
				// But some input files are messy and squish on one line.
				$this->consumeWhitespace();
				return 'Timestamp';
			}

			$this->recordError( 'Expected newline after cue id' );
			// @fixme consume until next double newline?
			$this->consumeLine();
			return 'Start';
		}

		if ( \ctype_space( $c ) ) {
			// Extra whitespace or blank lines are icky
			$this->consumeWhitespace();
		} else {
			$this->recordError( 'Expected digit cue id' );
			$this->consumeLine();
		}
		return 'Start';
	}

	public function stateTimestamp(): string {
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

	public function stateTextStart(): string {
		$base = new DOM\InternalNode;
		$this->stack = [ $base ];
		$this->current = $base;
		$this->tag = '';
		$this->tagSource = '';
		$this->text = '';
		return 'Text';
	}

	public function stateText(): string {
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
		}

		if ( $c === '&' ) {
			$entity = $this->consumeEntity();
			if ( $entity === '' ) {
				$this->consume();
				$this->text .= '&';
			} else {
				$this->text .= $entity;
			}
			return 'Text';
		}

		if ( $c === '' ) {
			return 'TextEnd';
		}

		if ( $c === "\n" ) {
			$this->consume();
			return 'TextNewline';
		}

		$this->text .= $this->consumePlaintext();
		return 'Text';
	}

	public function stateTextNewline(): string {
		$c = $this->peek();
		if ( $c === "\n" ) {
			// Second newline terminates the cue text.
			return 'TextEnd';
		}

		$this->text .= "\n";
		return 'Text';
	}

	public function stateTagStart(): string {
		$c = $this->peek();
		if ( $c === '/' ) {
			$this->consume();
			$this->tagSource .= $c;
			return 'TagCloseMain';
		}

		return 'TagMain';
	}

	public function stateTagMain(): string {
		$c = $this->consume();
		$this->tagSource .= $c;
		if ( $c === ' ' || $c === "\x09" ) {
			return 'TagSpace';
		}

		if ( $c === '/' ) {
			return 'TagSelfClose';
		}

		if ( $c === '>' ) {
			return 'TagEnd';
		}

		if ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		}

		$this->tag .= $c;
		return 'TagMain';
	}

	public function stateTagSelfClose(): string {
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
		}

		if ( $c === ' ' || $c === "\x09" ) {
			// bleeeeh
			return 'TagSelfClose';
		}

		if ( $c === '' ) {
			return 'TextEnd';
		}

		$this->tag .= $c;
		return 'TagSelfClose';
	}

	public function stateTagCloseMain(): string {
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
		}

		if ( $c === ' ' || $c === "\x09" ) {
			$node = new DOM\TextNode( $this->tagSource );
			$this->current->appendNode( $node );
			return 'Text';
		}

		if ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		}

		$this->tag .= $c;
		return 'TagCloseMain';
	}

	public function stateTagSpace(): string {
		$c = $this->consume();
		$this->tagSource .= $c;
		// @todo accept some attributes
		if ( $c === '>' ) {
			return 'TagEnd';
		}

		if ( $c === '' ) {
			$this->text = $this->tagSource;
			return 'TextEnd';
		}

		return 'TagSpace';
	}

	public function stateTagEnd(): string {
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

	public function stateTextEnd(): string {
		if ( $this->text !== '' ) {
			$this->current->appendNode( new DOM\TextNode( $this->text ) );
		}
		return 'CueDone';
	}

	public function stateCueDone(): string {
		$this->cue->nodes = $this->stack[0]->nodes;
		$this->cues[] = $this->cue;

		$this->stack = [];
		$this->current = null;
		$this->tag = '';
		$this->tagSource = '';
		$this->text = '';

		return 'Start';
	}

	public function stateUnexpectedEnd(): string {
		$this->recordError( 'Unexpected end of file' );
		return 'End';
	}
}
