<?php

use MediaWiki\TimedMediaHandler\TimedMediaTransformOutput;
use Wikimedia\TestingAccessWrapper;

/**
 * @covers \MediaWiki\TimedMediaHandler\TimedMediaTransformOutput
 */
class TimedMediaTransformOutputTest extends MediaWikiMediaTestCase {

	public function getFilePath() {
		return __DIR__ . '/media';
	}

	protected function setUp(): void {
		parent::setUp();

		// Disable video thumbnail generation. Not needed for this test.
		$repo = TestingAccessWrapper::newFromObject( $this->repo );
		$repo->transformVia404 = true;

		$this->setMWGlobals( [
			'wgMinimumVideoPlayerSize' => 400,
			'wgUseInstantCommons' => false,
			'wgForeignFileRepos' => []
		] );
	}

	/**
	 * @param int $width The requested width of the thumbnail
	 * @param int $minVideoSize The min width a non-pop up video is acceptable
	 * @param bool $expectPopup Do we expect a pop up video
	 *
	 * @dataProvider providerIsPopUp
	 */
	public function testIsPopUp( $width, $minVideoSize, $expectPopup ) {
		$this->setMwGlobals( 'wgMinimumVideoPlayerSize', $minVideoSize );

		// Note this file has a width of 400px and a height of 300px
		$file = $this->dataFile( 'test5seconds.electricsheep.300x400.ogv', 'application/ogg' );
		$thumbnail = TestingAccessWrapper::newFromObject( $file->transform( [ 'width' => $width ] ) );
		$this->assertTrue( $thumbnail && !$thumbnail->isError() );
		$this->assertEquals( $thumbnail->useImagePopUp(), $expectPopup );
	}

	public static function providerIsPopUp() {
		return [
			[ 400, 800, false ],
			[ 300, 800, true ],
			[ 300, 200, false ],
			[ 300, 300, false ]
		];
	}

	/**
	 * @param int $thumbWidth Requested width
	 * @param array $sources
	 * @param array $sortedSources
	 * @dataProvider providerSortMediaByBandwidth
	 */
	public function testSortMediaByBandwidth( $thumbWidth, $sources, $sortedSources ) {
		$params = [
			'width' => $thumbWidth,
			'height' => $thumbWidth * 9 / 16,
			'isVideo' => true,
			'fillwindow' => false,
			'file' => new FakeDimensionFile( [ 1820, 1024 ] )
		];
		$thumbObj = TestingAccessWrapper::newFromObject( new TimedMediaTransformOutput( $params ) );

		usort( $sources, [ $thumbObj, 'sortMediaByBandwidth' ] );
		$this->assertEquals( $sortedSources, $sources );
	}

	public static function providerSortMediaByBandwidth() {
		return [
			[
				600,
				[
					[ 'width' => 1000, 'bandwidth' => 2000 ],
					[ 'width' => 1000, 'bandwidth' => 7000 ],
					[ 'width' => 1000, 'bandwidth' => 1000 ],
				],
				[
					[ 'width' => 1000, 'bandwidth' => 1000 ],
					[ 'width' => 1000, 'bandwidth' => 2000 ],
					[ 'width' => 1000, 'bandwidth' => 7000 ],
				],
			],
			[
				600,
				[
					[ 'width' => 200, 'bandwidth' => 2000 ],
					[ 'width' => 1000, 'bandwidth' => 7000 ],
					[ 'width' => 200, 'bandwidth' => 1000 ],
				],
				[
					[ 'width' => 1000, 'bandwidth' => 7000 ],
					[ 'width' => 200, 'bandwidth' => 1000 ],
					[ 'width' => 200, 'bandwidth' => 2000 ],
				],
			],
			[
				/* Pop up viewer in this case */
				100,
				[
					[ 'width' => 700, 'bandwidth' => 2000 ],
					[ 'width' => 1000, 'bandwidth' => 7000 ],
					[ 'width' => 700, 'bandwidth' => 1000 ],
				],
				[
					[ 'width' => 1000, 'bandwidth' => 7000 ],
					[ 'width' => 700, 'bandwidth' => 1000 ],
					[ 'width' => 700, 'bandwidth' => 2000 ],
				],
			],
			[
				600,
				[
					[ 'width' => 700, 'bandwidth' => 2000 ],
					[ 'width' => 800, 'bandwidth' => 7000 ],
					[ 'width' => 1000, 'bandwidth' => 1000 ],
				],
				[
					[ 'width' => 1000, 'bandwidth' => 1000 ],
					[ 'width' => 700, 'bandwidth' => 2000 ],
					[ 'width' => 800, 'bandwidth' => 7000 ],
				],
			],
		];
	}
}
