<?php
/**
 * SrtReader test.
 *
 * @ingroup timedmedia
 */

use MediaWiki\TimedMediaHandler\TimedText\SrtReader;
use MediaWiki\TimedMediaHandler\TimedText\DOM;

/**
 * @covers SrtReader::read
 */
class SrtReaderTest extends PHPUnit\Framework\TestCase {
	public function setUp() {
		parent::setUp();
		$this->reader = new SrtReader;
	}

	public function testSingleParse() {
		// from https://en.wikipedia.org/wiki/SubRip
		$input = <<<END
168
00:20:41,150 --> 00:20:45,109
- How did he do that?
- Made him an offer he couldn't refuse.
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );

		$cue = $cues[0];
		$this->assertEquals( 168, $cue->id );
		$this->assertEquals( 1241.15, $cue->start );
		$this->assertEquals( 1245.109, $cue->end );
		$this->assertEquals( "- How did he do that?\n" .
			"- Made him an offer he couldn't refuse.",
			$this->flatten( $cue ) );
	}

	public function testMultiItems() {
		// from Commons' TimedText:Folgers.ogv.en.srt
		$input = <<<END
0
00:00:01,707 --> 00:00:04,154
Harold is the coffee alright?

1
00:00:04,169 --> 00:00:05,185
uh-uhmmm

2
00:00:05,005 --> 00:00:07,785
You mean it's as bad as yesterday?

3
00:00:07,785 --> 00:00:08,962
uh-huh
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 4, count( $cues ) );

		$this->assertEquals( 1, $cues[1]->id );
		$this->assertEquals( 3, $cues[3]->id );
		$this->assertEquals( 'uh-huh', $this->flatten( $cues[3] ) );
	}

	public function testTimeWithNoMillis() {
		// from Commons' TimedText:Folgers.ogv.en.srt
		$input = <<<END
4
00:00:09 --> 00:00:10,169
No improvement at all?
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );

		$this->assertEquals( 9.0, $cues[0]->start );
		$this->assertEquals( 10.169, $cues[0]->end );
	}

	public function testTimeWithPeriodSeparator() {
		// from Commons' TimedText:1946-02-21 New Airliner.ogv.fr.srt
		$input = <<<END
19
00:01:26,400 --> 00:01:27.800
Et maintenant, au revoir!
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );

		$this->assertEquals( 86.4, $cues[0]->start );
		$this->assertEquals( 87.8, $cues[0]->end );
	}

	public function testTimeWithNonPaddedMillis() {
		// from Commons' TimedText:中文維基百科教學頻道第四章_第二部分.ogv.zh-hant.srt
		$input = <<<END
2
00:00:31,200 --> 00:00:46,40
你上傳的檔案將直接上傳至"維基共享" ( Wikimedia Commons)
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );

		$this->assertEquals( 31.200, $cues[0]->start );
		$this->assertEquals( 46.040, $cues[0]->end );
	}

	public function testStartsWithBom() {
		// from Commons' TimedText:Welcome to globallives 2.0.ogv.fr.srt
		$input = <<<END
﻿1
00:00:00,000 --> 00:00:04,000
♫ Hip-hop brésilien en arrière plan ♫
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );
		$this->assertEquals( 1, $cues[0]->id );
	}

	public function testSquishedOnOneLine() {
		// from Commons' TimedText:WalesAnniversaryAddress.ogv.gl.srt
		$input = <<<END
		1 00:00:07,000 --> 00:00:10,500 Ola a todos e benvidos ó décimo aniversario da Wikipedia.
END;

		$this->reader->read( $input );
		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );
		$this->assertEquals( 1, $cues[0]->id );
		$this->assertEquals( 7, $cues[0]->start );
		$this->assertEquals( 10.5, $cues[0]->end );
		$this->assertEquals(
			'Ola a todos e benvidos ó décimo aniversario da Wikipedia.',
			$this->flatten( $cues[0] ) );
	}

	/*
	public function testStrayEmSpace() {
		// from Commons' TimedText:Wolff-parkinson-white syndrome video.webm.ru.srt
		$input = " \n" .
			"13\n" .
			"0:01:04,920 --> 0:01:09,860\n" .
			"который останавливает импульс, чтобы убедиться, " .
			"что все в порядке, прежде чем импульс пройдет через него, ";

		$this->reader->read( $input );

		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );
		$this->assertEquals( 13, $cues[0]->id );
		$this->assertEquals( 64.92, $cues[0]->start );
		$this->assertEquals( 69.86, $cues[0]->end );
		$this->assertEquals(
			'который останавливает импульс, чтобы убедиться, ' .
			'что все в порядке, прежде чем импульс пройдет через него,',
			$this->flatten( $cues[0] ) );
	}
	*/

	public function testBadFormatInTimes() {
		// from Commons' TimedText:Soy Wikipediasta.webm.pt-br.srt
		$input = <<<END
32
00: 01: 25,700 -> 00: 01: 27,720
Nós doamos nosso tempo para esta causa,
END;

		$this->reader->read( $input );

		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );
		$this->assertEquals( 32, $cues[0]->id );
		$this->assertEquals( 85.7, $cues[0]->start );
		$this->assertEquals( 87.720, $cues[0]->end );
		$this->assertEquals(
			'Nós doamos nosso tempo para esta causa,',
			$this->flatten( $cues[0] ) );
	}

	public function testItalic() {
		// from 1946-01-31_Radar_makes_Round_Trip_To_Moon.ogv.en.srt
		$input = <<<END
8
00:00:33,041 --> 00:00:35,689
The radar antenna is pointed directly at <i>Luna</i>

15
00:01:15,580 --> 00:01:18,715
<i>DeWitt:</i> Calculations showed that radar equipment could be
END;
		$this->reader->read( $input );

		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 2, count( $cues ) );

		$this->assertEquals( '8', $cues[0]->id );
		$this->assertEquals( 33.041, $cues[0]->start );
		$this->assertEquals( 35.689, $cues[0]->end );
		$this->assertEquals(
			'The radar antenna is pointed directly at Luna',
			$this->flatten( $cues[0] )
		);

		$italic = null;
		foreach ( $cues[0]->nodes as $node ) {
			if ( $node instanceof DOM\ItalicNode ) {
				$italic = $node;
				break;
			}
		}
		$this->assertTrue( $italic !== null );
		$this->assertEquals(
			'Luna',
			$this->flattenNode( $italic )
		);

		$this->assertEquals( '15', $cues[1]->id );
		$this->assertEquals(
			'DeWitt: Calculations showed that radar equipment could be',
			$this->flatten( $cues[1] )
		);

		$italic = null;
		foreach ( $cues[1]->nodes as $node ) {
			if ( $node instanceof DOM\ItalicNode ) {
				$italic = $node;
				break;
			}
		}
		$this->assertTrue( $italic !== null );
		$this->assertEquals(
			'DeWitt:',
			$this->flattenNode( $italic )
		);
	}

	public function testHtmlEntity() {
		// TimedText:1962-07-12_A_Day_in_History.webm.de.srt
		$input = <<<END
16
00:00:55,101 --> 00:00:59,145
Ein NASA-Team wickelt den Start für AT&amp;T ab.
END;

		$this->reader->read( $input );

		$cues = $this->reader->getCues();
		$errors = $this->reader->getErrors();
		$this->assertEmpty( $errors );

		$this->assertEquals( 1, count( $cues ) );

		$this->assertEquals( '16', $cues[0]->id );
		$this->assertEquals(
			'Ein NASA-Team wickelt den Start für AT&T ab.',
			$this->flatten( $cues[0] )
		);
	}

	protected function flatten( DOM\Cue $cue ) {
		return $this->flattenNodes( $cue->nodes );
	}

	protected function flattenNodes( $nodes ) {
		return implode( "", array_map( [ $this, 'flattenNode' ], $nodes ) );
	}

	protected function flattenNode( DOM\Node $node ) {
		if ( $node instanceof DOM\TextNode ) {
			return $node->text;
		} elseif ( $node instanceof DOM\InternalNode ) {
			return $this->flattenNodes( $node->nodes );
		} else {
			return '';
		}
	}
}
