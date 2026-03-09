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
			'segmentDuration' => 'sd',
			'videoRenditions' => [
				[
					'maxSize' => 'vrms',
					'videoBitrate' => 'vrvb',
					'maxrate' => 'vrmr',
					'crf' => 'vrc',
					'speed' => 'vrs',
				],
			]
		];

		$preset = new TranscodePreset( $settings );

		foreach ( $settings as $key => $value ) {
			if ( $key == 'videoRenditions' ) {
				foreach ( $value as $videoRendition ) {
					$vrOptions = new TranscodePreset( $videoRendition );
					foreach ( $vrOptions as $vrKey => $vrValue ) {
						$this->assertSame( $vrValue, $vrOptions->$vrKey );
					}
				}
			} else {
				$this->assertSame( $value, $preset->$key );
			}
		}
	}

	public function testGetMaxSizeNoRenditions() {
		$settings = [
			'maxSize' => '200x100',
		];

		$preset = new TranscodePreset( $settings );
		$this->assertEquals( '200x100', $preset->getMaxSize() );
	}

	public function testGetMaxSizeWithRenditions() {
		$settings = [
			'videoRenditions' => [
				[ 'maxSize' => '200x100' ],
				[ 'maxSize' => '400x200' ],
			]
		];

		$preset = new TranscodePreset( $settings );
		$this->assertEquals( '400x200', $preset->getMaxSize() );
	}
}
