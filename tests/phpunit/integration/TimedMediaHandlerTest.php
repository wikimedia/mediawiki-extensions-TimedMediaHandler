<?php
namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\FileRepo\File\File;
use MediaWiki\MainConfigNames;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWikiIntegrationTestCase;
use Wikimedia\TestingAccessWrapper;

class TimedMediaHandlerTest extends MediaWikiIntegrationTestCase {

	private TimedMediaHandler $handler;

	protected function setUp(): void {
		// Use an anonymous class for the abstract TimedMediaHandler
		$this->handler = new class extends TimedMediaHandler {
			public function hasVideo( $file ): bool {
				return false;
			}

			public function hasAudio( $file ): bool {
				return false;
			}

			public function getWebType( $file ): string {
				return '';
			}

			public function getAudioChannels( $file ): int {
				return 0;
			}

			public function getFramerate( $file ): float {
				return 0.0;
			}

			public function isInterlaced( $file ): bool {
				return false;
			}

			public function getStreamTypes( $file ): array {
				return [];
			}
		};
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

	/**
	 * @dataProvider providerGetSteppedThumbWidth
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::getSteppedThumbWidth
	 */
	public function testGetSteppedThumbWidth( int $requestWidth, int $srcWidth, int $expected ): void {
		// Subset of $wgThumbnailSteps in Wikimedia production
		$this->overrideConfigValue( MainConfigNames::ThumbnailSteps, [ 250, 330, 960, 1280 ] );
		$handler = TestingAccessWrapper::newFromObject( $this->handler );
		$file = $this->createMock( File::class );
		$this->assertSame(
			$expected,
			$handler->getSteppedThumbWidth( $file, $requestWidth, $srcWidth, (int)( $srcWidth * 9 / 16 ) )
		);
	}

	public static function providerGetSteppedThumbWidth() {
		return [
			'exact step' => [ 250, 1920, 250 ],
			'round up to next step' => [ 300, 1920, 330 ],
			'round down when next step exceeds source' => [ 1100, 1200, 960 ],
			'source larger than largest step' => [ 2000, 2000, 1280 ],
			'request between largest step and source' => [ 1500, 2000, 1280 ],
		];
	}

	/**
	 * @covers \MediaWiki\TimedMediaHandler\TimedMediaHandler::getSteppedThumbWidth
	 */
	public function testGetSteppedThumbWidthWithoutSteps(): void {
		$this->overrideConfigValue( MainConfigNames::ThumbnailSteps, [] );
		$handler = TestingAccessWrapper::newFromObject( $this->handler );
		$file = $this->createMock( File::class );
		$this->assertSame( 1500, $handler->getSteppedThumbWidth( $file, 1500, 2000, 1125 ) );
	}
}
