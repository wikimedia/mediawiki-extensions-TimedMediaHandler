<?php

require_once 'PHPUnit/Framework/TestSuite.php';

// Guess the MW_INSTALL_PATH
$IP = ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) 
	: dirname( __FILE__ ) . '/../../../../' );

// Include the phpunit test system:
require( "$IP/tests/phpunit/phpunit.php" );

// Add needed tests files ( why don't we have test files in autoloader when running the test suite? ) 
require( "$IP/tests/phpunit/MediaWikiTestCase.php" );
require( "$IP/tests/phpunit/includes/api/ApiSetup.php" );
require( "$IP/tests/phpunit/includes/api/ApiUploadTest.php" );
require( dirname( __FILE__ ) . '/TimedMediaHandlerApiUploadVideoTest.php' );
require( dirname( __FILE__ ) . '/TimedMediaHandlerApiVideoInfoTest.php' );

/**
 * Static test suite.
 * 
 * @ingroup timedmedia
 * @since 0.6.5
 * @author Michael Dale
 */
class TimedMediaHandlerTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'TimedMediaHandlerTestSuite' );

		$this->addTestSuite ( 'TimedMediaHandlerApiUploadVideoTest' );
		
		//$this->addTestSuite ( 'TimedMediaHandlerApiVideoInfoTest' );	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

