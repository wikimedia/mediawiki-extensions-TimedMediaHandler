<?php
/**
 * WebVideoTranscode provides:
 *  encode keys
 *  encode settings
 *
 * 	extends api to return all the streams
 *  extends video tag output to provide all the available sources
 */

namespace MediaWiki\TimedMediaHandler\WebVideoTranscode;

use CdnCacheUpdate;
use ConfigException;
use DeferredUpdates;
use Exception;
use File;
use IForeignRepoWithDB;
use IForeignRepoWithMWApi;
use MediaWiki\FileBackend\FSFile\TempFSFileFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\Handlers\FLACHandler\FLACHandler;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;
use MediaWiki\TimedMediaHandler\Handlers\MIDIHandler\MIDIHandler;
use MediaWiki\TimedMediaHandler\Handlers\MP3Handler\MP3Handler;
use MediaWiki\TimedMediaHandler\Handlers\MP4Handler\MP4Handler;
use MediaWiki\TimedMediaHandler\Handlers\OggHandler\OggHandler;
use MediaWiki\TimedMediaHandler\Handlers\WAVHandler\WAVHandler;
use MediaWiki\TimedMediaHandler\HLS\Multivariant;
use MediaWiki\Title\Title;
use Status;
use TempFSFile;
use Wikimedia\Rdbms\IDatabase;

/**
 * Main WebVideoTranscode Class hold some constants and config values
 */
class WebVideoTranscode {
	/** @var array[] Static cache of transcode state per instantiation */
	public static $transcodeState = [];

