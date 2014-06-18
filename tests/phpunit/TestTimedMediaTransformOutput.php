<?php
class TestTimedMediaTransformOutput extends MediaWikiMediaTestCase {

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
	}

	/**
	 * @param $width int The requested width of the thumbnail
	 * @param $minVideoSize int The min width a non-pop up video is acceptable
	 * @param $expectPopup boolean Do we expect a pop up video
	 *
	 * @dataProvider providerIsPopUp
	 */
	function testIsPopUp( $width, $minVideoSize, $expectPopup ) {
		$this->setMwGlobals( 'wgMinimumVideoPlayerSize', $minVideoSize );

		// Note this file has a width of 400px and a height of 300px
		$file = $this->dataFile( 'test5seconds.electricsheep.300x400.ogv', 'application/ogg' );
		$thumbnail = $file->transform( array( 'width' => $width ) );
		$this->assertTrue( $thumbnail && !$thumbnail->isError() );

		$reflection = new ReflectionClass( $thumbnail );
		$reflMethod = $reflection->getMethod( 'useImagePopUp' );
		$reflMethod->setAccessible( true );

		$actual = $reflMethod->invoke( $thumbnail );
		$this->assertEquals( $actual, $expectPopup );

	}

	function providerIsPopUp() {
		return array(
			array( 400, 800, false ),
			array( 300, 800, true ),
			array( 300, 200, false ),
			array( 300, 300, false )
		);
	}
}
