<?php

use MediaWiki\TimedMediaHandler\TimedMediaHandler;

abstract class TimedMediaHandlerTestCase extends MediaWikiMediaTestCase {

	/** @var TimedMediaHandler */
	protected $handler;

	public function getFilePath() {
		return __DIR__ . '/media';
	}

	protected function setUp(): void {
		parent::setUp();
		$this->handler = $this->getHandler();
	}

	abstract protected function getHandler(): TimedMediaHandler;

	abstract public static function providerSamples(): array;

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
	public function testGetWebType( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['webType'], $this->handler->getWebType( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testHasAudio( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['hasAudio'], $this->handler->hasAudio( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testHasVideo( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['hasVideo'], $this->handler->hasVideo( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetAudioChannels( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['audioChannels'], $this->handler->getAudioChannels( $testFile ) );
	}

	/**
	 * @dataProvider providerSamples
	 */
	public function testGetCommonMetaArray( string $filename, string $type, array $expected ) {
		$testFile = $this->dataFile( $filename, $type );
		$this->assertEquals( $expected['commonMeta'], $this->handler->getCommonMetaArray( $testFile ) );
	}

}
