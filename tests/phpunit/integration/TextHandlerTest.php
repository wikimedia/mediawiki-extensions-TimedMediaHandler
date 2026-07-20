<?php

namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;
use Wikimedia\Rdbms\FakeResultWrapper;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\TextHandler\TextHandler
 */
class TextHandlerTest extends MediaWikiIntegrationTestCase {

	private function newTextHandler(): TextHandler {
		$file = $this->createMock( File::class );
		$file->method( 'getTitle' )->willReturn( Title::makeTitle( NS_FILE, 'Example.webm' ) );
		$file->method( 'isLocal' )->willReturn( true );
		$file->method( 'getRepo' )->willReturn(
			$this->getServiceContainer()->getRepoGroup()->getLocalRepo() );
		return new TextHandler( $file );
	}

	private function makeRows( string ...$titles ): FakeResultWrapper {
		return new FakeResultWrapper(
			array_map( static fn ( $t ) => (object)[ 'page_title' => $t ], $titles )
		);
	}

	public function testGetTracksDedupesToOneVttTrackPerLanguage() {
		$rows = $this->makeRows(
			'Example.webm.en.srt',
			'Example.webm.en.vtt',
			'Example.webm.fr.srt',
			// Unrecognized subtitle format, must be ignored.
			'Example.webm.de.txt'
		);

		$tracks = $this->newTextHandler()->getTextTracksFromRows( $rows );

		$this->assertSame( [ 'en', 'fr' ], array_column( $tracks, 'srclang' ) );
		foreach ( $tracks as $track ) {
			$this->assertSame( 'text/vtt', $track['type'] );
			$this->assertStringContainsString( 'trackformat=vtt', $track['src'] );
		}
	}

	public function testGetTracksAllSourcesListsEveryPageInItsFormat() {
		$rows = $this->makeRows( 'Example.webm.en.srt', 'Example.webm.en.vtt' );

		$tracks = $this->newTextHandler()->getTextTracksFromRows( $rows, true );

		$types = array_column( $tracks, 'type' );
		sort( $types );
		$this->assertSame( [ 'text/vtt', 'text/x-srt' ], $types );
		$this->assertSame( [ 'en', 'en' ], array_column( $tracks, 'srclang' ) );
	}

	public function testVttToVttPreservesRegionsStylesNotesAndSettings() {
		$input = "WEBVTT\n\n"
			. "REGION\nid:bottom\nwidth:40%\nlines:3\n\n"
			. "STYLE\n::cue { color: yellow }\n\n"
			. "NOTE an authoring comment\n\n"
			. "intro\n"
			. "00:00:01.000 --> 00:00:04.000 align:start region:bottom\n"
			. "<v Bob>Hello <i>world</i></v>\n";

		$output = TextHandler::convertSubtitles( 'vtt', 'vtt', $input );

		// The round-trip must retain the parts our TimedText DOM does not model.
		$this->assertStringContainsString( 'REGION', $output );
		$this->assertStringContainsString( 'id:bottom', $output );
		$this->assertStringContainsString( 'STYLE', $output );
		$this->assertStringContainsString( '::cue { color: yellow }', $output );
		$this->assertStringContainsString( 'NOTE an authoring comment', $output );
		$this->assertStringContainsString( 'region:bottom', $output );
		$this->assertStringContainsString( 'align:start', $output );
		$this->assertStringContainsString( '<v Bob>Hello <i>world</i></v>', $output );
	}

	public function testVttToSrtDropsMarkupAndNumbersSequentially() {
		$input = "WEBVTT\n\n"
			. "00:00:01.000 --> 00:00:02.000\n<v Bob>first</v>\n\n"
			. "00:00:03.000 --> 00:00:04.000\n<i>second</i>\n";

		$output = TextHandler::convertSubtitles( 'vtt', 'srt', $input );

		$expected = "1\n00:00:01,000 --> 00:00:02,000\nfirst\n\n"
			. "2\n00:00:03,000 --> 00:00:04,000\n<i>second</i>";
		$this->assertSame( $expected, $output );
	}

	public function testSrtToVtt() {
		$input = "1\n00:00:01,000 --> 00:00:02,000\nHello\n";

		$output = TextHandler::convertSubtitles( 'srt', 'vtt', $input );

		$this->assertStringStartsWith( "WEBVTT", $output );
		$this->assertStringContainsString( "00:00:01.000 --> 00:00:02.000", $output );
		$this->assertStringContainsString( "Hello", $output );
	}
}
