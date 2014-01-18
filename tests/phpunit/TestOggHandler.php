<?php
class TestOggHandler extends MediaWikiMediaTestCase {

	/** @var OggHandlerTMH */
	private $handler;

	/** @var File */
	private $testFile;

	function getFilePath() {
		return __DIR__ . '/media';
	}

	function setUp() {
		parent::setUp();
		$this->handler = new OggHandlerTMH;
		$this->testFile = $this->dataFile( 'test5seconds.electricsheep.300x400.ogv', 'application/ogg' );
	}


	function testGetCommonMetaArray() {
		$expected = array(
			'Software' => array( 'Lavf53.21.1' ),
			'ObjectName' => array( 'Electric Sheep' ),
			'UserComment' => array( '🐑' )
		);
		$this->assertEquals( $expected, $this->handler->getCommonMetaArray( $this->testFile ) );
	}
}
