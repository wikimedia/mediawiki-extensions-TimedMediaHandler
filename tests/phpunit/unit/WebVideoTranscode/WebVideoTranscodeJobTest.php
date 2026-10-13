<?php

namespace MediaWiki\TimedMediaHandler\Tests\Unit\WebVideoTranscode;

use MediaWiki\Config\Config;
use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\TranscodePreset;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscodeJob;
use PHPUnit\Framework\TestCase;
use Wikimedia\TestingAccessWrapper;

/**
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscodeJob
 */
class WebVideoTranscodeJobTest extends TestCase {

	/**
	 * Test TranscodePreset -> string transformations in WebVideoTranscodeJob
	 *
	 * Avoid dependencies on broader MW functionality by subclassing the constructor
	 *
	 * @param int $sourceWidth
	 * @param int $sourceHeight
	 * @return TestingAccessWrapper
	 */
	private function createJob( int $sourceWidth, int $sourceHeight ): TestingAccessWrapper {
		$mockFile = $this->createMock( File::class );
		$mockFile->method( 'getWidth' )
			->willReturn( $sourceWidth );
		$mockFile->method( 'getHeight' )
			->willReturn( $sourceHeight );
		$mockFile->method( 'getHandler' )
			->willReturn( false );
		$mockFile->method( 'getLength' )
			->willReturn( 256 );
		$mockFile->method( 'getName' )
			->willReturn( 'Some_file_name' );

		$params = [];
		$config = $this->createMock( Config::class );
		$config->method( 'get' )
			->willReturn( 0 );
		// New class to mock the constructor
		$job = new class( $params, $config, $mockFile ) extends WebVideoTranscodeJob {
			public function __construct( $params, protected readonly Config $config, $file ) {
				$this->params = $params;
				$this->file = $file;
			}
		};
		return TestingAccessWrapper::newFromObject( $job );
	}

	private function getBasicOpts( $transcodePreset, $numberOfRenditions ): array {
		$basic = [ '-threads 0', '-pix_fmt yuv420p',
			$transcodePreset->videoCodec == 'vp9' ? '-vcodec libvpx-vp9' : '-vcodec libvpx',
			'-quality good', '-use_template 1', '-use_timeline 1',
			'-ss 0', '-avoid_negative_ts make_zero', '-map_metadata -1',
			'-init_seg_name init-$RepresentationID$.webm',
			'-media_seg_name chunk-$RepresentationID$-$Number%05d$.webm'
		];
		if ( $transcodePreset->segmentDuration ) {
			$basic[] = '-seg_duration ' . (int)$transcodePreset->segmentDuration;
		}
		$basic[] = '-map 0:a?';
		for ( $i = 0; $i < $numberOfRenditions; $i++ ) {
			$basic[] = '-map 0:v';
		}
		return $basic;
	}

	public function testFfmpegAddMPEGDASHOptions_allSizesLargerThanOriginal() {
		$job = $this->createJob( 100, 100 );
		$transcodePreset = new TranscodePreset( [
			'type' => 'application/dash+xml',
			'videoCodec' => 'vp9',
			'twopass' => false,
			'segmentDuration' => '4',
			'videoRenditions' => [
				[
					'maxSize' => '213x120',
					'videoBitrate' => '95k',
					'maxrate' => '137k',
					'crf' => '37',
					'speed' => '3',
				],
				[
					'maxSize' => '320x180',
					'videoBitrate' => '189k',
					'maxrate' => '274k',
					'crf' => '37',
					'speed' => '3',
				],
			]
		] );
		$this->assertEquals(
			array_merge( $this->getBasicOpts( $transcodePreset, 1 ), [
				// if all sizes are larger than the original just do a rendition at the original size
				'-s:v:0 100x100'
			] ),
			$job->ffmpegAddMPEGDASHOptions( $transcodePreset )
		);
	}

