<?php

use MediaWiki\TimedMediaHandler\TimedMediaHandler;

class TimedMediaHandlerTest extends MediaWikiIntegrationTestCase {

	/** @var TimedMediaHandler */
	private $handler;

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
	public function testParseParamString( $str, $expected ) {
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
