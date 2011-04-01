<?php 
/**
 * TestTimeParsing test .
 * 
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */
// xxx need to get testing autoloader stuff figured out
require_once( "$IP/includes/media/Generic.php" );
require_once( dirname( __FILE__ ) . '/../../TimedMediaHandler_body.php' );


class TestTimeParsing extends PHPUnit_Framework_TestCase {
	 /**
     * Test time string to np2
     */
    function testSeconds2NptFormat() {
    	
    	// Some time conversions:
    	$this->assertEquals( TimedMediaHandler::seconds2npt( 100 ), '0:1:40' );
    	$this->assertEquals( TimedMediaHandler::seconds2npt( 0 ), '0:0:0' );
    	$this->assertEquals( TimedMediaHandler::seconds2npt( 3601 ), '1:0:1' );
    	
    	// Test failures:
    	$this->assertEquals( TimedMediaHandler::seconds2npt( 'foo' ), false );
    	$this->assertEquals( TimedMediaHandler::seconds2npt( -1 ), false );
    }
}