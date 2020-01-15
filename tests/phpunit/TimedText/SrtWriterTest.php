<?php
/**
 * SrtWriter test.
 *
 * @ingroup timedmedia
 */

use MediaWiki\TimedMediaHandler\TimedText\DOM;
use MediaWiki\TimedMediaHandler\TimedText\SrtWriter;

/**
 * @covers SrtWriter::write
 */
class SrtWriterTest extends PHPUnit\Framework\TestCase {
	public function setUp() : void {
		parent::setUp();
		$this->writer = new SrtWriter;
	}

	public function testSingleCue() {
		// from https://en.wikipedia.org/wiki/SubRip
		$expected = <<<END
168
00:20:41,150 --> 00:20:45,109
- How did he do that?
- Made him an offer he couldn't refuse.
END;

		$cue = new DOM\Cue();
		$cue->id = '168';
		$cue->start = 1241.15;
		$cue->end = 1245.109;
		$cue->appendText( "- How did he do that?\n" .
			"- Made him an offer he couldn't refuse." );
		$cues = [ $cue ];

		$output = $this->writer->write( $cues );

		$this->assertEquals( $expected, $output );
	}

	public function testCueWithLineBreak() {
		// From File:Folgers.ogv.sv.srt
		$expected = <<<END
6
00:00:12,002 --> 00:00:17,000
Harold, skaka inte bara på huvudet,\x20
\x20
du måste berätta vad som är fel med kaffet.
END;

		$cue = new DOM\Cue();
		$cue->id = '6';
		$cue->start = 12.002;
		$cue->end = 17;
		$cue->appendText(
			"Harold, skaka inte bara på huvudet, \n\n" .
			"du måste berätta vad som är fel med kaffet."
		);
		$cues = [ $cue ];

		$output = $this->writer->write( $cues );

		$this->assertEquals( $expected, $output );
	}

	public function testCueWithLineBreakTwoNodes() {
		$expected = <<<END
6
00:00:12,002 --> 00:00:17,000
Harold, skaka inte bara på huvudet,\x20
\x20
du måste berätta vad som är fel med kaffet.
END;

		$cue = new DOM\Cue();
		$cue->id = '6';
		$cue->start = 12.002;
		$cue->end = 17;
		$cue->appendText( "Harold, skaka inte bara på huvudet, " );
		$cue->appendText( "\n" ); // the <br> maps to &#10;
		$cue->appendText( "\n" );
		$cue->appendText( "du måste berätta vad som är fel med kaffet." );
		$cues = [ $cue ];

		$output = $this->writer->write( $cues );

		$this->assertEquals( $expected, $output );
	}
}
