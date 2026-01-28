<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\WebVideoTranscode;

use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePreset;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePreset
 */
class TranscodePresetTest extends TestCase {

	public function testConstructor() {
		$settings = [
			'type' => 'video/webm',
			'unknownKey' => 'someValue',
		];

		$preset = new TranscodePreset( $settings );

		$this->assertSame( 'video/webm', $preset->type );
		$this->assertObjectNotHasProperty( 'unknownKey', $preset );
	}

	public function testAllProperties() {
		$settings = [
			'type' => 't',
			'maxSize' => 'ms',
			'videoBitrate' => 'vb',
			'minrate' => 'mr',
			'maxrate' => 'mar',
			'crf' => 'c',
			'speed' => 's',
			'twopass' => 'tp',
			'videoCodec' => 'vc',
			'audioCodec' => 'ac',
			'samplerate' => 'sr',
			'channels' => 'ch',
			'audioBitrate' => 'ab',
			'slices' => 'sl',
			'tileColumns' => 'tc',
			'novideo' => 'nv',
			'streaming' => 'st',
			'minChannels' => 'mc',
			'fpsmax' => 'fm',
			'noaudio' => 'na',
			'width' => 'w',
			'height' => 'h',
			'intraframe' => 'i',
			'remuxFrom' => [ 'f1', 'f2' ],
			'audioQuality' => 'aq',
			'noUpscaling' => 'nu',
			'aspect' => 'as',
			'framerate' => 'fr',
		];

		$preset = new TranscodePreset( $settings );

		foreach ( $settings as $key => $value ) {
			$this->assertSame( $value, $preset->$key );
		}
	}
}
