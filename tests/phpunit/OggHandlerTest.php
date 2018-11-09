<?php

/**
 * @covers OggHandler
 */
class OggHandlerTest extends MediaWikiMediaTestCase {

	/** @var OggHandler */
	private $handler;

	public function getFilePath() {
		return __DIR__ . '/media';
	}

	protected function setUp() {
		parent::setUp();
		$this->handler = new OggHandler;
	}

	/**
	 * @dataProvider providerGetCommonMetaArray
	 * @param string $filename name of file
	 * @param array $expected
	 */
	public function testGetCommonMetaArray( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'application/ogg' );
		$this->assertEquals( $expected, $this->handler->getCommonMetaArray( $testFile ) );
	}

	public function providerGetCommonMetaArray() {
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
	 * @param string $filename name of file
	 * @param string $expected Mime type (including codecs)
	 */
	public function testGetWebType( $filename, $expected ) {
		$testFile = $this->dataFile( $filename, 'application/ogg' );
		$this->assertEquals( $expected, $this->handler->getWebType( $testFile ) );
	}

	public function providerGetWebType() {
		return [
			[ 'test5seconds.electricsheep.300x400.ogv', 'video/ogg; codecs="theora"' ],
			[ 'doubleTag.oga', 'audio/ogg; codecs="vorbis"' ],
			// XXX: This behaviour is somewhat questionable. It perhaps should be
			// application/ogg in this case.
			[ 'broken-file.ogg', 'audio/ogg' ],
		];
	}

}
