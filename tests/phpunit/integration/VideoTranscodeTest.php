<?php
namespace MediaWiki\TimedMediaHandler\Test\Integration;

use MediaWiki\Config\ConfigException;
use MediaWiki\Config\HashConfig;

/**
 * @author michael dale
 * @group medium
 * @group Database
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscodeJob
 */
class VideoTranscodeTest extends ApiVideoUploadTestCase {

	public static function transcodeSetProvider() {
		return [
			[ [ '360p.vp9.webm' => true ], [], false ],
			[ [ 'foobar' => true ], [], true ],
			[ [], [ 'foobar' => true ], true ],
		];
	}

	/**
	 * @dataProvider transcodeSetProvider
	 */
	public function testEnabledTranscodeSetConfiguration( $set, $audioSet, $exception ) {
		$config = new HashConfig( [
			'EnabledTranscodeSet' => $set,
			'EnabledAudioTranscodeSet' => $audioSet,
		] );
		if ( $exception ) {
			$this->expectException( ConfigException::class );
		}
		$this->getServiceContainer()
			->getService( 'TimedMediaHandler.TranscodePresets' )
			->validateTranscodeConfiguration( $config );
		// Silence testcase when everything is ok
		$this->assertTrue( true );
	}
}
