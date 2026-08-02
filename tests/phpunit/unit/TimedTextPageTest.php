<?php

declare( strict_types=1 );

namespace MediaWiki\TimedMediaHandler\Test\Unit;

use MediaWiki\Language\MessageLocalizer;
use MediaWiki\TimedMediaHandler\TimedText\ParseError;
use MediaWiki\TimedMediaHandler\TimedTextPage;
use MediaWikiUnitTestCase;

/**
 * @covers \MediaWiki\TimedMediaHandler\TimedTextPage::buildValidationErrorList
 * @group TimedMediaHandler
 */
class TimedTextPageTest extends MediaWikiUnitTestCase {
	private function makeContext(): MessageLocalizer {
		$itemMsg = $this->getMockMessage( 'ITEM' );
		$moreMsg = $this->getMockMessage( 'MORE' );

		$context = $this->createMock( MessageLocalizer::class );
		$context->method( 'msg' )->willReturnCallback(
			static fn ( string $key ) => $key === 'timedmedia-subtitle-validation-more' ? $moreMsg : $itemMsg
		);
		return $context;
	}

	/**
	 * @param int $n
	 * @return ParseError[]
	 */
	private function makeErrors( int $n ): array {
		$errors = [];
		for ( $i = 1; $i <= $n; $i++ ) {
			$errors[] = new ParseError( $i, '', "error $i" );
		}
		return $errors;
	}

	public static function provideErrorCounts(): array {
		// [ error count, shown items, "N more" line expected? ]
		// $max is 10 inside buildValidationErrorList, and showing 11 instead
		// of 10 + "1 more" is a deliberate special case: a line that only
		// says "1 more" isn't worth it when you could just show the thing.
		return [
			'none' => [ 0, 0, false ],
			'under the cap' => [ 4, 4, false ],
			'exactly at the cap' => [ 10, 10, false ],
			'one over the cap, shown in full instead of "1 more"' => [ 11, 11, false ],
			'two over the cap, truncated' => [ 12, 10, true ],
			'well over the cap, truncated' => [ 20, 10, true ],
		];
	}

	/**
	 * @dataProvider provideErrorCounts
	 */
	public function testBuildValidationErrorListTruncation(
		int $errorCount, int $shownItems, bool $expectMoreLine
	): void {
		$html = TimedTextPage::buildValidationErrorList( $this->makeContext(), $this->makeErrors( $errorCount ) );

		$expectedLiCount = $shownItems + ( $expectMoreLine ? 1 : 0 );
		$this->assertSame( $expectedLiCount, substr_count( $html, '<li>' ),
			"unexpected total <li> count for $errorCount errors" );
		$this->assertSame( $shownItems, substr_count( $html, 'ITEM' ) );
		$this->assertSame( $expectMoreLine, str_contains( $html, 'MORE' ) );
	}

	public function testBuildValidationErrorListEmpty(): void {
		$this->assertSame( '', TimedTextPage::buildValidationErrorList( $this->makeContext(), [] ) );
	}
}
