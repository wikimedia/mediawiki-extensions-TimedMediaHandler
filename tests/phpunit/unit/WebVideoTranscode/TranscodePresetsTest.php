<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\WebVideoTranscode;

use MediaWiki\Config\ConfigException;
use MediaWiki\Config\HashConfig;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePreset;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePresets
 */
class TranscodePresetsTest extends TestCase {

	public function testAll() {
		$all = TranscodePresets::allPresets();
		$this->assertIsArray( $all );
		$this->assertContainsOnlyInstancesOf( TranscodePreset::class, $all );
		$this->assertArrayHasKey( '360p.webm', $all );
		$this->assertArrayHasKey( 'ogg', $all );
	}

	public function testFindByKey() {
		$config = new HashConfig( [] );
		$presets = new TranscodePresets( $config );
		$preset = $presets->findByKey( '360p.webm' );
		$this->assertInstanceOf( TranscodePreset::class, $preset );
		$this->assertSame( 'video/webm; codecs="vp8, vorbis"', $preset->type );

		$this->assertNull( $presets->findByKey( 'nonexistent' ) );
	}

	/**
	 * @dataProvider provideEnabledTranscodes
	 */
	public function testEnabledTranscodes( $enabledSet, $enabledAudioSet, $expected ) {
		$config = new HashConfig( [
			'EnabledTranscodeSet' => $enabledSet,
			'EnabledAudioTranscodeSet' => $enabledAudioSet,
		] );

		$presets = new TranscodePresets( $config );
		$enabled = $presets->enabledTranscodes();
		$this->assertSame( $expected, $enabled );
	}

	public function testValidateTranscodeConfiguration() {
		$config = new HashConfig( [
			'EnabledTranscodeSet' => [ '360p.webm' => true ],
			'EnabledAudioTranscodeSet' => [ 'ogg' => true ],
		] );

		TranscodePresets::validateTranscodeConfiguration( $config );

		$configInvalid = new HashConfig( [
			'EnabledTranscodeSet' => [ 'invalid' => true ],
			'EnabledAudioTranscodeSet' => [],
		] );

		$this->expectException( ConfigException::class );
		TranscodePresets::validateTranscodeConfiguration( $configInvalid );
	}

	public static function provideEnabledTranscodes() {
		yield 'both empty' => [
			[],
			[],
			[]
		];
		yield 'video only' => [
			[ '360p.webm' => true, '720p.webm' => true ],
			[],
			[ '360p.webm', '720p.webm' ]
		];
		yield 'audio only' => [
			[],
			[ 'ogg' => true, 'mp3' => true ],
			[ 'mp3', 'ogg' ]
		];
		yield 'mixed and sorted' => [
			[ '720p.webm' => true, '360p.webm' => true ],
			[ 'ogg' => true ],
			[ '360p.webm', '720p.webm', 'ogg' ]
		];
		yield 'filtered' => [
			[ '360p.webm' => true, '720p.webm' => false ],
			[ 'ogg' => true ],
			[ '360p.webm', 'ogg' ]
		];
	}
}
