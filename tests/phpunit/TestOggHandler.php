<?php

class TestOggHandler extends MediaWikiMediaTestCase {

	/** @var OggHandlerTMH */
	private $handler;

	function getFilePath() {
		return __DIR__ . '/media';
	}

	function setUp() {
		parent::setUp();
		$this->handler = new OggHandlerTMH;
	}

	/**
	 * @dataProvider providerGetCommonMetaArray
	 * @param $filename String name of file
	 * @param $expected Array
	 */
	function testGetCommonMetaArray( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'application/ogg' );
		$this->assertEquals( $expected, $this->handler->getCommonMetaArray( $testFile ) );
	}

	function providerGetCommonMetaArray() {
		return [
			[ 'test5seconds.electricsheep.300x400.ogv',
				[
					'Software' => [ 'Lavf53.21.1' ],
					'ObjectName' => [ 'Electric Sheep' ],
					'UserComment' => [ '🐑' ]
				]
			],
			[ 'doubleTag.oga',
				[
					'Artist' => [ 'Brian', 'Bawolff' ],
					'Software' => [ 'Lavf55.10.2' ]
				]
			],
			[ 'broken-file.ogg',
				[]
			],
		];
	}

	/**
	 * @dataProvider providerGetWebType
	 * @param $filename String name of file
	 * @param $expected String Mime type (including codecs)
	 */
	function testGetWebType( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'application/ogg' );
		$this->assertEquals( $expected, $this->handler->getWebType( $testFile ) );
	}

	function providerGetWebType() {
		return [
			[ 'test5seconds.electricsheep.300x400.ogv', 'video/ogg; codecs="theora"' ],
			[ 'doubleTag.oga', 'audio/ogg; codecs="vorbis"' ],
			// XXX: This behaviour is somewhat questionable. It perhaps should be
			// application/ogg in this case.
			[ 'broken-file.ogg', 'audio/ogg' ],
		];
	}

}
