<?php
namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWikiIntegrationTestCase;

class TimedMediaHandlerTest extends MediaWikiIntegrationTestCase {

	private TimedMediaHandler $handler;

	protected function setUp(): void {
		$this->handler = new TimedMediaHandler;
		parent::setUp();
	}

	/**
	 * @dataProvider providerParseParamString
	 * @param string $str a thumbnail parameter string
	 * @param array $expected Expected thumbnailing parameters
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::parseParamString
	 */
	public function testParseParamString( string $str, array $expected ): void {
		$result = $this->handler->parseParamString( $str );
		$this->assertEquals( $expected, $result );
	}

	public static function providerParseParamString() {
		return [
			[
				'mid',
				[],
			],
			[
				'220px-',
				[ 'width' => 220 ],
			],
			[
				'seek=30',
				[ 'thumbtime' => 30.0 ],
			],
			[
				'seek=15.72',
				[ 'thumbtime' => 15.72 ],
			],
			[
				'180px-seek=15',
				[ 'thumbtime' => 15, 'width' => 180 ],
			],
		];
	}
}
