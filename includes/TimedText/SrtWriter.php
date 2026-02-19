<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class SrtWriter extends Writer {

	/** @inheritDoc */
	public function write( $cues ) {
		return implode( "\n\n",
			array_map(
				[ $this, 'formatCue' ],
				$cues
			)
		);
	}

	/**
	 * @param DOM\Cue $cue cue to output
	 * @return string
	 */
	public function formatCue( DOM\Cue $cue ) {
		return (int)$cue->id .
			"\n" .
			$this->formatTimestamp( $cue->start ) .
			" --> " .
			$this->formatTimestamp( $cue->end ) .
			"\n" .
			$this->formatText( $this->formatNodes( $cue->nodes ) );
	}

	/**
	 * @param float $time
	 *
	 * @return string
	 */
	public function formatTimestamp( $time ) {
		$s = floor( $time );
		$frac = $time - $s;
		$millis = round( $frac * 1000.0 );

		$seconds = $s % 60;
		$s = ( $s - $seconds ) / 60;

		$minutes = $s % 60;
		$s = ( $s - $minutes ) / 60;

		$hours = $s;

		return sprintf( "%02d:%02d:%02d,%03d",
			$hours,
			$minutes,
			$seconds,
			$millis
		);
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function formatText( $text ) {
		// Ensure there can be no blank lines in the cue.
		// But cues may span multiple lines.
		return str_replace( "\n\n", "\n \n", $text );
	}

	/**
	 * @param array $nodes
	 *
	 * @return string
	 */
	public function formatNodes( $nodes ) {
		$s = '';
		foreach ( $nodes as $node ) {
			$s .= $this->formatNode( $node );
		}
		return $s;
	}

	/**
	 * @param DOM\Node $node
	 *
	 * @return string
	 */
	public function formatNode( DOM\Node $node ) {
		if ( $node instanceof DOM\InternalNode ) {
			if ( $node instanceof DOM\BoldNode ) {
				$tag = 'b';
			} elseif ( $node instanceof DOM\ItalicNode ) {
				$tag = 'i';
			} elseif ( $node instanceof DOM\UnderlineNode ) {
				$tag = 'u';
			} else {
				$tag = '';
			}

			$content = $this->formatNodes( $node->nodes );

			if ( $tag ) {
				return "<$tag>$content</$tag>";
			}
			return $content;
		}

		if ( $node instanceof DOM\TextNode ) {
			return htmlspecialchars( $node->text, ENT_NOQUOTES | ENT_HTML5, 'utf-8' );
		}

		return '';
	}
}
