<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

use MediaWiki\TimedMediaHandler\TimedText\DOM\BoldNode;
use MediaWiki\TimedMediaHandler\TimedText\DOM\Cue;
use MediaWiki\TimedMediaHandler\TimedText\DOM\InternalNode;
use MediaWiki\TimedMediaHandler\TimedText\DOM\ItalicNode;
use MediaWiki\TimedMediaHandler\TimedText\DOM\Node;
use MediaWiki\TimedMediaHandler\TimedText\DOM\TextNode;
use MediaWiki\TimedMediaHandler\TimedText\DOM\UnderlineNode;
use WebVTT\DOM\CueText\ElementNode as VividElementNode;
use WebVTT\DOM\CueText\Node as VividNode;
use WebVTT\DOM\CueText\TextNode as VividTextNode;
use WebVTT\DOM\Enums\CueTag;
use WebVTT\DOM\VttFile;
use WebVTT\VttReader as VividReader;

/**
 * A WebVTT (.vtt) subtitle file reader.
 *
 * This is a thin adapter over the hartman/vtt-vivid library: it parses the
 * input with vtt-vivid and maps the resulting cues and cue text node tree onto
 * the TimedText DOM used by the writers. The parsed vtt-vivid file is also
 * kept, so callers that stay within WebVTT can round-trip losslessly via
 * toVtt().
 */
class VttReader extends Reader {
	/** @var Cue[] */
	protected $cues = [];
	/** @var ParseError[] */
	protected $errors = [];
	/** @var VttFile|null */
	private $vttFile;

	/** @inheritDoc */
	public function read( $input ) {
		$this->cues = [];
		$this->errors = [];

		$reader = VividReader::fromString( $input );
		$reader->parse();
		$this->vttFile = $reader->getVTTFile();

		foreach ( $this->vttFile->getCues() as $vttCue ) {
			$cue = new Cue();
			$cue->id = $vttCue->getId();
			$cue->start = $vttCue->getStartTime();
			$cue->end = $vttCue->getEndTime();
			// @todo carry cue settings (position/line/align/region) once the
			// writers support them.
			foreach ( $vttCue->getContentNodes() as $node ) {
				$converted = $this->convertNode( $node );
				if ( $converted ) {
					$cue->appendNode( $converted );
				}
			}
			$this->cues[] = $cue;
		}

		foreach ( $reader->errors as $message ) {
			$this->errors[] = $this->toParseError( (string)$message );
		}
	}

	/**
	 * Convert a vtt-vivid cue text node into a TimedText DOM node.
	 *
	 * The TimedText DOM only models what SRT can express (text and
	 * bold/italic/underline), so richer WebVTT constructs are down-converted:
	 * classes, voice, language and ruby become plain containers that keep only
	 * their text, and in-cue timestamps are dropped.
	 *
	 * @param VividNode $node
	 * @return Node|null Null for nodes that have no SRT-expressible content.
	 */
	private function convertNode( VividNode $node ): ?Node {
		if ( $node instanceof VividTextNode ) {
			return new TextNode( $node->getValue() );
		}
		if ( !$node instanceof VividElementNode ) {
			// In-cue timestamps and anything else without children.
			return null;
		}
		$internal = $this->createNode( $node->getTag() );
		foreach ( $node->getChildren() as $child ) {
			$converted = $this->convertNode( $child );
			if ( $converted ) {
				$internal->appendNode( $converted );
			}
		}
		return $internal;
	}

	/**
	 * @param CueTag $tag WebVTT cue text tag
	 * @return InternalNode
	 */
	private function createNode( CueTag $tag ): InternalNode {
		switch ( $tag ) {
			case CueTag::ITALIC:
				return new ItalicNode();
			case CueTag::BOLD:
				return new BoldNode();
			case CueTag::UNDERLINE:
				return new UnderlineNode();
			default:
				// c, ruby, rt, v, lang have no SRT equivalent; keep the text.
				return new InternalNode();
		}
	}

	/**
	 * vtt-vivid reports errors as strings, optionally prefixed with "Line N: ".
	 *
	 * @param string $message
	 * @return ParseError
	 */
	private function toParseError( string $message ): ParseError {
		$line = 0;
		if ( preg_match( '/^Line (\d+): (.*)$/s', $message, $m ) ) {
			$line = (int)$m[1];
			$message = $m[2];
		}
		return new ParseError( $line, '', $message );
	}

	/** @inheritDoc */
	public function getCues() {
		return $this->cues;
	}

	/** @inheritDoc */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Serialize the parsed WebVTT back to a normalized WebVTT string.
	 *
	 * Unlike a round-trip through the TimedText DOM and VttWriter, this
	 * preserves regions, style blocks, notes and cue settings.
	 *
	 * @return string
	 */
	public function toVtt(): string {
		return $this->vttFile ? $this->vttFile->toVtt() : "WEBVTT\n";
	}
}
