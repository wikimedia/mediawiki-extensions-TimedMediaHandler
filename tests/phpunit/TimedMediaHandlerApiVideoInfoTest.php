<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * TimedMediaHandlerApiVideoInfoTest test case.
 * 
 * @ingroup timedmedia
 * @since 0.2
 * @author Michael Dale
 */
class TimedMediaHandlerApiVideoInfoTest extends ApiTestSetup {

	public function setUp() {
		parent::setUp();

		wfSetupSession();

		ini_set( 'log_errors', 1 );
		ini_set( 'error_reporting', 1 );
		ini_set( 'display_errors', 1 );

	}
}