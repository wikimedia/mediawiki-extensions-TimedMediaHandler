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

		if ( $result !== false ) {
			// ensure the values from parseParamString are taken as valid by validateParam
			foreach ( $result as $param => $value ) {
				$this->assertTrue(
					$this->handler->validateParam( $param, $value ),
					'parseParamString/validateParam roundtrip'
				);
			}
		}
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

	/**
	 * @dataProvider providerValidateParam
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::validateParam
	 */
	public function testValidateParam( string $param, mixed $value, bool $expected ): void {
		$result = $this->handler->validateParam( $param, $value );
		$this->assertSame( $expected, $result );
	}

	public static function providerValidateParam() {
		return [
			[
				'width',
				220,
				true,
			],
			[
				'width',
				'220',
				true,
			],
			[
				'width',
				'abc',
				false,
			],
			[
				'thumbtime',
				'15.72',
				true,
			],
			[
				'thumbtime',
				'abc',
				false,
			],
			[
				'disablecontrols',
				[],
				false,
			],
			[
				'disablecontrols',
				'invalid,options',
				false,
			],
			[
				'disablecontrols',
				'options',
				true,
			],
		];
	}
}
