<?php
class TestOggHandler extends MediaWikiMediaTestCase {

	/** @var OggHandlerTMH */
	private $handler;

	function getFilePath() {
		return __DIR__ . '/media';
	}

	function setUp() {
		parent::setUp();
		$this->handler = new WebMHandler;
	}

	/**
	 * @dataProvider providerGetStreamTypes
	 * @param $filename String name of file
	 * @param $expected array List of codecs in file
	 */
	function testGetStreamTypes( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'video/webm' );
		$this->assertEquals( $expected, $this->handler->getStreamTypes( $testFile ) );
	}

	function providerGetStreamTypes() {
		return array(
			array( 'shuttle10seconds.1080x608.webm', array( 'VP8' ) ),
			array( 'VP9-tractor.webm', array( 'VP9' ) ),
			array( 'bear-vp9-opus.webm', array( 'Opus', 'VP9' ) )
		);
	}


	/**
	 * @dataProvider providerGetWebType
	 * @param $filename String name of file
	 * @param $expected String Mime type
	 */
	function testGetWebType( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'video/webm' );
		$this->assertEquals( $expected, $this->handler->getWebType( $testFile ) );
	}

	function providerGetWebType() {
		return array(
			array( 'shuttle10seconds.1080x608.webm', 'video/webm; codecs="vp8"' ),
			array( 'VP9-tractor.webm', 'video/webm; codecs="vp9"' ),
			array( 'bear-vp9-opus.webm', 'video/webm; codecs="opus, vp9"' )
		);
	}
}