	public function testFfmpegAddMPEGDASHOptions() {
		$job = $this->createJob( 3840, 2160 );
		$transcodePreset = new TranscodePreset( [
			'type' => 'application/dash+xml',
			'videoCodec' => 'vp9',
			'twopass' => false,
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'segmentDuration' => '4',
			'videoRenditions' => [
				[
					'maxSize' => '213x120',
					'videoBitrate' => '95k',
					'maxrate' => '137k',
					'crf' => '37',
					'speed' => '3',
				],
				[
					'maxSize' => '320x180',
					'videoBitrate' => '189k',
					'maxrate' => '274k',
					'crf' => '37',
					'speed' => '3',
				],
				[
					'maxSize' => '426x240',
					'videoBitrate' => '308k',
					'maxrate' => '447k',
					'crf' => '37',
					'speed' => '3',
					'tileColumns' => '2',
				],
			]
		] );
		$this->assertEquals(
			array_merge( $this->getBasicOpts( $transcodePreset, 3 ), [
				// note that for the size param we round odd numbers up to the nearest even number
				'-s:v:0 214x120',
				'-b:v:0 95000',
				'-maxrate:v:0 137000',
				'-bufsize:v:0 548000',
				'-speed:v:0 3',
				'-crf:v:0 37',
				'-s:v:1 320x180',
				'-b:v:1 189000',
				'-maxrate:v:1 274000',
				'-bufsize:v:1 1096000',
				'-speed:v:1 3',
				'-crf:v:1 37',
				'-s:v:2 426x240',
				'-b:v:2 308000',
				'-maxrate:v:2 447000',
				'-bufsize:v:2 1788000',
				'-speed:v:2 3',
				'-crf:v:2 37',
				'-tile-columns:v:2 2',
			] ),
			$job->ffmpegAddMPEGDASHOptions( $transcodePreset )
		);
	}

	public function testFfmpegAddMPEGDASHOptions_sizeLimit() {
		$job = $this->createJob( 320, 180 );
		$transcodePreset = new TranscodePreset( [
			'type' => 'application/dash+xml',
			'videoCodec' => 'vp9',
			'twopass' => false,
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'segmentDuration' => '4',
			'videoRenditions' => [
				[
					'maxSize' => '213x120',
					'videoBitrate' => '95k',
					'maxrate' => '137k',
					'crf' => '37',
					'speed' => '3',
				],
				// This is larger than the original filesize, so will be ignored
				[
					'maxSize' => '426x240',
					'videoBitrate' => '308k',
					'maxrate' => '447k',
					'crf' => '37',
					'speed' => '3',
				],
			]
		] );
		$this->assertEquals(
			array_merge( $this->getBasicOpts( $transcodePreset, 1 ), [
				// note that for the size param we round odd numbers up to the nearest even number
				'-s:v:0 214x120',
				'-b:v:0 95000',
				'-maxrate:v:0 137000',
				'-bufsize:v:0 548000',
				'-speed:v:0 3',
				'-crf:v:0 37',
			] ),
			$job->ffmpegAddMPEGDASHOptions( $transcodePreset )
		);
	}

	public function testFfmpegAddMPEGDASHOptions_adjustAspect() {
		$job = $this->createJob( 320, 320 );
		$transcodePreset = new TranscodePreset( [
			'type' => 'application/dash+xml',
			'videoCodec' => 'vp9',
			'twopass' => false,
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'segmentDuration' => '4',
			'videoRenditions' => [
				[
					'maxSize' => '213x120',
					'videoBitrate' => '95k',
					'maxrate' => '137k',
					'crf' => '37',
					'speed' => '3',
				],
			]
		] );
		$this->assertEquals(
			array_merge( $this->getBasicOpts( $transcodePreset, 1 ), [
				'-s:v:0 120x120',
				'-b:v:0 95000',
				'-maxrate:v:0 137000',
				'-bufsize:v:0 548000',
				'-speed:v:0 3',
				'-crf:v:0 37',
			] ),
			$job->ffmpegAddMPEGDASHOptions( $transcodePreset )
		);
	}
}
