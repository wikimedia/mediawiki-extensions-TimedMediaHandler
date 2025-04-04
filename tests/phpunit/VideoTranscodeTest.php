<?php

use MediaWiki\Config\ConfigException;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

/**
 * @author michael dale
 * @group medium
 * @group Database
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode
 * @covers \MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscodeJob
 */
class VideoTranscodeTest extends ApiVideoUploadTestCase {

	/**
	 * Once video files are uploaded test transcoding
	 *
	 *  Test if a transcode job is added for a file once requested
	 *
	 * @dataProvider mediaFilesProvider
	 * @param array $file
	 * Broken as per bug 61878
	 * @group Broken
	 * @covers \MediaWiki\TimedMediaHandler\ApiQueryVideoInfo
	 */
	public function testTranscodeJobs( $file ) {
		// Upload the file to the mediaWiki system
		$result = $this->uploadFile( $file );

		// Check for derivatives ( should trigger adding jobs )
		$fileName = basename( $file['filePath'] );
		$params = [
			'action' => 'query',
			'titles' => 'File:' . $fileName,
			'prop' => 'videoinfo',
			'viprop' => "derivatives",
		];
		[ $result, , ] = $this->doApiRequest( $params );

		// Get the $derivatives:
		$derivatives = $this->getDerivativesFromResult( $result );
		// Only the "source" asset will be present at first:
		$source = current( $derivatives );

		// Check that the source matches the api bandwidth property:
		$this->assertEquals( $file['bandwidth'], $source['bandwidth'] );

		// Check if the transcode jobs were added:
		// get results: query jobs table
		$db = $this->getServiceContainer()->getDBLoadBalancerFactory()->getPrimaryDatabase();
		$res = $db->newSelectQueryBuilder()
			->select( '*' )
			->from( 'transcode' )
			->where( [
				'transcode_image_name' => ucfirst( $fileName )
			] )
			->caller( __METHOD__ )
			->fetchResultSet();
		// Make sure we target at least one ogg and one webm or an mp3:
		$hasOgg = $hasWebM = $hasMP3 = false;
		$novideo = false;
		$targetEncodes = [];
		foreach ( $res as $row ) {
			$transcodeSettings = WebVideoTranscode::$derivativeSettings[ $row->transcode_key ];
			$codec = $transcodeSettings[ 'videoCodec' ] ?? $transcodeSettings[ 'audioCodec' ];
			$novideo = $transcodeSettings[ 'novideo' ] ?? false;
			if ( $codec === 'theora' ) {
				$hasOgg = true;
			}
			if ( $codec === 'vp8' ) {
				$hasWebM = true;
			}
			if ( $codec === 'vp9' ) {
				$hasWebM = true;
			}
			if ( $codec === 'mp3' ) {
				$hasMP3 = true;
			}
			$targetEncodes[ $row->transcode_key ] = $row;
		}

		if ( $novideo ) {
			$this->assertTrue( $hasMP3, 'audio has mp3' );
		} else {
			// Make sure we have ogg and webm for video:
			$this->assertTrue( $hasOgg, 'video has ogg' );
			$this->assertTrue( $hasWebM, 'video has webm' );
		}

		// Now run the transcode job queue
		$this->runJobs( [], [ 'type' => 'webVideoTranscode' ] );

		$db->newSelectQueryBuilder()
			->select( '*' )
			->from( 'transcode' )
			->where( [
				'transcode_image_name' => ucfirst( $fileName )
			] )
			->caller( __METHOD__ )
			->fetchResultSet();

		// Now check if the derivatives were created:
		[ $result, , ] = $this->doApiRequest( $params );
		$derivatives = $this->getDerivativesFromResult( $result );

		// Check that every requested encode was encoded:
		foreach ( $targetEncodes as $transcodeKey => $row ) {
			$targetEncodeFound = false;
			foreach ( $derivatives as $derv ) {
				// The transcode key is always the last part of the file name:
				if ( substr( $derv['src'], -1 * strlen( $transcodeKey ) ) === $transcodeKey ) {
					$targetEncodeFound = true;
				}
			}
			// Test that target encode was found:
			$this->assertTrue( $targetEncodeFound );
		}
	}

	private function getDerivativesFromResult( $result ) {
		// Only the source should be listed initially:
		$this->assertTrue( isset( $result['query']['pages'] ) );
		$page = current( $result['query']['pages'] );

		$videoInfo = current( $page['videoinfo'] );
		$this->assertTrue( isset( $videoInfo['derivatives'] ) );

		return $videoInfo['derivatives'];
	}

	public static function transcodeSetProvider() {
		return [
			[ [ '360p.webm' => true ], [], false ],
			[ [ 'foobar' => true ], [], true ],
			[ [], [ 'foobar' => true ], true ],
		];
	}

	/**
	 * @dataProvider transcodeSetProvider
	 */
	public function testEnabledTranscodeSetConfiguration( $set, $audioSet, $exception ) {
		$this->setMWGlobals( [
			'wgEnabledTranscodeSet' => $set,
			'wgEnabledAudioTranscodeSet' => $audioSet
		] );
		if ( $exception ) {
			$this->expectException( ConfigException::class );
		}
		WebVideoTranscode::validateTranscodeConfiguration();
		// Silence testcase when everything is ok
		$this->assertTrue( true );
	}
}
