<?php
/**
 * This file is part of the TimedMediaHandler extension for MediaWiki.
 *
 * Transcode configuration and utilities.
 *
 * @file
 */

namespace MediaWiki\TimedMediaHandler\WebVideoTranscode;

use MediaWiki\Config\Config;
use MediaWiki\Config\ConfigException;

class TranscodePresets {

	/** @var array<string, TranscodePreset>|null */
	private static ?array $settings = null;

	private Config $config;

	/**
	 * Encoding parameters are interpreted and used for WebVideoTranscodeJob
	 *
	 * @var array<string, array>
	 */
	private static $derivativeSettings = [

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
		// * 144p.video.mjpeg.mov fallback video for old iOS (optional)
		// * 180p .. 480p.video.mpeg4.mp4 fallback video for old iOS (optional)
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

		// Optional back-compat for iOS before 17.4 (which adds consistent WebM)
		// Flat files because HLS support varies based on iOS version
		//
		// Motion-JPEG compresses very poorly, but works consistently.
		// AAC-LC for the audio track:
		'144p.mjpeg.mov' => [
			'maxSize' => '256x144',
			'fpsmax' => '30',
			'videoBitrate' => '1000k',
			'videoCodec' => 'mjpeg',
			'audioCodec' => 'mp3',
			'audioBitrate' => '128k',
			'samplerate' => '48000',
			'channels' => '2',
			'type' => 'video/quicktime'
		],
		// MPEG-4 Visual compresses a lot better, and allows a more
		// suitable resolution for online viewing.
		// AAC-LC for the audio track:
		'360p.mpeg4.mov' => [
			'maxSize' => '640x360',
			'videoBitrate' => '1000k',
			'twopass' => 'true',
			'videoCodec' => 'mpeg4',
			'audioCodec' => 'mp3',
			'audioBitrate' => '128k',
			'samplerate' => '48000',
			'channels' => '2',
			'type' => 'video/quicktime',
		],
		// Streaming Motion-JPEG track
		//
		// These are video-only, in fragmented .mov that allows adaptive streaming
		// with chunks split at fragment boundaries listed in an associated .m3u8
		// streaming playlist. MJPEG works with iOS on hardware that doesn't support
		// the VP9 codec, but is poorly compressed for the low resolution.
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
		//
		// These are video-only, in fragmented .mp4 that allows adaptive streaming
		// with chunks split at fragment boundaries listed in an associated .m3u8
		// streaming playlist.
		//
		// The 'remuxFrom' key specifies that if a WebM tracks was previously made,
		// it can be used as a data source via remuxing packets instead of doing
		// a fresh encoding when doing bulk conversions with requeueTranscodes.php
		// with the '--remux' option.
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
			'remuxFrom' => [ '240p.vp9.webm' ],
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
			'remuxFrom' => [ '360p.vp9.webm' ],
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
			'remuxFrom' => [ '480p.vp9.webm' ],
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
			'remuxFrom' => [ '720p.vp9.webm' ],
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
			'remuxFrom' => [ '1080p.vp9.webm' ],
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
			'remuxFrom' => [ '1440p.vp9.webm' ],
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
			'remuxFrom' => [ '2160p.vp9.webm' ],
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

	public function __construct( Config $config ) {
		$this->config = $config;
	}

	private static function filterAndSort( array $set ): array {
		$keys = array_keys( array_filter( $set ) );
		natsort( $keys );
		// @phan-suppress-next-line PhanRedundantArrayValuesCall False positive: renumbering
		return array_values( $keys );
	}

	private function getConfig(): Config {
		return $this->config;
	}

	/**
	 * List of preset keys for all known and enabled preset definitions
	 *
	 * @return array<string>
	 */
	public function enabledTranscodes(): array {
		$config = $this->getConfig();
		return self::filterAndSort( array_merge(
			$config->get( 'EnabledTranscodeSet' ),
			$config->get( 'EnabledAudioTranscodeSet' )
		) );
	}

	/**
	 * List of preset keys for known and enabled preset definitions for video
	 *
	 * @return array<string>
	 */
	public function enabledVideoTranscodes(): array {
		$config = $this->getConfig();
		return self::filterAndSort( $config->get( 'EnabledTranscodeSet' ) );
	}

	/**
	 * List of preset keys for known and enabled preset definitions for audio
	 *
	 * @return array<string>
	 */
	public function enabledAudioTranscodes(): array {
		$config = $this->getConfig();
		return self::filterAndSort( $config->get( 'EnabledAudioTranscodeSet' ) );
	}

	/**
	 * This is used from the extension registrataion callback and runs very early
	 */
	public static function validateTranscodeConfiguration( Config $config ): void {
		$enabledTranscodes = self::filterAndSort( array_merge(
			$config->get( 'EnabledTranscodeSet' ),
			$config->get( 'EnabledAudioTranscodeSet' )
		) );
		foreach ( $enabledTranscodes as $transcodeKey ) {
			if ( !isset( self::allPresets()[ $transcodeKey ] ) ) {
				throw new ConfigException(
					__METHOD__ . ": Invalid key '$transcodeKey' specified in"
					. " wgEnabledTranscodeSet or wgEnabledAudioTranscodeSet."
				);
			}
		}
	}

	/**
	 * Returns all known preset definitions
	 *
	 * @return array<string, TranscodePreset>
	 */
	public static function allPresets(): array {
		if ( self::$settings === null ) {
			self::$settings = [];
			foreach ( self::$derivativeSettings as $key => $settings ) {
				self::$settings[$key] = new TranscodePreset( $settings );
			}
		}
		return self::$settings;
	}

	/**
	 * Return the preset definition for a given key, or null if not found
	 */
	public function findByKey( string $presetKey ): ?TranscodePreset {
		return self::allPresets()[ $presetKey ] ?? null;
	}
}
