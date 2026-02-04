<?php
/**
 * TestTimeParsing test .
 *
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */

use MediaWiki\TimedMediaHandler\TimedMediaHandler;

class TimeParsingTest extends PHPUnit\Framework\TestCase {
	/**
	 * Test time string to np2
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::seconds2npt
	 */
	public function testSeconds2NptFormat() {
		// Some time conversions:
		$this->assertSame( '00:01:40', TimedMediaHandler::seconds2npt( 100 ) );
		$this->assertSame( '00:00:00', TimedMediaHandler::seconds2npt( 0 ) );
		$this->assertSame( '01:00:01', TimedMediaHandler::seconds2npt( 3601 ) );
		$this->assertSame( '01:00:01.500', TimedMediaHandler::seconds2npt( 3601.5 ) );
		$this->assertSame( '01:00:01.050', TimedMediaHandler::seconds2npt( 3601.05 ) );

		// Test failures:
		$this->assertFalse( TimedMediaHandler::seconds2npt( 'foo' ) );
		$this->assertFalse( TimedMediaHandler::seconds2npt( -1 ) );
	}

	/**
	 * Test time parsing to seconds
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::parseTimeString
	 */
	public function testParseTimeString() {
		// Some time conversions:
		$this->assertSame( 100.0, TimedMediaHandler::parseTimeString( 100 ) );
		$this->assertSame( 100.5, TimedMediaHandler::parseTimeString( 100.5 ) );
		$this->assertSame( 60.0, TimedMediaHandler::parseTimeString( '01:00' ) );
		$this->assertSame( 3600.0, TimedMediaHandler::parseTimeString( '1:0:0' ) );
		$this->assertSame( 3600.0, TimedMediaHandler::parseTimeString( '01:00:00' ) );
		$this->assertSame( 3600.032, TimedMediaHandler::parseTimeString( '01:00:00.032' ) );
		$this->assertSame( 0, TimedMediaHandler::parseTimeString( -1 ) );
		// Test longer than duration check ( should return time -1 )
		$this->assertSame( 8, TimedMediaHandler::parseTimeString( 10, 9 ) );

		// Test failures:
		$this->assertFalse( TimedMediaHandler::parseTimeString( '1:1:1:1' ) );
		$this->assertFalse( TimedMediaHandler::parseTimeString( 'abc' ) );
	}
}
