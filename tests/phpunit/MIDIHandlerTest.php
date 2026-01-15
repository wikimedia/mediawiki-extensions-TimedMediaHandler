<?php

use MediaWiki\TimedMediaHandler\Handlers\MIDIHandler\MIDIHandler;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * @covers \MediaWiki\TimedMediaHandler\Handlers\MIDIHandler\MIDIHandler
 */
class MIDIHandlerTest extends TimedMediaHandlerTestCase {

	protected function getHandler(): TimedMediaHandler {
		return new MIDIHandler;
	}

	public static function providerSamples(): array {
		return [
			[
				'chord.mid',
				'audio/midi',
				[
					'webType' => 'audio/midi',
					'streamTypes' => [ 'MIDI' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 1,
					'commonMeta' => false,
					'length' => 4.371875,
				]
			],
			[
				'c-major.midi',
				'audio/midi',
				[
					'webType' => 'audio/midi',
					'streamTypes' => [ 'MIDI' ],
					'hasVideo' => false,
					'hasAudio' => true,
					'audioChannels' => 2,
					'commonMeta' => false,
					'length' => 9.6,
				]
			],
		];
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetLength( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$length = $this->handler->getLength( $testFile );
		$this->assertEquals( $expected['length'], $length );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetWebType( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['webType'], $this->handler->getWebType( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetStreamTypes( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['streamTypes'], $this->handler->getStreamTypes( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetAudioChannels( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['audioChannels'], $this->handler->getAudioChannels( $testFile ) );
	}

}