	/**
	 * Encoding parameters are set via firefogg encode api
	 *
	 * For clarity and compatibility with passing down
	 * client side encode settings at point of upload
	 *
	 * http://firefogg.org/dev/index.html
	 * @var string[][]
	 */
	public static $derivativeSettings = [

		// WebM VP8/Vorbis transcodes
		//
		// Two-pass encoding is a bit slower, but *massively* improves bitrate control.
		// Trading off speed using the '-speed 3' parameter on the second pass.
		//
		// The current defaults do not include VP8 output, but it may be helpful
		// at a limited resolution range for certain back-compatibility scenarios.
		'160p.webm' => [
			'maxSize' => '288x160',
			'videoBitrate' => '193k',
			'minrate' => '96k',
			'maxrate' => '280k',
			'crf' => '37',
			'speed' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'240p.webm' => [
			'maxSize' => '426x240',
			'videoBitrate' => '385k',
			'minrate' => '193k',
			'maxrate' => '558k',
			'crf' => '37',
			'speed' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'360p.webm' => [
			'maxSize' => '640x360',
			'videoBitrate' => '767k',
			'minrate' => '383k',
			'maxrate' => '1112k',
			'crf' => '36',
			'speed' => '3',
			'slices' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'480p.webm' => [
			'maxSize' => '854x480',
			'videoBitrate' => '1250k',
			'minrate' => '625k',
			'maxrate' => '1813k',
			'crf' => '33',
			'speed' => '3',
			'slices' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'720p.webm' => [
			'maxSize' => '1280x720',
			'videoBitrate' => '2491k',
			'minrate' => '1246k',
			'maxrate' => '3612k',
			'crf' => '32',
			'speed' => '3',
			'slices' => '4',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'1080p.webm' => [
			'maxSize' => '1920x1080',
			'videoBitrate' => '4963k',
			'minrate' => '2482k',
			'maxrate' => '7197k',
			'crf' => '31',
			'speed' => '3',
			'slices' => '4',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'1440p.webm' => [
			'maxSize' => '2560x1440',
			'videoBitrate' => '8094k',
			'minrate' => '4047k',
			'maxrate' => '11736k',
			'crf' => '24',
			'speed' => '2',
			'slices' => '8',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],
		'2160p.webm' => [
			'maxSize' => '3840x2160',
			'videoBitrate' => '16126k',
			'minrate' => '8063k',
			'maxrate' => '23382k',
			'crf' => '15',
			'speed' => '2',
			'slices' => '8',
			'twopass' => 'true',
			'videoCodec' => 'vp8',
			'audioCodec' => 'vorbis',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'video/webm; codecs="vp8, vorbis"',
		],

		// WebM VP9 transcode:
		//
		// Two-pass encoding is a bit slower, but *massively* improves bitrate control.
		// Trading off speed using the '-speed 3' parameter on the second pass.
		//
		// Encoding speed is greatly affected by threading settings; HD videos can use up to
		// 8 threads with a suitable ffmpeg/libvpx and $wgFFmpegVP9RowMT enabled ("row-mt").
		// Ultra-HD can use up to 16 threads. Be sure to set $wgFFmpegThreads to a suitable
		// maximum values!
		//
		'120p.vp9.webm' => [
			'maxSize' => '213x120',
			'videoBitrate' => '95k',
			'minrate' => '47k',
			'maxrate' => '137k',
			'crf' => '37',
			'speed' => '3',
			'videoCodec' => 'vp9',
			'twopass' => 'true',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'180p.vp9.webm' => [
			'maxSize' => '320x180',
			'videoBitrate' => '189k',
			'minrate' => '94k',
			'maxrate' => '274k',
			'crf' => '37',
			'speed' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'240p.vp9.webm' => [
			'maxSize' => '426x240',
			'videoBitrate' => '308k',
			'minrate' => '154k',
			'maxrate' => '447k',
			'crf' => '37',
			'speed' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'360p.vp9.webm' => [
			'maxSize' => '640x360',
			'videoBitrate' => '613k',
			'minrate' => '307k',
			'maxrate' => '889k',
			'crf' => '36',
			'speed' => '3',
			'tileColumns' => '1',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'480p.vp9.webm' => [
			'maxSize' => '854x480',
			'videoBitrate' => '1000k',
			'minrate' => '500k',
			'maxrate' => '1450k',
			'crf' => '33',
			'speed' => '3',
			'tileColumns' => '1',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'720p.vp9.webm' => [
			'maxSize' => '1280x720',
			'videoBitrate' => '1993k',
			'minrate' => '996k',
			'maxrate' => '2890k',
			'crf' => '32',
			'speed' => '3',
			'tileColumns' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'1080p.vp9.webm' => [
			'maxSize' => '1920x1080',
			'videoBitrate' => '3971k',
			'minrate' => '1985k',
			'maxrate' => '5757k',
			'crf' => '31',
			'speed' => '3',
			'tileColumns' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'1440p.vp9.webm' => [
			'maxSize' => '2560x1440',
			'videoBitrate' => '6475k',
			'minrate' => '3238k',
			'maxrate' => '9389k',
			'crf' => '24',
			'speed' => '3',
			'tileColumns' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],
		'2160p.vp9.webm' => [
			'maxSize' => '3840x2160',
			'videoBitrate' => '12900k',
			'minrate' => '6450k',
			'maxrate' => '18706k',
			'crf' => '15',
			'speed' => '3',
			'tileColumns' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'audioCodec' => 'opus',
			'audioBitrate' => '96k',
			'type' => 'video/webm; codecs="vp9, opus"',
		],

		// Adaptive streaming transcodes:
		// * stereo.audio.mp3 audio (for Safari 16 and below)
		// * stereo.audio.opus.mp4 audio (for Chromium, Firefox, Safari 17)
		// * surround.audio.opus.mp4 audio (reserved for future expansion)
		// * 144p.video.mjpeg.mov fallback video for old iOS
		// * 240p .. 2160p.video.vp9.mp4 video
		// * .m3u8 playlists
		//
		'stereo.audio.mp3' => [
			'novideo' => 'true',
			'audioCodec' => 'mp3',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '128k',
			'type' => 'audio/mpeg',
			'streaming' => 'hls',
		],
		'stereo.audio.opus.mp4' => [
			'novideo' => 'true',
			'audioCodec' => 'opus',
			'samplerate' => '48000',
			'channels' => '2',
			'audioBitrate' => '96k',
			'type' => 'audio/mp4; codecs="opus"',
			'streaming' => 'hls',
		],
		/*
		// @todo implement surround support for input
		// with >2 channels. note safari doesn't grok
		// opus surround.
		'surround.audio.opus.mp4' => [
			'novideo' => true,
			'audioCodec' => 'opus',
			'samplerate' => '48000',
			'minChannels' => 3,
			'audioBitrate' => '256k',
			'type' => 'audio/mp4; codecs="opus"',
			'streaming' => 'hls',
		],
		*/

		// Optional back-compat
		// Streaming Motion-JPEG track
		'144p.video.mjpeg.mov' => [
			'width' => '176',
			'height' => '144',
			'fpsmax' => '30',
			'videoBitrate' => '1000k',
			'videoCodec' => 'mjpeg',
			'noaudio' => 'true',
			'type' => 'video/quicktime; codecs="jpeg"',
			'streaming' => 'hls',
			'intraframe' => true,
		],

		// VP9 streaming tracks
		'240p.video.vp9.mp4' => [
			'maxSize' => '426x240',
			'videoBitrate' => '308k',
			'crf' => '37',
			'speed' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'360p.video.vp9.mp4' => [
			'maxSize' => '640x360',
			'videoBitrate' => '613k',
			'crf' => '36',
			'speed' => '3',
			'tileColumns' => '1',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'480p.video.vp9.mp4' => [
			'maxSize' => '854x480',
			'videoBitrate' => '1000k',
			'crf' => '33',
			'speed' => '3',
			'tileColumns' => '1',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'720p.video.vp9.mp4' => [
			'maxSize' => '1280x720',
			'videoBitrate' => '1993k',
			'crf' => '32',
			'speed' => '3',
			'tileColumns' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'1080p.video.vp9.mp4' => [
			'maxSize' => '1920x1080',
			'videoBitrate' => '3971k',
			'crf' => '31',
			'speed' => '3',
			'tileColumns' => '2',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'1440p.video.vp9.mp4' => [
			'maxSize' => '2560x1440',
			'videoBitrate' => '6475k',
			'crf' => '24',
			'speed' => '3',
			'tileColumns' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],
		'2160p.video.vp9.mp4' => [
			'maxSize' => '3840x2160',
			'videoBitrate' => '12900k',
			'crf' => '15',
			'speed' => '3',
			'tileColumns' => '3',
			'twopass' => 'true',
			'videoCodec' => 'vp9',
			'noaudio' => 'true',
			'type' => 'video/mp4; codecs="vp09.00.51.08"',
			'streaming' => 'hls',
		],

		// Loosely defined per PCF guide to mp4 profiles:
		// https://develop.participatoryculture.org/index.php/ConversionMatrix
		// and apple HLS profile guide:
		// https://developer.apple.com/library/ios/#documentation/networkinginternet/conceptual/streamingmediaguide/UsingHTTPLiveStreaming/UsingHTTPLiveStreaming.html#//apple_ref/doc/uid/TP40008332-CH102-DontLinkElementID_24

		// high profile
		// level 2 needed for 160p60
		// level 2.1 needed for 240p60
		// level 3 needed for 360p60, 480p60
		// level 4 needed for 720p60, 1080p30
		// level 4.1 needed for 1080p60
		// level 5 needed for 1440p60, 2160p30
		// level 5.1 needed for 2160p60

		// deprecated
		'160p.mp4' => [
			'maxSize' => '288x160',
			'videoCodec' => 'h264',
			'videoBitrate' => '154k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.640014, mp4a.40.2"',
		],

		'240p.mp4' => [
			'maxSize' => '426x240',
			'videoCodec' => 'h264',
			'videoBitrate' => '308k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E015, mp4a.40.2"',
		],

		// deprecated
		'320p.mp4' => [
			'maxSize' => '480x320',
			'videoCodec' => 'h264',
			'videoBitrate' => '460k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
		],

		'360p.mp4' => [
			'maxSize' => '640x360',
			'videoCodec' => 'h264',
			'videoBitrate' => '613k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
		],
		'480p.mp4' => [
			'maxSize' => '854x480',
			'videoCodec' => 'h264',
			'videoBitrate' => '1000k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
		],
		'720p.mp4' => [
			'maxSize' => '1280x720',
			'videoCodec' => 'h264',
			'videoBitrate' => '1993k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E028, mp4a.40.2"',
		],
		'1080p.mp4' => [
			'maxSize' => '1920x1080',
			'videoCodec' => 'h264',
			'videoBitrate' => '3971k',
			'audioCodec' => 'aac',
			'audioBitrate' => '128k',
			'type' => 'video/mp4; codecs="avc1.640029, mp4a.40.2"',
		],
		// Recommend against due to size
		'1440p.mp4' => [
			'maxSize' => '2560x1440',
			'videoCodec' => 'h264',
			'videoBitrate' => '6475k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E032, mp4a.40.2"',
		],
		// Recommend against due to size
		'2160p.mp4' => [
			'maxSize' => '4096x2160',
			'videoCodec' => 'h264',
			'videoBitrate' => '12900k',
			'audioCodec' => 'aac',
			'audioBitrate' => '112k',
			'type' => 'video/mp4; codecs="avc1.42E033, mp4a.40.2"',
		],

		// Audio profiles
		'ogg' => [
			'audioCodec' => 'vorbis',
			'audioQuality' => '3',
			'samplerate' => '44100',
			'noUpscaling' => 'true',
			'novideo' => 'true',
			'type' => 'audio/ogg; codecs="vorbis"',
		],
		'opus' => [
			'audioCodec' => 'opus',
			'audioQuality' => '1',
			'samplerate' => '48000',
			'noUpscaling' => 'true',
			'novideo' => 'true',
			'type' => 'audio/ogg; codecs="opus"',
		],
		'mp3' => [
			'audioCodec' => 'mp3',
			'audioQuality' => '1',
			'samplerate' => '44100',
			'channels' => '2',
			'noUpscaling' => 'true',
			'novideo' => 'true',
			'type' => 'audio/mpeg',
		],
		'm4a' => [
			'audioCodec' => 'aac',
			'audioQuality' => '1',
			'samplerate' => '44100',
			'noUpscaling' => 'true',
			'novideo' => 'true',
			'type' => 'audio/mp4; codecs="mp4a.40.5"',
		],
	];

	/**
	 * @param File $file
	 * @param string $transcodeKey
	 * @return string
	 */
	public static function getDerivativeFilePath( $file, $transcodeKey ) {
		return $file->getTranscodedPath( static::getTranscodeFileBaseName( $file, $transcodeKey ) );
	}

	/**
	 * Get the name to use as the base name for the transcode.
	 *
	 * Swift has problems where the url-encoded version of
	 * the path (ie '0/00/filename.ogv/filename.ogv.720p.webm' )
	 * is greater than > 1024 bytes, so shorten in that case.
	 *
	 * Future versions might respect FileRepo::$abbrvThreshold.
	 *
	 * @param File $file
	 * @param string $suffix Optional suffix (e.g. transcode key).
	 * @return string File name, or the string transcode.
	 */
	public static function getTranscodeFileBaseName( $file, $suffix = '' ) {
		$name = $file->getName();
		$length = strlen( urlencode( '0/00/' . $name . '/' . $name . '.' . $suffix ) );
		if ( $length > 1024 ) {
			return 'transcode' . '.' . $suffix;
		}
		return $name . '.' . $suffix;
	}

	/**
	 * Get url for a transcode.
	 *
	 * @param File $file
	 * @param string $suffix Transcode key
	 * @return string
	 */
	public static function getTranscodedUrlForFile( $file, $suffix = '' ) {
		return $file->getTranscodedUrl( static::getTranscodeFileBaseName( $file, $suffix ) );
	}

	/**
	 * Get temp file at target path for video encode
	 *
	 * @param File $file
	 * @param string $transcodeKey
	 * @param string $suffix
	 *
	 * @return TempFSFile|false at target encode path
	 */
	public static function getTargetEncodeFile( $file, $transcodeKey, $suffix = '' ) {
		$filePath = static::getDerivativeFilePath( $file, $transcodeKey ) . $suffix;
		$ext = strtolower( pathinfo( $filePath, PATHINFO_EXTENSION ) );

		// Create a temp FS file with the same extension
		$tmpFileFactory = new TempFSFileFactory();
		$tmpFile = $tmpFileFactory->newTempFSFile( 'transcode_' . $transcodeKey, $ext );
		if ( !$tmpFile ) {
			return false;
		}
		return $tmpFile;
	}

	/**
	 * Get the max size of the web stream ( constant bitrate )
	 * @return int
	 */
	public static function getMaxSizeWebStream() {
		$maxSize = 0;
		foreach ( static::enabledVideoTranscodes() as $transcodeKey ) {
			if ( isset( static::$derivativeSettings[$transcodeKey]['videoBitrate'] ) ) {
				$currentSize = static::$derivativeSettings[$transcodeKey]['maxSize'] ?? null;
				if ( $currentSize > $maxSize ) {
					$maxSize = $currentSize;
				}
			}
		}
		return $maxSize;
	}

	/**
	 * Give a rough estimate on file size
	 * Note this is not always accurate.. especially with variable bitrate codecs ;)
	 * @param File $file
	 * @param string $transcodeKey
	 * @suppress PhanTypePossiblyInvalidDimOffset
	 * @return int
	 */
	public static function getProjectedFileSize( $file, $transcodeKey ) {
		$settings = static::$derivativeSettings[$transcodeKey];
		// FIXME broken, as bitrate settings can contain units (64k)
		if ( $settings[ 'videoBitrate' ] && $settings['audioBitrate'] ) {
			return $file->getLength() * 8 * (
				(int)$settings['videoBitrate']
				+
				(int)$settings['audioBitrate']
			);
		}
		// Else just return the size of the source video
		// ( we have no idea how large the actual derivative size will be )

		/** @var ID3Handler $handler */
		$handler = $file->getHandler();
		'@phan-var ID3Handler $handler';
		return $file->getLength() * $handler->getBitrate( $file ) * 8;
	}

	/**
	 * Static function to get the set of video assets
	 * Checks if the file is local or remote and grabs respective sources
	 * @param File &$file
	 * @param array $options
	 * @return array|mixed
	 */
	public static function getSources( &$file, $options = [] ) {
		if ( $file->isLocal() || $file->repo instanceof IForeignRepoWithDB ) {
			return static::getLocalSources( $file, $options );
		}

		if ( $file->getRepo() instanceof IForeignRepoWithMWApi ) {
			return static::getRemoteSources( $file, $options );
		}

		return [];
	}

	/**
	 * Grabs sources from the remote repo via ApiQueryVideoInfo.php entry point.
	 *
	 * TODO: This method could use some rethinking. See comments on PS1 of
	 * 	 <https://gerrit.wikimedia.org/r/#/c/117916/>
	 *
	 * Because this works with commons regardless of whether TimedMediaHandler is installed or not
	 * @param File $file The File must belong to a repo that is an instance of IForeignRepoWithMWApi
	 * @param array $options
	 * @return array|mixed
	 */
	public static function getRemoteSources( $file, $options = [] ) {
		$regenerator = static function () use ( $file, $options ) {
			// Setup source attribute options
			$dataPrefix = in_array( 'nodata', $options, true ) ? '' : 'data-';

			wfDebug( "Get Video sources from remote api for " . $file->getName() . "\n" );
			$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
			$query = [
				'action' => 'query',
				'prop' => 'videoinfo',
				'viprop' => 'derivatives',
				'titles' => $namespaceInfo->getCanonicalName( NS_FILE ) . ':' . $file->getTitle()->getText()
			];

			/** @var IForeignRepoWithMWApi $repo */
			$repo = $file->getRepo();
			'@phan-var IForeignRepoWithMWApi $repo';
			$data = $repo->fetchImageQuery( $query );

			if ( isset( $data['warnings']['query'] ) &&
				$data['warnings']['query']['*'] === "Unrecognized value for parameter 'prop': videoinfo"
			) {
				// The target wiki doesn't have TimedMediaHandler.
				// Use the normal file repo system single source:
				return [ static::getPrimarySourceAttributes( $file, [ $dataPrefix ] ) ];
			}

			$sources = [];
			// Generate the source list from the data response:
			if ( isset( $data['query']['pages'] ) ) {
				$vidResult = array_shift( $data['query']['pages'] );
				if ( isset( $vidResult['videoinfo'] ) ) {
					$derResult = array_shift( $vidResult['videoinfo'] );
					$derivatives = $derResult['derivatives'];
					foreach ( $derivatives as $derivativeSource ) {
						$sources[] = $derivativeSource;
					}
				}
			}

			return $sources;
		};

		$repoInfo = $file->getRepo()->getInfo();
		$cacheTTL = $repoInfo['descriptionCacheExpiry'] ?? 0;

		if ( $cacheTTL > 0 ) {
			$cache = MediaWikiServices::getInstance()->getMainWANObjectCache();
			$sources = $cache->getWithSetCallback(
				$cache->makeKey( 'WebVideoSources-url', $file->getRepoName(), $file->getName() ),
				$cacheTTL,
				$regenerator
			);
		} else {
			$sources = $regenerator();
		}

		return $sources;
	}

	/**
	 * Based on the $wgEnabledTranscodeSet set of enabled derivatives we
	 * return sources that are ready.
	 *
	 * This will not automatically update or queue anything!
	 *
	 * @param File &$file File object
	 * @param array $options Options, a set of options:
	 * 		'nodata' Strips the data- attribute, useful when your output is not html
	 * @return array an associative array of sources suitable for <source> tag output
	 */
	public static function getLocalSources( &$file, $options = [] ) {
		global $wgEnableTranscode;
		$sources = [];

		// Add the original file:
		$sources[] = static::getPrimarySourceAttributes( $file, $options );

		// If $wgEnableTranscode is false don't look for or add other local sources:
		if ( $wgEnableTranscode === false &&
			!( $file->repo instanceof IForeignRepoWithDB ) ) {
			return $sources;
		}

		// If an "oldFile" don't look for other sources:
		if ( $file->isOld() ) {
			return $sources;
		}

		/** @var ID3Handler $handler */
		$handler = $file->getHandler();
		'@phan-var ID3Handler $handler';
		// Now Check for derivatives
		if ( $handler->isAudio( $file ) ) {
			$transcodeSet = static::enabledAudioTranscodes();
		} else {
			$transcodeSet = static::enabledVideoTranscodes();
		}

		$lastHLS = null;
		foreach ( $transcodeSet as $transcodeKey ) {
			if ( static::isTranscodeKeyPlayable( $transcodeKey ) &&
				 static::isTranscodeEnabled( $file, $transcodeKey )
			) {
				// Try and add the source
				static::addSourceIfReady( $file, $sources, $transcodeKey, $options );
			}
			$streaming = static::$derivativeSettings[$transcodeKey]['streaming'] ?? '';
			if ( $streaming === 'hls' && static::isTranscodeReady( $file, $transcodeKey ) ) {
				$lastHLS = $transcodeKey;
			}
		}
		if ( $lastHLS ) {
			$src = static::getTranscodedUrlForFile( $file, 'm3u8' );
			$settings =& static::$derivativeSettings[$lastHLS];
			[ $width, $height ] = static::getMaxSizeTransform(
				$file,
				$settings['maxSize'] ?? (
					implode( 'x', [
						$settings['width'] ?? '0',
						$settings['height'] ?? '0',
					] )
				)
			);
			$sources[] = [
				'src' => $src,
				'title' => wfMessage( 'timedmedia-derivative-desc-m3u8' )->text(),
				'type' => 'application/vnd.apple.mpegurl',
				'shorttitle' => wfMessage( 'timedmedia-derivative-desc-m3u8' )->text(),
				'transcodekey' => 'm3u8',
				'width' => $width,
				'height' => $height,
			];
		}

		return $sources;
	}

	/**
	 * Does this transcode key represent a directly-playable type?
	 * If not it's a backing track for adaptive streaming, and should
	 * not be exposed directly as a downloadable/playable derivative.
	 *
	 * @param string $transcodeKey
	 * @return bool
	 */
	public static function isTranscodeKeyPlayable( $transcodeKey ) {
		$settings = static::$derivativeSettings[$transcodeKey] ?? null;
		if ( !$settings ) {
			return false;
		}
		$streaming = $settings['streaming'] ?? false;
		return !$streaming;
	}

	/**
	 * Get the transcode state for a given filename and transcodeKey
	 *
	 * @param File $file
	 * @param string $transcodeKey
	 * @return bool
	 */
	public static function isTranscodeReady( $file, $transcodeKey ) {
		// Check if we need to populate the transcodeState cache:
		$transcodeState = static::getTranscodeState( $file );

		// If no state is found the cache for this file is false:
		if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
			return false;
		}
		// Else return boolean ready state ( if not null, then ready ):
		return ( $transcodeState[ $transcodeKey ]['time_success'] ) !== null;
	}

	/**
	 * Clear the transcode state cache:
	 * @param string|null $fileName Optional fileName to clear transcode cache for
	 */
	public static function clearTranscodeCache( $fileName = null ) {
		if ( $fileName ) {
			unset( static::$transcodeState[ $fileName ] );
		} else {
			static::$transcodeState = [];
		}
	}

	/**
	 * Populates the transcode table with the current DB state of transcodes
	 * if transcodes are not found in the database their state is set to "false"
	 *
	 * @param File $file File object
	 * @param IDatabase|false $db
	 * @return array[]
	 */
	public static function getTranscodeState( $file, $db = false ) {
		global $wgTranscodeBackgroundTimeLimit;
		$fileName = $file->getName();
		if ( !isset( static::$transcodeState[$fileName] ) ) {
			if ( $db === false ) {
				$db = $file->repo->getReplicaDB();
			}
			// initialize the transcode state array
			static::$transcodeState[ $fileName ] = [];
			$res = $db->newSelectQueryBuilder()
				->select( '*' )
				->from( 'transcode' )
				->where( [ 'transcode_image_name' => $fileName ] )
				->limit( 100 )
				->caller( __METHOD__ )
				->fetchResultSet();

			$overTimeout = [];
			$over = time() - ( 2 * $wgTranscodeBackgroundTimeLimit );
			// Populate the per transcode state cache
			foreach ( $res as $row ) {
				// strip the out the "transcode_" from keys
				$transcodeState = [];
				foreach ( $row as $k => $v ) {
					$transcodeState[ str_replace( 'transcode_', '', $k ) ] = $v;
				}
				static::$transcodeState[ $fileName ][ $row->transcode_key ] = $transcodeState;
				if ( $row->transcode_time_startwork !== null
					&& wfTimestamp( TS_UNIX, $row->transcode_time_startwork ) < $over
					&& $row->transcode_time_success === null
					&& $row->transcode_time_error === null
				) {
					$overTimeout[] = $row->transcode_key;
				}
			}
			if ( $overTimeout ) {
				$dbw = $file->repo->getPrimaryDB();
				$dbw->newUpdateQueryBuilder()
					->update( 'transcode' )
					->set( [
						'transcode_time_error' => $dbw->timestamp(),
						'transcode_error' => 'timeout'
					] )
					->where( [
						'transcode_image_name' => $fileName,
						'transcode_key' => $overTimeout
					] )
					->caller( __METHOD__ )
					->execute();
			}
		}
		$sorted = static::$transcodeState[ $fileName ];
		uksort( $sorted, 'strnatcmp' );
		return $sorted;
	}

	/**
	 * Remove any transcode files and db states associated with a given $file
	 * Note that if you want to see them again, you must re-queue them by calling
	 * startJobQueue() or updateJobQueue().
	 *
	 * also remove the transcode files:
	 * @param File $file File Object
	 * @param string|false $transcodeKey Optional transcode key to remove only this key
	 */
	public static function removeTranscodes( $file, $transcodeKey = false ) {
		// if transcode key is non-false, non-null:
		if ( $transcodeKey ) {
			// only remove the requested $transcodeKey
			$removeKeys = [ $transcodeKey ];
		} else {
			// Remove any existing files ( regardless of their state )
			$res = $file->repo->getPrimaryDB()->newSelectQueryBuilder()
				->select( 'transcode_key' )
				->from( 'transcode' )
				->where( [ 'transcode_image_name' => $file->getName() ] )
				->caller( __METHOD__ )
				->fetchResultSet();

			$removeKeys = [];
			foreach ( $res as $transcodeRow ) {
				$removeKeys[] = $transcodeRow->transcode_key;
			}
		}

		// Remove files by key:
		$urlsToPurge = [];
		$filesToPurge = [];
		foreach ( $removeKeys as $tKey ) {
			$urlPath = static::getTranscodedUrlForFile( $file, $tKey );
			$filePath = static::getDerivativeFilePath( $file, $tKey );
			$urlsToPurge[] = $urlPath;
			$filesToPurge[] = $filePath;

			$options = static::$derivativeSettings[$tKey] ?? [];
			$streaming = $options['streaming'] ?? null;
			if ( $streaming === 'hls' ) {
				$urlsToPurge[] = $urlPath . '.m3u8';
				$filesToPurge[] = $filePath . '.m3u8';
			}
		}
		foreach ( $filesToPurge as $filePath ) {
			if ( $file->repo->fileExists( $filePath ) ) {
				$res = $file->repo->quickPurge( $filePath );
				if ( !$res ) {
					wfDebug( "Could not delete file $filePath\n" );
				}
			}
		}

		$update = new CdnCacheUpdate( $urlsToPurge );
		DeferredUpdates::addUpdate( $update );

		// Build the sql query:
		$queryBuilder = $file->repo->getPrimaryDB()->newDeleteQueryBuilder()
			->deleteFrom( 'transcode' )
			->where( [ 'transcode_image_name' => $file->getName() ] );
		// Check if we are removing a specific transcode key
		if ( $transcodeKey !== false ) {
			$queryBuilder->andWhere( [ 'transcode_key' => $transcodeKey ] );
		}
		// Remove the db entries
		$queryBuilder->caller( __METHOD__ )->execute();

		// Purge the cache for pages that include this video:
		$titleObj = $file->getTitle();
		static::invalidatePagesWithFile( $titleObj );

		// Remove from local WebVideoTranscode cache:
		static::clearTranscodeCache( $titleObj->getDBkey() );
		static::updateStreamingManifests( $file );
	}

	/**
	 * @param Title $titleObj
	 */
	public static function invalidatePagesWithFile( $titleObj ) {
		wfDebug( "WebVideoTranscode:: Invalidate pages that include: " . $titleObj->getDBkey() . "\n" );
		// Purge the main image page:
		$titleObj->invalidateCache();

		// TODO if the video is used in over 500 pages add to 'job queue'
		// TODO interwiki invalidation ?
		$limit = 500;
		$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
		$dbr = $lbFactory->getReplicaDatabase();
		$res = $dbr->newSelectQueryBuilder()
			->fields( [ 'page_namespace', 'page_title' ] )
			->from( 'imagelinks' )
			->join( 'page', null, [ 'il_from = page_id' ] )
			->where( [ 'il_to' => $titleObj->getDBkey() ] )
			->limit( $limit + 1 )
			->caller( __METHOD__ )
			->fetchResultSet();

		foreach ( $res as $page ) {
			$title = Title::makeTitle( $page->page_namespace, $page->page_title );
			$title->invalidateCache();
		}
	}

	/**
	 * Add a source to the sources list if the transcode job is ready
	 *
	 * If the source is not found, it will not be used yet...
	 * Missing transcodes should be added by write tasks, not read tasks!
	 * @param File $file
	 * @param array &$sources
	 * @param string $transcodeKey
	 * @param array $dataPrefix
	 */
	public static function addSourceIfReady( $file, &$sources, $transcodeKey, $dataPrefix ) {
		// Check if the transcode is ready:
		if ( static::isTranscodeReady( $file, $transcodeKey ) ) {
			$sources[] = static::getDerivativeSourceAttributes( $file, $transcodeKey, $dataPrefix );
		}
	}

	/**
	 * Get the primary "source" asset used for other derivatives
	 * @param File $file
	 * @param array $options
	 * @return array
	 */
	public static function getPrimarySourceAttributes( $file, $options = [] ) {
		$src = in_array( 'fullurl', $options, true ) ? wfExpandUrl( $file->getUrl() ) : $file->getUrl();

		/** @var FLACHandler|MIDIHandler|MP3Handler|MP4Handler|OggHandler|WAVHandler $handler */
		$handler = $file->getHandler();
		'@phan-var FLACHandler|MIDIHandler|MP3Handler|MP4Handler|OggHandler|WAVHandler $handler';
		$bitrate = $handler->getBitrate( $file );

		$source = [
			'src' => $src,
			'type' => $handler->getWebType( $file ),
			'width' => (int)$file->getWidth(),
			'height' => (int)$file->getHeight(),
		];

		if ( $bitrate ) {
			$source["bandwidth"] = round( $bitrate );
		}

		// For video include framerate:
		if ( !$handler->isAudio( $file ) ) {
			$framerate = $handler->getFramerate( $file );
			if ( $framerate ) {
				$source[ "framerate" ] = (float)$framerate;
			}
		}
		return $source;
	}

	/**
	 * Get derivative "source" attributes
	 * @param File $file
	 * @param string $transcodeKey
	 * @param array $options
	 * @return array
	 * @suppress PhanTypePossiblyInvalidDimOffset
	 */
	public static function getDerivativeSourceAttributes( $file, $transcodeKey, $options = [] ) {
		$fileName = $file->getTitle()->getDBkey();

		$src = static::getTranscodedUrlForFile( $file, $transcodeKey );

		/** @var ID3Handler $handler */
		$handler = $file->getHandler();
		'@phan-var ID3Handler $handler';
		if ( $handler->isAudio( $file ) ) {
			$width = $height = 0;
		} else {
			[ $width, $height ] = static::getMaxSizeTransform(
				$file,
				static::$derivativeSettings[$transcodeKey]['maxSize']
			);
		}

		$framerate = static::$derivativeSettings[$transcodeKey]['framerate']
			?? $handler->getFramerate( $file );
		// Setup the url src:
		$src = in_array( 'fullurl', $options, true ) ? wfExpandUrl( $src ) : $src;
		$fields = [
			'src' => $src,
			'type' => static::$derivativeSettings[ $transcodeKey ][ 'type' ],
			'transcodekey' => $transcodeKey,

			// Add data attributes per emerging DASH / webTV adaptive streaming attributes
			// eventually we will define a manifest xml entry point.
			"width" => (int)$width,
			"height" => (int)$height,
		];

		// a "ready" transcode should have a bitrate:
		if ( isset( static::$transcodeState[$fileName] ) ) {
			$fields["bandwidth"] = (int)static::$transcodeState[$fileName][$transcodeKey]['final_bitrate'];
		}

		if ( !$handler->isAudio( $file ) ) {
			$fields += [ "framerate" => (float)$framerate ];
		}
		return $fields;
	}

	/**
	 * Queue up all enabled transcodes if missing.
	 * @param File $file File object
	 */
	public static function startJobQueue( File $file ) {
		$keys = static::enabledTranscodes();

		// 'Natural sort' puts the transcodes in ascending order by resolution,
		// which roughly gives us fastest-to-slowest order.
		natsort( $keys );

		foreach ( $keys as $tKey ) {
			// Note the job queue will de-duplicate and handle various errors, so we
			// can just blast out the full list here.
			static::updateJobQueue( $file, $tKey );
		}
	}

	/**
	 * Regenerate the streaming manifests, currently the HLS multivariant playlist,
	 * to refer to available completed transcodes. If there are no available
	 * compatible transcodes the playlist will be written out empty.
	 *
	 * Simultaneous attempts to overwrite will result in whichever commits to
	 * the filesystem or other backend last "winning". Locks in the database
	 * have been known to cause production problems, and a more thorough queueing
	 * system might be wise to look into later.
	 *
	 * @param File $file base file to check for transcodes on
	 */
	public static function updateStreamingManifests( File $file ): Status {
		$fileName = $file->getTitle()->getDBkey();
		$repo = $file->getRepo();
		if ( !is_a( $repo, 'LocalRepo' ) ) {
			return Status::newGood();
		}
		$dbw = $repo->getPrimaryDB();

		// Note that trying to use a database lock here plays hell with many
		// many scenarios in production, it seems, especially when deleting
		// files.
		//
		// See [T348689](https://phabricator.wikimedia.org/T348689) etc.
		//
		// To in future: serialize these updates through the job queue
		// or something else *clever* and non-destructive in terms of wait
		// states.

		static::clearTranscodeCache( $fileName );

		// Currently only HLS streaming is output.
		$m3u8 = "$fileName.m3u8";
		$keys = [];
		foreach ( static::$derivativeSettings as $key => $settings ) {
			$streaming = $settings['streaming'] ?? '';
			if ( $streaming === 'hls' && static::isTranscodeEnabled( $file, $key ) ) {
				$keys[] = $key;
			}
		}
		// @todo look up the frame rate and final bitrates and use those
		$multivariant = new Multivariant( $fileName, $keys );
		$playlist = $multivariant->playlist();

		$tmpFileFactory = new TempFSFileFactory();
		$tmpFile = $tmpFileFactory->newTempFSFile( $m3u8, 'm3u8' );
		if ( !$tmpFile ) {
			return Status::newFatal( 'm3u8-error-create-temp', $m3u8 );
		}
		$result = file_put_contents( $tmpFile->getPath(), $playlist );
		if ( $result === false ) {
			return Status::newFatal( 'm3u8-error-write-temp', $m3u8 );
		}

		$result = $repo->quickImport(
			$tmpFile,
			$file->getTranscodedPath( $m3u8 )
		);
		return $result;
	}

	/**
	 * Make sure all relevant transcodes for the given file are tracked in the
	 * transcodes table; add entries for any missing ones.
	 *
	 * @param File $file File object
	 */
	public static function cleanupTranscodes( File $file ) {
		$fileName = $file->getTitle()->getDBkey();
		$dbw = $file->repo->getPrimaryDB();

		$transcodeState = static::getTranscodeState( $file, $dbw );

		$keys = static::enabledTranscodes();
		foreach ( $keys as $transcodeKey ) {
			if ( !static::isTranscodeEnabled( $file, $transcodeKey ) ) {
				// This transcode is no longer enabled or erroneously included...
				// Leave it in place, allowing it to be removed manually;
				// it won't be used in playback and should be doing no harm.
				continue;
			}
			if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
				$dbw->insert(
					'transcode',
					[
						'transcode_image_name' => $fileName,
						'transcode_key' => $transcodeKey,
						'transcode_time_addjob' => null,
						'transcode_error' => "",
						'transcode_final_bitrate' => 0
					],
					__METHOD__,
					[ 'IGNORE' ]
				);
			}
		}

		// Remove from local WebVideoTranscode cache:
		static::clearTranscodeCache( $fileName );
	}

	/**
	 * Check if the given transcode key is appropriate for the file.
	 *
	 * @param File $file File object
	 * @param string $transcodeKey transcode key
	 * @return bool
	 * @suppress PhanTypePossiblyInvalidDimOffset
	 */
	public static function isTranscodeEnabled( File $file, $transcodeKey ) {
		/** @var FLACHandler|MIDIHandler|MP3Handler|MP4Handler|OggHandler|WAVHandler $handler */
		$handler = $file->getHandler();
		'@phan-var FLACHandler|MIDIHandler|MP3Handler|MP4Handler|OggHandler|WAVHandler $handler';
		$audio = $handler->isAudio( $file );
		if ( $audio ) {
			$keys = static::enabledAudioTranscodes();
		} else {
			$keys = static::enabledVideoTranscodes();
		}

		if ( in_array( $transcodeKey, $keys, true ) ) {
			$settings = static::$derivativeSettings[$transcodeKey];
			if ( $audio ) {
				$sourceCodecs = $handler->getStreamTypes( $file );
				$sourceCodec = $sourceCodecs ? strtolower( $sourceCodecs[0] ) : '';
				return ( $sourceCodec !== $settings['audioCodec'] );
			}
			$streaming = $settings['streaming'] ?? false;
			$novideo = $settings['novideo'] ?? false;
			if ( $streaming && $novideo ) {
				// Streaming audio should be generated for all formats
				// if audio is present on the file, and for none if not.
				return $handler->hasAudio( $file );
			}
			if ( static::isTargetLargerThanFile( $file, $settings['maxSize'] ?? '' ) ) {
				// Are we the smallest enabled transcode for this type?
				// Then go ahead and make a wee little transcode for compat.
				return static::isSmallestTranscodeForCodec( $transcodeKey );
			}
			return true;
		}
		// Transcode key is invalid or has been disabled.
		return false;
	}

	/**
	 * Update the job queue if the file is not already in the job queue:
	 * @param File &$file File object
	 * @param string $transcodeKey transcode key
	 * @param bool $manualOverride permission to override soft limits on output size
	 */
	public static function updateJobQueue( &$file, $transcodeKey, $manualOverride = false ) {
		$fileName = $file->getTitle()->getDBkey();
		$dbw = $file->repo->getPrimaryDB();

		$transcodeState = static::getTranscodeState( $file, $dbw );

		if ( !static::isTranscodeEnabled( $file, $transcodeKey ) ) {
			return;
		}

		// If the job hasn't been added yet, attempt to do so
		if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
			$dbw->insert(
				'transcode',
				[
					'transcode_image_name' => $fileName,
					'transcode_key' => $transcodeKey,
					'transcode_time_addjob' => $dbw->timestamp(),
					'transcode_error' => "",
					'transcode_final_bitrate' => 0
				],
				__METHOD__,
				[ 'IGNORE' ]
			);

			if ( !$dbw->affectedRows() ) {
				// There is already a row for that job added by another request, no need to continue
				return;
			}

			// Set the priority
			$prioritized = static::isTranscodePrioritized( $file, $transcodeKey );

			$job = new WebVideoTranscodeJob( $file->getTitle(), [
				'transcodeMode' => 'derivative',
				'transcodeKey' => $transcodeKey,
				'prioritized' => $prioritized,
				'manualOverride' => $manualOverride,
			] );

			try {
				MediaWikiServices::getInstance()->getJobQueueGroupFactory()->makeJobQueueGroup()->push( $job );
				// Clear the state cache ( now that we have updated the page )
				static::clearTranscodeCache( $fileName );
			} catch ( Exception $ex ) {
				// Adding job failed, update transcode row
				$dbw->update(
					'transcode',
					[
						'transcode_time_error' => $dbw->timestamp(),
						'transcode_error' => "Failed to insert Job."
					],
					[
						'transcode_image_name' => $fileName,
						'transcode_key' => $transcodeKey,
					],
					__METHOD__
				);
			}
		}
	}

	/**
	 * Check if this transcode belongs to the high-priority queue.
	 * @param File $file
	 * @param string $transcodeKey
	 * @return bool
	 */
	public static function isTranscodePrioritized( File $file, $transcodeKey ) {
		global $wgTmhPriorityResolutionThreshold, $wgTmhPriorityLengthThreshold;

		$transcodeHeight = 0;
		$matches = [];
		if ( preg_match( '/^(\d+)p/', $transcodeKey, $matches ) ) {
			$transcodeHeight = (int)$matches[0];
		}
		return ( $transcodeHeight <= $wgTmhPriorityResolutionThreshold )
			&& ( $file->getLength() <= $wgTmhPriorityLengthThreshold );
	}

	/**
	 * Return job queue length for the queue that will run this transcode.
	 * @param File $file
	 * @param string $transcodeKey
	 * @return int
	 */
	public static function getQueueSize( File $file, $transcodeKey ) {
		// Warning: this won't treat the prioritized queue separately.
		$db = $file->repo->getPrimaryDB();
		return $db->newSelectQueryBuilder()
			->from( 'transcode' )
			->where( [
				'transcode_time_addjob IS NOT NULL',
				'transcode_time_startwork IS NULL',
				'transcode_time_success IS NULL',
				'transcode_time_error IS NULL',
			] )
			->caller( __METHOD__ )
			->fetchRowCount();
	}

	/**
	 * Transforms the size per a given "maxSize"
	 *  if maxSize is > file, file size is used
	 * @param File $file
	 * @param string $targetMaxSize
	 * @return int[]
	 */
	public static function getMaxSizeTransform( $file, $targetMaxSize ) {
		$maxSize = static::getMaxSize( $targetMaxSize );
		$sourceWidth = (int)$file->getWidth();
		$sourceHeight = (int)$file->getHeight();
		if ( $sourceHeight === 0 ) {
			// Audio file
			return [ 0, 0 ];
		}
		$sourceAspect = $sourceWidth / $sourceHeight;
		$targetWidth = $sourceWidth;
		$targetHeight = $sourceHeight;
		if ( $sourceAspect <= $maxSize['aspect'] ) {
			if ( $sourceHeight > $maxSize['height'] ) {
				$targetHeight = $maxSize['height'];
				$targetWidth = (int)( $targetHeight * $sourceAspect );
			}
		} else {
			if ( $sourceWidth > $maxSize['width'] ) {
				$targetWidth = $maxSize['width'];
				$targetHeight = (int)( $targetWidth / $sourceAspect );
				// some players do not like uneven frame sizes
			}
		}
		// some players do not like uneven frame sizes
		$targetWidth += $targetWidth % 2;
		$targetHeight += $targetHeight % 2;
		return [ $targetWidth, $targetHeight ];
	}

	/**
	 * Test if a given transcode target is larger than the source file
	 *
	 * @param File &$file File object
	 * @param string $targetMaxSize
	 * @return bool
	 */
	public static function isTargetLargerThanFile( &$file, $targetMaxSize ) {
		$maxSize = static::getMaxSize( $targetMaxSize );
		$sourceWidth = $file->getWidth();
		$sourceHeight = $file->getHeight();
		$sourceAspect = (int)$sourceWidth / (int)$sourceHeight;
		if ( $sourceAspect <= $maxSize['aspect'] ) {
			return ( $maxSize['height'] > $sourceHeight );
		}
		return ( $maxSize['width'] > $sourceWidth );
	}

	/**
	 * Is the given transcode key the smallest configured transcode for
	 * its video codec?
	 * @param string $transcodeKey
	 * @return bool
	 * @suppress PhanTypePossiblyInvalidDimOffset
	 */
	public static function isSmallestTranscodeForCodec( $transcodeKey ) {
		$settings = static::$derivativeSettings[$transcodeKey];
		$vcodec = $settings['videoCodec'];
		$maxSize = static::getMaxSize( $settings['maxSize'] );

		foreach ( static::enabledVideoTranscodes() as $tKey ) {
			$tsettings = static::$derivativeSettings[$tKey];
			if ( isset( $tsettings['novideo'] ) ) {
				// This is an audio track for a video streaming set.
				// Always generate it.
				return true;
			}
			if ( $tsettings['videoCodec'] === $vcodec ) {
				$tmaxSize = static::getMaxSize( $tsettings['maxSize'] );
				if ( $tmaxSize['width'] < $maxSize['width'] ) {
					return false;
				}
				if ( $tmaxSize['height'] < $maxSize['height'] ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Return maxSize array for given maxSize setting
	 *
	 * @param string $targetMaxSize
	 * @return array
	 */
	public static function getMaxSize( $targetMaxSize ) {
		$maxSize = [];
		$targetMaxSize = explode( 'x', $targetMaxSize, 2 );
		$maxSize['width'] = (int)$targetMaxSize[0];
		if ( count( $targetMaxSize ) === 1 ) {
			$maxSize['height'] = (int)$targetMaxSize[0];
		} else {
			$maxSize['height'] = (int)$targetMaxSize[1];
		}
		// check for zero size ( audio )
		if ( $maxSize['width'] === 0 || $maxSize['height'] === 0 ) {
			$maxSize['aspect'] = 0;
		} else {
			$maxSize['aspect'] = $maxSize['width'] / $maxSize['height'];
		}
		return $maxSize;
	}

	/**
	 * @param array $set
	 *
	 * @return array
	 */
	private static function filterAndSort( array $set ) {
		$keys = array_keys( array_filter( $set ) );
		natsort( $keys );
		return $keys;
	}

	public static function enabledTranscodes() {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;
		// @phan-suppress-next-line PhanTypeMismatchArgumentNullable These globals are arrays
		return static::filterAndSort( array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet ) );
	}

	public static function enabledVideoTranscodes() {
		global $wgEnabledTranscodeSet;
		return static::filterAndSort( $wgEnabledTranscodeSet );
	}

	public static function enabledAudioTranscodes() {
		global $wgEnabledAudioTranscodeSet;
		return static::filterAndSort( $wgEnabledAudioTranscodeSet );
	}

	public static function validateTranscodeConfiguration() {
		foreach ( static::enabledTranscodes() as $transcodeKey ) {
			if ( !isset( static::$derivativeSettings[ $transcodeKey ] ) ) {
				throw new ConfigException(
					__METHOD__ . ": Invalid key '$transcodeKey' specified in"
						. " wgEnabledTranscodeSet or wgEnabledAudioTranscodeSet."
				);
			}
		}
	}

	public static function isBaseMediaFormat( string $extension ): bool {
		$isos = [ 'mp4', 'm4v', 'm4a', 'mov', '3gp' ];
		return in_array( $extension, $isos );
	}

	/**
	 * Expand a bitrate that may have a k/m/g suffix
	 *
	 * @param string|int $rate
	 * @return int
	 */
	public static function expandRate( $rate ) {
		if ( is_int( $rate ) ) {
			return $rate;
		}
		$matches = [];
		if ( preg_match( '/^(\d+)([kmg])$/', strtolower( $rate ), $matches ) ) {
			$n = (int)$matches[1];
			switch ( $matches[2] ) {
			case 'g':
				$n *= 1000;
				// fall through
			case 'm':
				$n *= 1000;
				// fall through
			case 'k':
				$n *= 1000;
				break;
			default:
				throw new Exception( "Unexpected size suffix: " . $matches[2] );
			}
			return $n;
		} else {
			return (int)$rate;
		}
	}

}
