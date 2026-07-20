<?php

use MediaWiki\TimedMediaHandler\TimedText\DOM;
use MediaWiki\TimedMediaHandler\TimedText\VttReader;

/**
 * @covers \MediaWiki\TimedMediaHandler\TimedText\VttReader
 */
class VttReaderTest extends PHPUnit\Framework\TestCase {
	/** @var VttReader */
	private $reader;

	protected function setUp(): void {
		parent::setUp();
		$this->reader = new VttReader();
	}

	public function testHeader() {
		$this->reader->read( "WEBVTT\n" );
		$this->assertSame( [], $this->reader->getErrors() );
		$this->assertCount( 0, $this->reader->getCues() );
	}

	public function testHeaderWithBOM() {
		$this->reader->read( "\xEF\xBB\xBFWEBVTT\n" );
		$this->assertSame( [], $this->reader->getErrors() );
	}

	public function testHeaderWithComment() {
		$this->reader->read( "WEBVTT hi just some --> commentary\n\n" );
		$this->assertSame( [], $this->reader->getErrors() );
	}

	public function testMissingHeader() {
		$this->reader->read( "\nWEBVTT" );
		$this->assertNotEmpty( $this->reader->getErrors() );
	}

	public function testSingleParse() {
		$input = <<<END
			WEBVTT

			168
			00:20:41.150 --> 00:20:45.109 align:center
			- How did he do that?
			- Made him an offer he couldn't refuse.
			END;

		$this->reader->read( $input );
		$this->assertSame( [], $this->reader->getErrors() );

		$cues = $this->reader->getCues();
		$this->assertCount( 1, $cues );

		$cue = $cues[0];
		$this->assertSame( '168', $cue->id );
		$this->assertSame( 1241.15, $cue->start );
		$this->assertSame( 1245.109, $cue->end );
		$this->assertSame(
			"- How did he do that?\n- Made him an offer he couldn't refuse.",
			$this->flatten( $cue )
		);
	}

	public function testWithoutIdentifier() {
		$input = <<<END
			WEBVTT

			00:20:41.150 --> 00:20:45.109
			- How did he do that?
			END;

		$this->reader->read( $input );
		$this->assertSame( [], $this->reader->getErrors() );

		$cues = $this->reader->getCues();
		$this->assertCount( 1, $cues );
		$this->assertSame( '', $cues[0]->id );
		$this->assertSame( '- How did he do that?', $this->flatten( $cues[0] ) );
	}

	public function testOptionalHours() {
		$input = <<<END
			WEBVTT

			1
			20:41.150 --> 20:45.109
			Test
			END;

		$this->reader->read( $input );
		$this->assertSame( [], $this->reader->getErrors() );

		$cues = $this->reader->getCues();
		$this->assertCount( 1, $cues );
		$this->assertSame( 1241.15, $cues[0]->start );
		$this->assertSame( 1245.109, $cues[0]->end );
		$this->assertSame( 'Test', $this->flatten( $cues[0] ) );
	}

	public function testCueMarkup() {
		$input = <<<END
			WEBVTT

			1
			00:00:01.000 --> 00:00:02.000
			Hello <b>world</b> and <v Bob>hi</v>
			END;

		$this->reader->read( $input );
		$this->assertSame( [], $this->reader->getErrors() );

		$nodes = $this->reader->getCues()[0]->nodes;
		$this->assertInstanceOf( DOM\TextNode::class, $nodes[0] );
		$this->assertInstanceOf( DOM\BoldNode::class, $nodes[1] );
		$this->assertSame( 'world', $this->flattenNodes( $nodes[1]->nodes ) );
		// Voice has no SRT equivalent: it becomes a plain container that keeps
		// only its text.
		$this->assertSame( DOM\InternalNode::class, get_class( $nodes[3] ) );
		$this->assertSame( 'hi', $this->flattenNodes( $nodes[3]->nodes ) );
	}

	public function testTimestampIsDropped() {
		$input = <<<END
			WEBVTT

			1
			00:00:08.000 --> 00:00:10.000
			With <00:00:08.500>timestamp
			END;

		$this->reader->read( $input );
		$this->assertSame( [], $this->reader->getErrors() );

		// SRT cannot express in-cue timestamps, so they are dropped while the
		// surrounding text is preserved.
		$this->assertSame( 'With timestamp', $this->flatten( $this->reader->getCues()[0] ) );
	}

	private function flatten( DOM\Cue $cue ): string {
		return $this->flattenNodes( $cue->nodes );
	}

	private function flattenNodes( array $nodes ): string {
		$out = '';
		foreach ( $nodes as $node ) {
			if ( $node instanceof DOM\TextNode ) {
				$out .= $node->text;
			} elseif ( $node instanceof DOM\InternalNode ) {
				$out .= $this->flattenNodes( $node->nodes );
			}
		}
		return $out;
	}
}
