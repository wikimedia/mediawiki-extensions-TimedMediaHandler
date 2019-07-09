<?php

namespace MediaWiki\TimedMediaHandler\TimedText;

class VttWriter extends Writer {
	// https://www.w3.org/TR/webvtt1/
	//
	// note: webvtt ids may be alphanumeric
	// note: position info may follow on the timestamp line
	// todo: style blocks
	// todo: note/comment blocks
	// todo: region blocks
	// todo: formatting
	// todo: chapter markers -- is that the id?
	// todo: metadata (json blobs)

	protected $usedIdentifiers = [];

	/**
	 * @inheritDoc
	 */
	public function write( $cues ) {
		return "WEBVTT\n\n" . implode( "\n\n",
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
		return $this->normalizeCueId( $cue->id, $this->usedIdentifiers ) .
			"\n" .
			$this->formatTimestamp( $cue->start ) .
			" --> " .
			$this->formatTimestamp( $cue->end ) .
			"\n" .
			$this->fixNewlines( $this->formatNodes( $cue->nodes ) );
	}

	public function normalizeCueId( $id, &$usedMap ) {
		// https://www.w3.org/TR/webvtt1/#webvtt-cue-identifier
		$id = str_replace( "\n", "", $id );
		$id = str_replace( "-->", "- ->", $id );

		// Must be globally unique
		if ( isset( $usedMap[$id] ) ) {
			$i = 2;
			for ( $i = 2; ; $i++ ) {
				$alt = "$id $i";
				if ( !isset( $usedMap[$alt] ) ) {
					$id = $alt;
					break;
				}
			}
		}
		$usedMap[$id] = true;

		return $id;
	}

	public function formatTimestamp( $time ) {
		//
		$s = floor( $time );
		$frac = $time - $s;
		$millis = round( $frac * 1000.0 );

		$seconds = $s % 60;
		$s = ( $s - $seconds ) / 60;

		$minutes = $s % 60;
		$s = ( $s - $minutes ) / 60;

		$hours = $s;

		if ( $hours > 0 ) {
			return sprintf( "%02d:%02d:%02d.%03d",
				$hours,
				$minutes,
				$seconds,
				$millis
			);
		} else {
			return sprintf( "%02d:%02d.%03d",
				$minutes,
				$seconds,
				$millis
			);
		}
	}

	public function fixNewlines( $text ) {
		// Cues must not contain blank lines, but may
		// contain newlines as character references.
		//
		// @todo use &#10; instead of adding a space here;
		// but that's not supported yet by VideoJS or Firefox.
		return str_replace( "\n\n", "\n \n", $text );
	}

	public function formatText( $text ) {
		// < and > and & and friends are special, kinda like HTML
		// but not exactly
		return htmlspecialchars( $text, ENT_NOQUOTES | ENT_HTML5, 'utf-8' );
	}

	public function formatNodes( $nodes ) {
		$s = '';
		foreach ( $nodes as $node ) {
			$s .= $this->formatNode( $node );
		}
		return $s;
	}

	public function formatNode( DOM\Node $node ) {
		if ( $node instanceof DOM\InternalNode ) {
			if ( $node instanceof DOM\ClassNode ) {
				$tag = 'c';
			} elseif ( $node instanceof DOM\ItalicNode ) {
				$tag = 'i';
			} elseif ( $node instanceof DOM\BoldNode ) {
				$tag = 'b';
			} elseif ( $node instanceof DOM\UnderlineNode ) {
				$tag = 'u';
			} elseif ( $node instanceof DOM\RubyNode ) {
				$tag = 'ruby';
			} elseif ( $node instanceof DOM\RubyTextNode ) {
				$tag = 'rt';
			} elseif ( $node instanceof DOM\LanguageNode ) {
				$tag = 'lang';
			} else {
				$tag = '';
			}

			$content = $this->formatNodes( $node->nodes );

			if ( $tag ) {
				$out = '<';
				$out .= $tag;
				foreach ( $node->classes as $class ) {
					$out .= '.';
					$out .= $class;
				}
				if ( $node->annotation !== '' ) {
					$out .= ' ';
					$out .= $node->annotation;
				}
				$out .= '>';
				$out .= $content;
				$out .= '</';
				$out .= $tag;
				$out .= '>';
				return $out;
			} else {
				return $content;
			}
		} elseif ( $node instanceof DOM\TextNode ) {
			return $this->formatText( $node->text );
		} elseif ( $node instanceof DOM\TimestampNode ) {
			return '<' . $this->formatTimestamp( $node->timestamp ) . '>';
		} else {
			return '';
		}
	}
}
