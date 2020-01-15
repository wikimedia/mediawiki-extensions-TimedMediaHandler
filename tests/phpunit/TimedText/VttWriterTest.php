<?php
/**
 * VttWriter test.
 *
 * @ingroup timedmedia
 */

use MediaWiki\TimedMediaHandler\TimedText\DOM;
use MediaWiki\TimedMediaHandler\TimedText\VttWriter;

/**
 * @covers SrtWriter::write
 */
class VttWriterTest extends PHPUnit\Framework\TestCase {
	public function setUp() : void {
		parent::setUp();
		$this->writer = new VttWriter;
	}

	public function testSingleCue() {
		// adapted from https://en.wikipedia.org/wiki/SubRip
		$input = <<<END
WEBVTT

168
20:41.150 --> 20:45.109
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

		$this->assertEquals( $input, $output );
	}

	public function testCueWithLineBreak() {
		// adapted from SRT input...
		$input = <<<END
6
00:00:12,002 --> 00:00:17
Harold, skaka inte bara på huvudet, <br>
du måste berätta vad som är fel med kaffet.
END;

		// @todo use &#10; instead of adding a space here;
		// but that's not supported yet by VideoJS or Firefox.
		$expected = <<<END
WEBVTT

6
00:12.002 --> 00:17.000
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
		// adapted from SRT input...
		$input = <<<END
6
00:00:12,002 --> 00:00:17
Harold, skaka inte bara på huvudet, <br>
du måste berätta vad som är fel med kaffet.
END;

		// @todo use &#10; instead of adding a space here;
		// but that's not supported yet by VideoJS or Firefox.
		$expected = <<<END
WEBVTT

6
00:12.002 --> 00:17.000
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
