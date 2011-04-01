<?php
// Guess the MW_INSTALL_PATH
$IP = ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) 
	: dirname( __FILE__ ) . '/../../../../' );

	
require_once 'PHPUnit/Framework/TestSuite.php';

// ALl the test files: 
require_once( dirname( __FILE__ )  . '/TestTimeParsing.php' );

/**
 * TimedMediaHandler test suite.
 * 
 * @ingroup timedmedia
 * @since 0.6.5
 * @author Michael Dale
 */

class TimedMediaHandlerTestSuite {

    public static function main() {
        PHPUnit_TextUI_TestRunner::run( self::suite() );
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite( 'timedmedia' );
        
		$suite->addTestSuite( 'testTimeParsing' );
		
        return $suite;
    }
}

TimedMediaHandlerTestSuite::main();

?>