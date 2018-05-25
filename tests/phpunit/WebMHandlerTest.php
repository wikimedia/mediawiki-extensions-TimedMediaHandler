<?php

class WebMHandlerTest extends MediaWikiMediaTestCase {

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
	 * @param string $filename name of file
	 * @param array $expected List of codecs in file
	 */
	function testGetStreamTypes( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'video/webm' );
		$this->assertEquals( $expected, $this->handler->getStreamTypes( $testFile ) );
	}

	function providerGetStreamTypes() {
		return [
			[ 'shuttle10seconds.1080x608.webm', [ 'VP8' ] ],
			[ 'VP9-tractor.webm', [ 'VP9' ] ],
			[ 'bear-vp9-opus.webm', [ 'VP9', 'Opus' ] ]
		];
	}

	/**
	 * @dataProvider providerGetWebType
	 * @param string $filename name of file
	 * @param string $expected Mime type
	 */
	function testGetWebType( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'video/webm' );
		$this->assertEquals( $expected, $this->handler->getWebType( $testFile ) );
	}

	function providerGetWebType() {
		return [
			[ 'shuttle10seconds.1080x608.webm', 'video/webm; codecs="vp8"' ],
			[ 'VP9-tractor.webm', 'video/webm; codecs="vp9"' ],
			[ 'bear-vp9-opus.webm', 'video/webm; codecs="vp9, opus"' ]
		];
	}
}
