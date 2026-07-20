<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

use WebVTT\DOM\CueText\ElementNode as VividElementNode;
use WebVTT\DOM\CueText\Node as VividNode;
use WebVTT\DOM\CueText\TextNode as VividTextNode;
use WebVTT\DOM\Enums\CueTag;
use WebVTT\DOM\VttCue;
use WebVTT\DOM\VttFile;

/**
 * Writes TimedText DOM cues as WebVTT.
 *
 * This is an adapter over the hartman/vtt-vivid library: it serializes the
 * TimedText cue node tree to WebVTT cue text and lets vtt-vivid handle the
 * file framing (header, cue timing lines, normalization).
 *
 * Note this only carries what the TimedText DOM models (cues and cue text).
 * Regions, style blocks, notes and cue settings are not represented here; a
 * lossless WebVTT round-trip goes through VttReader::toVtt() instead.
 */
class VttWriter extends Writer {

	/** @inheritDoc */
	public function write( $cues ) {
		$file = new VttFile();
		foreach ( $cues as $cue ) {
			// vtt-vivid owns cue text serialization: its nodes escape themselves,
			// and VttCue collapses blank lines and escapes "-->" so the payload
			// can't prematurely end the cue. We only map our DOM onto its nodes.
			$vttCue = new VttCue( $cue->start, $cue->end, '' );
			$vttCue->setContentNodes( $this->convertNodes( $cue->nodes ) );
			if ( $cue->id !== '' ) {
				$vttCue->setId( $cue->id );
			}
			$file->addBlock( $vttCue );
		}
		return $file->toVtt();
	}

	/**
	 * @param DOM\Node[] $nodes
	 *
	 * @return VividNode[]
	 */
	private function convertNodes( array $nodes ): array {
		$out = [];
		foreach ( $nodes as $node ) {
			if ( $node instanceof DOM\TextNode ) {
				$out[] = new VividTextNode( $node->text );
			} elseif ( $node instanceof DOM\InternalNode ) {
				$children = $this->convertNodes( $node->nodes );
				$tag = $this->tagFor( $node );
				if ( $tag === null ) {
					// A container with no SRT-expressible tag: keep only its text.
					array_push( $out, ...$children );
				} else {
					$element = new VividElementNode( $tag );
					foreach ( $children as $child ) {
						$element->appendChild( $child );
					}
					$out[] = $element;
				}
			}
		}
		return $out;
	}

	private function tagFor( DOM\InternalNode $node ): ?CueTag {
		if ( $node instanceof DOM\ItalicNode ) {
			return CueTag::ITALIC;
		}
		if ( $node instanceof DOM\BoldNode ) {
			return CueTag::BOLD;
		}
		if ( $node instanceof DOM\UnderlineNode ) {
			return CueTag::UNDERLINE;
		}
		return null;
	}
}
