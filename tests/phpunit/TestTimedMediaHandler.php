<?php
class TestTimedMediaHandler extends MediaWikiTestCase {

	/** @var TimedMediaHandler */
	private $handler;

	function setUp() {
		$this->handler = new TimedMediaHandler;
		parent::setUp();
	}

	/**
	 * @dataProvider providerParseParamString
	 * @param $str String a thumbnail parameter string
	 * @param $expected Array Expected thumbnailing parameters
	 */
	function testParseParamString( $str, $expected ) {
		$result = $this->handler->parseParamString( $str );
		$this->assertEquals( $result, $expected );
	}

	function providerParseParamString() {
		return array(
			array(
				'mid',
				array(),
			),
			array(
				'220px-',
				array( 'width' => 220 ),
			),
			array(
				'seek=30',
				array( 'thumbtime' => 30.0 ),
			),
			array(
				'seek=15.72',
				array( 'thumbtime' => 15.72 ),
			),
			array(
				'180px-seek=15',
				array( 'thumbtime' => 15, 'width' => 180 ),
			),
		);

	}
}
