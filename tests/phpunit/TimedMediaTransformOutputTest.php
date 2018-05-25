<?php

class TimedMediaTransformOutputTest extends MediaWikiMediaTestCase {

	private $sortMethod;
	private $thumbObj;

	function getFilePath() {
		return __DIR__ . '/media';
	}

	protected function setUp() {
		parent::setUp();

		// Disable video thumbnail generation. Not needed for this test.
		$reflection = new ReflectionClass( $this->repo );
		$reflectionProperty = $reflection->getProperty( 'transformVia404' );
		$reflectionProperty->setAccessible( true );
		$reflectionProperty->setValue( $this->repo, true );

		$this->setMWGlobals( 'wgMinimumVideoPlayerSize', '400' );
	}

	/**
	 * @param int $width The requested width of the thumbnail
	 * @param int $minVideoSize The min width a non-pop up video is acceptable
	 * @param bool $expectPopup Do we expect a pop up video
	 *
	 * @dataProvider providerIsPopUp
	 */
	function testIsPopUp( $width, $minVideoSize, $expectPopup ) {
		$this->setMwGlobals( 'wgMinimumVideoPlayerSize', $minVideoSize );

		// Note this file has a width of 400px and a height of 300px
		$file = $this->dataFile( 'test5seconds.electricsheep.300x400.ogv', 'application/ogg' );
		$thumbnail = $file->transform( [ 'width' => $width ] );
		$this->assertTrue( $thumbnail && !$thumbnail->isError() );

		$reflection = new ReflectionClass( $thumbnail );
		$reflMethod = $reflection->getMethod( 'useImagePopUp' );
		$reflMethod->setAccessible( true );

		$actual = $reflMethod->invoke( $thumbnail );
		$this->assertEquals( $actual, $expectPopup );
	}

	function providerIsPopUp() {
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
	function testSortMediaByBandwidth( $thumbWidth, $sources, $sortedSources ) {
		$params = [
			'width' => $thumbWidth,
			'height' => $thumbWidth * 9 / 16,
			'isVideo' => true,
			'fillwindow' => false,
			'file' => new FakeDimensionFile( [ 1820, 1024 ] )
		];
		$this->thumbObj = new TimedMediaTransformOutput( $params );

		$reflection = new ReflectionClass( $this->thumbObj );
		$this->sortMethod = $reflection->getMethod( 'sortMediaByBandwidth' );
		$this->sortMethod->setAccessible( true );

		usort( $sources, [ $this, 'callSortMethodHelper' ] );
		$this->assertEquals( $sortedSources, $sources );
	}

	public function callSortMethodHelper( $a, $b ) {
		return $this->sortMethod->invoke( $this->thumbObj, $a, $b );
	}

	function providerSortMediaByBandwidth() {
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
