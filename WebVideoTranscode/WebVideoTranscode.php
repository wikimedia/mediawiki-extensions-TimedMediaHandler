<?php
/**
 * WebVideoTranscode provides:
 *  encode keys
 *  encode settings
 *
 * 	extends api to return all the streams
 *  extends video tag output to provide all the available sources
 */

/**
 * Main WebVideoTranscode Class hold some constants and config values
 */
class WebVideoTranscode {
	/**
	* Key constants for the derivatives,
	* this key is appended to the derivative file name
	*
	* If you update the wgDerivativeSettings for one of these keys
	* and want to re-generate the video you should also update the
	* key constant. ( Or just run a maintenance script to delete all
	* the assets for a given profile )
	*
	* Msg keys for derivatives are set as follows:
	* $messages['timedmedia-derivative-200_200kbs.ogv'] => 'Ogg 200';
	*/

	// Ogg Profiles
	const ENC_OGV_160P = '160p.ogv';
	const ENC_OGV_240P = '240p.ogv';
	const ENC_OGV_360P = '360p.ogv';
	const ENC_OGV_480P = '480p.ogv';
	const ENC_OGV_720P = '720p.ogv';
	const ENC_OGV_1080P = '1080p.ogv';

	// WebM VP8/Vorbis profiles:
	const ENC_WEBM_160P = '160p.webm';
	const ENC_WEBM_240P = '240p.webm';
	const ENC_WEBM_360P = '360p.webm';
	const ENC_WEBM_480P = '480p.webm';
	const ENC_WEBM_720P = '720p.webm';
	const ENC_WEBM_1080P = '1080p.webm';
	const ENC_WEBM_1440P = '1440p.webm';
	const ENC_WEBM_2160P = '2160p.webm';

	// WebM VP9/Opus profiles:
	const ENC_VP9_160P = '160p.vp9.webm';
	const ENC_VP9_240P = '240p.vp9.webm';
	const ENC_VP9_360P = '360p.vp9.webm';
	const ENC_VP9_480P = '480p.vp9.webm';
	const ENC_VP9_720P = '720p.vp9.webm';
	const ENC_VP9_1080P = '1080p.vp9.webm';
	const ENC_VP9_1440P = '1440p.vp9.webm';
	const ENC_VP9_2160P = '2160p.vp9.webm';

	// mp4 profiles:
	const ENC_H264_160P = '160p.mp4';
	const ENC_H264_240P = '240p.mp4';
	const ENC_H264_320P = '320p.mp4';
	const ENC_H264_360P = '360p.mp4';
	const ENC_H264_480P = '480p.mp4';
	const ENC_H264_720P = '720p.mp4';
	const ENC_H264_1080P = '1080p.mp4';
	const ENC_H264_1440P = '1440p.mp4';
	const ENC_H264_2160P = '2160p.mp4';

	const ENC_OGG_VORBIS = 'ogg';
	const ENC_OGG_OPUS = 'opus';
	const ENC_MP3 = 'mp3';
	const ENC_AAC = 'm4a';

	// Static cache of transcode state per instantiation
	public static $transcodeState = [];

	/**
	* Encoding parameters are set via firefogg encode api
	*
	* For clarity and compatibility with passing down
	* client side encode settings at point of upload
	*
	* http://firefogg.org/dev/index.html
	*/
	public static $derivativeSettings = [
		WebVideoTranscode::ENC_OGV_160P =>
			[
				'maxSize'                    => '288x160',
				'videoBitrate'               => '160',
				'framerate'                  => '15',
				'audioQuality'               => '-1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],
		WebVideoTranscode::ENC_OGV_240P =>
			[
				'maxSize'                    => '426x240',
				'videoBitrate'               => '512',
				'audioQuality'               => '0',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],
		WebVideoTranscode::ENC_OGV_360P =>
			[
				'maxSize'                    => '640x360',
				'videoBitrate'               => '1024',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],
		WebVideoTranscode::ENC_OGV_480P =>
			[
				'maxSize'                    => '854x480',
				'videoBitrate'               => '2048',
				'audioQuality'               => '2',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],

		WebVideoTranscode::ENC_OGV_720P =>
			[
				'maxSize'                    => '1280x720',
				'videoQuality'               => 6,
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],

		WebVideoTranscode::ENC_OGV_1080P =>
			[
				'maxSize'                    => '1920x1080',
				'videoQuality'               => 6,
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'twopass'                    => 'false', // will be overridden by $wgTmhTheoraTwoPassEncoding
				'optimize'                   => 'true',
				'keyframeInterval'           => '128',
				'videoCodec'                 => 'theora',
				'type'                       => 'video/ogg; codecs="theora, vorbis"',
			],

		// WebM transcode:
		WebVideoTranscode::ENC_WEBM_160P =>
			[
				'maxSize'                    => '288x160',
				'videoBitrate'               => '128',
				'audioQuality'               => '-1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_240P =>
			[
				'maxSize'                    => '426x240',
				'videoBitrate'               => '256',
				'audioQuality'               => '-1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_240P =>
			[
				'maxSize'                    => '426x240',
				'videoBitrate'               => '256',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_360P =>
			[
				'maxSize'                    => '640x360',
				'videoBitrate'               => '512',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_480P =>
			[
				'maxSize'                    => '854x480',
				'videoBitrate'               => '1024',
				'audioQuality'               => '2',
				'samplerate'                 => '44100',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_720P =>
			[
				'maxSize'                    => '1280x720',
				'videoBitrate'               => '2048',
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_1080P =>
			 [
				'maxSize'                    => '1920x1080',
				'videoBitrate'               => '4096',
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_1440P =>
			 [
				'maxSize'                    => '2560x1440',
				'videoBitrate'               => '8192',
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],
		WebVideoTranscode::ENC_WEBM_2160P =>
			 [
				'maxSize'                    => '4096x2160',
				'videoBitrate'               => '16384',
				'audioQuality'               => 3,
				'noUpscaling'                => 'true',
				'videoCodec'                 => 'vp8',
				'type'                       => 'video/webm; codecs="vp8, vorbis"',
			],

		// WebM VP9 transcode:
		WebVideoTranscode::ENC_VP9_160P =>
			[
				'maxSize'                    => '288x160',
				'videoBitrate'               => '80',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_240P =>
			[
				'maxSize'                    => '426x240',
				'videoBitrate'               => '128',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_360P =>
			[
				'maxSize'                    => '640x360',
				'videoBitrate'               => '256',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_480P =>
			[
				'maxSize'                    => '854x480',
				'videoBitrate'               => '512',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_720P =>
			[
				'maxSize'                    => '1280x720',
				'videoBitrate'               => '1024',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'tileColumns'                => '2',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_1080P =>
			 [
				'maxSize'                    => '1920x1080',
				'videoBitrate'               => '2048',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'tileColumns'                => '4',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_1440P =>
			 [
				'maxSize'                    => '2560x1440',
				'videoBitrate'               => '4096',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'tileColumns'                => '4',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],
		WebVideoTranscode::ENC_VP9_2160P =>
			 [
				'maxSize'                    => '4096x2160',
				'videoBitrate'               => '8192',
				'samplerate'                 => '48000',
				'noUpscaling'                => 'true',
				'twopass'                    => 'true',
				'keyframeInterval'           => '128',
				'bufDelay'                   => '256',
				'videoCodec'                 => 'vp9',
				'audioCodec'                 => 'opus',
				'tileColumns'                => '4',
				'type'                       => 'video/webm; codecs="vp9, opus"',
			],

		// @codingStandardsIgnoreStart
		// Losly defined per PCF guide to mp4 profiles:
		// https://develop.participatoryculture.org/index.php/ConversionMatrix
		// and apple HLS profile guide:
		// https://developer.apple.com/library/ios/#documentation/networkinginternet/conceptual/streamingmediaguide/UsingHTTPLiveStreaming/UsingHTTPLiveStreaming.html#//apple_ref/doc/uid/TP40008332-CH102-DontLinkElementID_24
		// @codingStandardsIgnoreEnd

		WebVideoTranscode::ENC_H264_160P =>
			[
				'maxSize' => '288x160',
				'videoCodec' => 'h264',
				'videoBitrate' => '160k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '40k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],

		WebVideoTranscode::ENC_H264_240P =>
			[
				'maxSize' => '426x240',
				'videoCodec' => 'h264',
				'videoBitrate' => '256k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '40k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],

		WebVideoTranscode::ENC_H264_320P =>
			[
				'maxSize' => '480x320',
				'videoCodec' => 'h264',
				'videoBitrate' => '400k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '40k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],

		WebVideoTranscode::ENC_H264_360P =>
			[
				'maxSize' => '640x360',
				'videoCodec' => 'h264',
				'videoBitrate' => '512k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '64k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],

		WebVideoTranscode::ENC_H264_480P =>
			[
				'maxSize' => '854x480',
				'videoCodec' => 'h264',
				'videoBitrate' => '1200k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '64k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],
		WebVideoTranscode::ENC_H264_720P =>
			[
				'maxSize' => '1280x720',
				'videoCodec' => 'h264',
				'videoBitrate' => '2500k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '128k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],
		WebVideoTranscode::ENC_H264_1080P =>
			[
				'maxSize' => '1920x1080',
				'videoCodec' => 'h264',
				'videoBitrate' => '5000k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '128k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],
		WebVideoTranscode::ENC_H264_1440P =>
			[
				'maxSize' => '2560x1440',
				'videoCodec' => 'h264',
				'videoBitrate' => '16384k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '128k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],
		WebVideoTranscode::ENC_H264_2160P =>
			[
				'maxSize' => '4096x2160',
				'videoCodec' => 'h264',
				'videoBitrate' => '16384k',
				'audioCodec' => 'aac',
				'channels' => '2',
				'audioBitrate' => '128k',
				'type' => 'video/mp4; codecs="avc1.42E01E, mp4a.40.2"',
			],

		// Audio profiles
		WebVideoTranscode::ENC_OGG_VORBIS =>
			[
				'audioCodec'                 => 'vorbis',
				'audioQuality'               => '3',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'novideo'                    => 'true',
				'type'                       => 'audio/ogg; codecs="vorbis"',
			],
		WebVideoTranscode::ENC_OGG_OPUS =>
			[
				'audioCodec'                 => 'opus',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'novideo'                    => 'true',
				'type'                       => 'audio/ogg; codecs="opus"',
			],
		WebVideoTranscode::ENC_MP3 =>
			[
				'audioCodec'                 => 'mp3',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'novideo'                    => 'true',
				'type'                       => 'audio/mpeg',
			],
		WebVideoTranscode::ENC_AAC =>
			[
				'audioCodec'                 => 'aac',
				'audioQuality'               => '1',
				'samplerate'                 => '44100',
				'channels'                   => '2',
				'noUpscaling'                => 'true',
				'novideo'                    => 'true',
				'type'                       => 'audio/mp4; codecs="mp4a.40.5"',
			],
	];

	/**
	 * @param $file File
	 * @param $transcodeKey string
	 * @return string
	 */
	public static function getDerivativeFilePath( $file, $transcodeKey ) {
		return $file->getTranscodedPath( self::getTranscodeFileBaseName( $file, $transcodeKey ) );
	}

	/**
	 * Get the name to use as the base name for the transcode.
	 *
	 * Swift has problems where the url-encoded version of
	 * the path (ie 'filename.ogv/filename.ogv.720p.webm' )
	 * is greater that > 1024 bytes, so shorten in that case.
	 *
	 * Future versions might respect FileRepo::$abbrvThreshold.
	 *
	 * @param File $file
	 * @param String $suffix Optional suffix (e.g. transcode key).
	 * @return String File name, or the string transcode.
	 */
	public static function getTranscodeFileBaseName( $file, $suffix = '' ) {
		$name = $file->getName();
		if ( strlen( urlencode( $name ) ) * 2 + 12 > 1024 ) {
			return 'transcode' . '.' . $suffix;
		} else {
			return $name . '.' . $suffix;
		}
	}

	/**
	 * Get url for a transcode.
	 *
	 * @param $file File
	 * @param $suffix string Transcode key
	 * @return string
	 */
	public static function getTranscodedUrlForFile( $file, $suffix = '' ) {
		return $file->getTranscodedUrl( self::getTranscodeFileBaseName( $file, $suffix ) );
	}

	/**
	 * Get temp file at target path for video encode
	 *
	 * @param $file File
	 * @param $transcodeKey String
	 *
	 * @return TempFSFile at target encode path
	 */
	public static function getTargetEncodeFile( &$file, $transcodeKey ) {
		$filePath = self::getDerivativeFilePath( $file, $transcodeKey );
		$ext = strtolower( pathinfo( "$filePath", PATHINFO_EXTENSION ) );

		// Create a temp FS file with the same extension
		$tmpFile = TempFSFile::factory( 'transcode_' . $transcodeKey, $ext );
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
		global $wgEnabledTranscodeSet;
		$maxSize = 0;
		foreach ( $wgEnabledTranscodeSet as $transcodeKey ) {
			if ( isset( self::$derivativeSettings[$transcodeKey]['videoBitrate'] ) ) {
				$currentSize = self::$derivativeSettings[$transcodeKey]['maxSize'];
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
	 * @param $file File
	 * @param $transcodeKey string
	 * @return number
	 */
	public static function getProjectedFileSize( $file, $transcodeKey ) {
		$settings = self::$derivativeSettings[$transcodeKey];
		if ( $settings[ 'videoBitrate' ] && $settings['audioBitrate'] ) {
			return $file->getLength() * 8 * (
				self::$derivativeSettings[$transcodeKey]['videoBitrate']
				+
				self::$derivativeSettings[$transcodeKey]['audioBitrate']
			);
		}
		// Else just return the size of the source video
		// ( we have no idea how large the actual derivative size will be )
		return $file->getLength() * $file->getHandler()->getBitrate( $file ) * 8;
	}

	/**
	 * Static function to get the set of video assets
	 * Checks if the file is local or remote and grabs respective sources
	 * @param $file File
	 * @param $options array
	 * @return array|mixed
	 */
	public static function getSources( &$file , $options = [] ) {
		if ( $file->isLocal() || $file->repo instanceof ForeignDBViaLBRepo ) {
			return self::getLocalSources( $file, $options );
		} else {
			return self::getRemoteSources( $file, $options );
		}
	}

	/**
	 * Grabs sources from the remote repo via ApiQueryVideoInfo.php entry point.
	 *
	 * TODO: This method could use some rethinking. See comments on PS1 of
	 *	 <https://gerrit.wikimedia.org/r/#/c/117916/>
	 *
	 * Because this works with commons regardless of whether TimedMediaHandler is installed or not
	 * @param $file File
	 * @param $options array
	 * @return array|mixed
	 */
	public static function getRemoteSources( &$file, $options = [] ) {
		global $wgMemc;
		// Setup source attribute options
		$dataPrefix = in_array( 'nodata', $options )? '': 'data-';

		// Use descriptionCacheExpiry as our expire for timed text tracks info
		if ( $file->repo->descriptionCacheExpiry > 0 ) {
			wfDebug( "Attempting to get sources from cache..." );
			$key = $file->repo->getLocalCacheKey( 'WebVideoSources', 'url', $file->getName() );
			$sources = $wgMemc->get( $key );
			if ( $sources ) {
				wfDebug( "Success found sources in local cache\n" );
				return $sources;
			}
			wfDebug( "source cache miss\n" );
		}

		wfDebug( "Get Video sources from remote api for " . $file->getName() . "\n" );
		$query = [
			'action' => 'query',
			'prop' => 'videoinfo',
			'viprop' => 'derivatives',
			'titles' => MWNamespace::getCanonicalName( NS_FILE ) .':'. $file->getTitle()->getText()
		];

		$data = $file->repo->fetchImageQuery( $query );

		if ( isset( $data['warnings'] ) && isset( $data['warnings']['query'] )
			&& $data['warnings']['query']['*'] == "Unrecognized value for parameter 'prop': videoinfo"
		) {
			// Commons does not yet have TimedMediaHandler.
			// Use the normal file repo system single source:
			return [ self::getPrimarySourceAttributes( $file, [ $dataPrefix ] ) ];
		}
		$sources = [];
		// Generate the source list from the data response:
		if ( isset( $data['query'] ) && $data['query']['pages'] ) {
			$vidResult = array_shift( $data['query']['pages'] );
			if ( isset( $vidResult['videoinfo'] ) ) {
				$derResult = array_shift( $vidResult['videoinfo'] );
				$derivatives = $derResult['derivatives'];
				foreach ( $derivatives as $derivativeSource ) {
					$sources[] = $derivativeSource;
				}
			}
		}

		// Update the cache:
		if ( $sources && $file->repo->descriptionCacheExpiry > 0 ) {
			$wgMemc->set( $key, $sources, $file->repo->descriptionCacheExpiry );
		}

		return $sources;

	}

	/**
	 * Based on the $wgEnabledTranscodeSet set of enabled derivatives we
	 * return sources that are ready.
	 *
	 * This will not automatically update or queue anything!
	 *
	 * @param $file File object
	 * @param $options array Options, a set of options:
	 * 					'nodata' Strips the data- attribute, useful when your output is not html
	 * @return array an associative array of sources suitable for <source> tag output
	 */
	public static function getLocalSources( &$file , $options=[] ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet, $wgEnableTranscode;
		$sources = [];

		// Add the original file:
		$sources[] = self::getPrimarySourceAttributes( $file, $options );

		// If $wgEnableTranscode is false don't look for or add other local sources:
		if ( $wgEnableTranscode === false &&
			!( $file->repo instanceof ForeignDBViaLBRepo ) ) {
			return $sources;
		}

		// If an "oldFile" don't look for other sources:
		if ( $file->isOld() ) {
			return $sources;
		}

		// Now Check for derivatives
		if ( $file->getHandler()->isAudio( $file ) ) {
			$transcodeSet = $wgEnabledAudioTranscodeSet;
		} else {
			$transcodeSet = $wgEnabledTranscodeSet;
		}
		foreach ( $transcodeSet as $transcodeKey ) {
			if ( self::isTranscodeEnabled( $file, $transcodeKey ) ) {
				// Try and add the source
				self::addSourceIfReady( $file, $sources, $transcodeKey, $options );
			}
		}

		return $sources;
	}

	/**
	 * Get the transcode state for a given filename and transcodeKey
	 *
	 * @param $fileName string
	 * @param $transcodeKey string
	 * @return bool
	 */
	public static function isTranscodeReady( $file, $transcodeKey ) {

		// Check if we need to populate the transcodeState cache:
		$transcodeState = self::getTranscodeState( $file );

		// If no state is found the cache for this file is false:
		if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
			return false;
		}
		// Else return boolean ready state ( if not null, then ready ):
		return !is_null( $transcodeState[ $transcodeKey ]['time_success'] );
	}

	/**
	 * Clear the transcode state cache:
	 * @param String $fileName Optional fileName to clear transcode cache for
	 */
	public static function clearTranscodeCache( $fileName = null ) {
		if ( $fileName ) {
			unset( self::$transcodeState[ $fileName ] );
		} else {
			self::$transcodeState = [];
		}
	}

	/**
	 * Populates the transcode table with the current DB state of transcodes
	 * if transcodes are not found in the database their state is set to "false"
	 *
	 * @param {Object} File object
	 */
	public static function getTranscodeState( $file, $db = false ) {
		global $wgTranscodeBackgroundTimeLimit;
		$fileName = $file->getName();
		if ( !isset( self::$transcodeState[$fileName] ) ) {
			if ( $db === false ) {
				$db = $file->repo->getSlaveDB();
			}
			// initialize the transcode state array
			self::$transcodeState[ $fileName ] = [];
			$res = $db->select( 'transcode',
					'*',
					[ 'transcode_image_name' => $fileName ],
					__METHOD__,
					[ 'LIMIT' => 100 ]
			);
			$overTimeout = [];
			$over = $db->timestamp( time() - ( 2 * $wgTranscodeBackgroundTimeLimit ) );
			// Populate the per transcode state cache
			foreach ( $res as $row ) {
				// strip the out the "transcode_" from keys
				$transcodeState = [];
				foreach ( $row as $k => $v ) {
					$transcodeState[ str_replace( 'transcode_', '', $k ) ] = $v;
				}
				self::$transcodeState[ $fileName ][ $row->transcode_key ] = $transcodeState;
				if ( $row->transcode_time_startwork != null
					&& $row->transcode_time_startwork < $over
					&& $row->transcode_time_success == null
					&& $row->transcode_time_error == null ) {
					$overTimeout[] = $row->transcode_key;
				}
			}
			if ( $overTimeout ) {
				$dbw = wfGetDB( DB_MASTER );
				$dbw->update(
					'transcode',
					[
						'transcode_time_error' => $dbw->timestamp(),
						'transcode_error' => 'timeout'
					],
					[
						'transcode_image_name' => $fileName,
						'transcode_key' => $overTimeout
					],
					__METHOD__,
					[ 'LIMIT' => count( $overTimeout ) ]
				);
			}
		}
		$sorted = self::$transcodeState[ $fileName ];
		uksort( $sorted, 'strnatcmp' );
		return $sorted;
	}

	/**
	 * Remove any transcode files and db states associated with a given $file
	 * Note that if you want to see them again, you must re-queue them by calling
	 * startJobQueue() or updateJobQueue().
	 *
	 * also remove the transcode files:
	 * @param $file File Object
	 * @param $transcodeKey String Optional transcode key to remove only this key
	 */
	public static function removeTranscodes( &$file, $transcodeKey = false ) {

		// if transcode key is non-false, non-null:
		if ( $transcodeKey ) {
			// only remove the requested $transcodeKey
			$removeKeys = [ $transcodeKey ];
		} else {
			// Remove any existing files ( regardless of their state )
			$res = $file->repo->getMasterDB()->select( 'transcode',
				[ 'transcode_key' ],
				[ 'transcode_image_name' => $file->getName() ]
			);
			$removeKeys = [];
			foreach ( $res as $transcodeRow ) {
				$removeKeys[] = $transcodeRow->transcode_key;
			}
		}

		// Remove files by key:
		$urlsToPurge = [];
		foreach ( $removeKeys as $tKey ) {
			$urlsToPurge[] = self::getTranscodedUrlForFile( $file, $tKey );
			$filePath = self::getDerivativeFilePath( $file, $tKey );
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
		$dbw = wfGetDB( DB_MASTER );
		$deleteWhere = [ 'transcode_image_name' => $file->getName() ];
		// Check if we are removing a specific transcode key
		if ( $transcodeKey !== false ) {
			$deleteWhere['transcode_key'] = $transcodeKey;
		}
		// Remove the db entries
		$dbw->delete( 'transcode', $deleteWhere, __METHOD__ );

		// Purge the cache for pages that include this video:
		$titleObj = $file->getTitle();
		self::invalidatePagesWithFile( $titleObj );

		// Remove from local WebVideoTranscode cache:
		self::clearTranscodeCache( $titleObj->getDBkey() );
	}

	/**
	 * @param $titleObj Title
	 */
	public static function invalidatePagesWithFile( &$titleObj ) {
		wfDebug( "WebVideoTranscode:: Invalidate pages that include: " . $titleObj->getDBkey() . "\n" );
		// Purge the main image page:
		$titleObj->invalidateCache();

		// TODO if the video is used in over 500 pages add to 'job queue'
		// TODO interwiki invalidation ?
		$limit = 500;
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			[ 'imagelinks', 'page' ],
			[ 'page_namespace', 'page_title' ],
			[ 'il_to' => $titleObj->getDBkey(), 'il_from = page_id' ],
			__METHOD__,
			[ 'LIMIT' => $limit + 1 ]
		);
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
	 */
	public static function addSourceIfReady( &$file, &$sources, $transcodeKey, $dataPrefix = '' ) {
		// Check if the transcode is ready:
		if ( self::isTranscodeReady( $file, $transcodeKey ) ) {
			$sources[] = self::getDerivativeSourceAttributes( $file, $transcodeKey, $dataPrefix );
		}
	}

	/**
	 * Get the primary "source" asset used for other derivatives
	 * @param $file File
	 * @param $options array
	 * @return array
	 */
	public static function getPrimarySourceAttributes( $file, $options = [] ) {
		global $wgLang;
		$src = in_array( 'fullurl', $options )?  wfExpandUrl( $file->getUrl() ) : $file->getUrl();

		$bitrate = $file->getHandler()->getBitrate( $file );
		$metadataType = $file->getHandler()->getMetadataType( $file );

		// Give grep a chance to find the usages: timedmedia-ogg, timedmedia-webm,
		// timedmedia-mp4, timedmedia-flac, timedmedia-wav
		if ( $file->getHandler()->isAudio( $file ) ) {
			$title = wfMessage( 'timedmedia-source-audio-file-desc',
				wfMessage( 'timedmedia-' . $metadataType )->text() )
				->params( $wgLang->formatBitrate( $bitrate ) )->text();
		} else {
			$title = wfMessage( 'timedmedia-source-file-desc',
				wfMessage( 'timedmedia-' . $metadataType )->text() )
				->numParams( $file->getWidth(), $file->getHeight() )
				->params( $wgLang->formatBitrate( $bitrate ) )->text();
		}

		// Give grep a chance to find the usages: timedmedia-ogg, timedmedia-webm,
		// timedmedia-mp4, timedmedia-flac, timedmedia-wav
		$source = [
			'src' => $src,
			'type' => $file->getHandler()->getWebType( $file ),
			'title' => $title,
			"shorttitle" => wfMessage(
				'timedmedia-source-file',
				wfMessage( 'timedmedia-' . $metadataType )->text()
			)->text(),
			"width" => intval( $file->getWidth() ),
			"height" => intval( $file->getHeight() ),
		];

		if ( $bitrate ) {
			$source["bandwidth"] = round( $bitrate );
		}

		// For video include framerate:
		if ( !$file->getHandler()->isAudio( $file ) ) {
			$framerate = $file->getHandler()->getFramerate( $file );
			if ( $framerate ) {
				$source[ "framerate" ] = floatval( $framerate );
			}
		}
		return $source;
	}

	/**
	 * Get derivative "source" attributes
	 * @param $file File
	 * @param $transcodeKey string
	 * @param $options array
	 * @return array
	 */
	public static function getDerivativeSourceAttributes( $file, $transcodeKey, $options = [] ) {
		$fileName = $file->getTitle()->getDbKey();

		$src = self::getTranscodedUrlForFile( $file, $transcodeKey );

		if ( $file->getHandler()->isAudio( $file ) ) {
			$width = $height = 0;
		} else {
			list( $width, $height ) = WebVideoTranscode::getMaxSizeTransform(
				$file,
				self::$derivativeSettings[$transcodeKey]['maxSize']
			);
		}

		$framerate = ( isset( self::$derivativeSettings[$transcodeKey]['framerate'] ) )?
						self::$derivativeSettings[$transcodeKey]['framerate'] :
						$file->getHandler()->getFramerate( $file );
		// Setup the url src:
		$src = in_array( 'fullurl', $options ) ?  wfExpandUrl( $src ) : $src;
		$fields = [
				'src' => $src,
				'title' => wfMessage( 'timedmedia-derivative-desc-' . $transcodeKey )->text(),
				'type' => self::$derivativeSettings[ $transcodeKey ][ 'type' ],
				"shorttitle" => wfMessage( 'timedmedia-derivative-' . $transcodeKey )->text(),
				"transcodekey" => $transcodeKey,

				// Add data attributes per emerging DASH / webTV adaptive streaming attributes
				// eventually we will define a manifest xml entry point.
				"width" => intval( $width ),
				"height" => intval( $height ),
			];

		// a "ready" transcode should have a bitrate:
		if ( isset( self::$transcodeState[$fileName] ) ) {
			$fields["bandwidth"] = intval(
				self::$transcodeState[$fileName][ $transcodeKey ]['final_bitrate']
			);
		}

		if ( !$file->getHandler()->isAudio( $file ) ) {
			$fields += [ "framerate" => floatval( $framerate ) ];
		}
		return $fields;
	}

	/**
	 * Queue up all enabled transcodes if missing.
	 * @param $file File object
	 */
	public static function startJobQueue( File $file ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;
		$keys = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );

		// 'Natural sort' puts the transcodes in ascending order by resolution,
		// which roughly gives us fastest-to-slowest order.
		natsort( $keys );

		foreach ( $keys as $tKey ) {
			// Note the job queue will de-duplicate and handle various errors, so we
			// can just blast out the full list here.
			self::updateJobQueue( $file, $tKey );
		}
	}

	/**
	 * Make sure all relevant transcodes for the given file are tracked in the
	 * transcodes table; add entries for any missing ones.
	 *
	 * @param $file File object
	 */
	public static function cleanupTranscodes( File $file ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;

		$fileName = $file->getTitle()->getDbKey();
		$db = $file->repo->getMasterDB();

		$transcodeState = self::getTranscodeState( $file, $db );

		$keys = array_merge( $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet );
		foreach ( $keys as $transcodeKey ) {
			if ( !self::isTranscodeEnabled( $file, $transcodeKey ) ) {
				// This transcode is no longer enabled or erroneously included...
				// Leave it in place, allowing it to be removed manually;
				// it won't be used in playback and should be doing no harm.
				continue;
			}
			if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
				$db->insert(
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
	}

	/**
	 * Check if the given transcode key is appropriate for the file.
	 *
	 * @param $file File object
	 * @param $transcodeKey String transcode key
	 * @return boolean
	 */
	public static function isTranscodeEnabled( File $file, $transcodeKey ) {
		global $wgEnabledTranscodeSet, $wgEnabledAudioTranscodeSet;

		$audio = $file->getHandler()->isAudio( $file );
		if ( $audio ) {
			$keys = $wgEnabledAudioTranscodeSet;
		} else {
			$keys = $wgEnabledTranscodeSet;
		}

		if ( in_array( $transcodeKey, $keys ) ) {
			$settings = self::$derivativeSettings[$transcodeKey];
			if ( $audio ) {
				$sourceCodecs = $file->getHandler()->getStreamTypes( $file );
				$sourceCodec = $sourceCodecs ? strtolower( $sourceCodecs[0] ) : '';
				return ( $sourceCodec !== $settings['audioCodec'] );
			} elseif ( self::isTargetLargerThanFile( $file, $settings['maxSize'] ) ) {
				// Are we the smallest enabled transcode for this type?
				// Then go ahead and make a wee little transcode for compat.
				return self::isSmallestTranscodeForCodec( $transcodeKey );
			} else {
				return true;
			}
		} else {
			// Transcode key is invalid or has been disabled.
			return false;
		}
	}

	/**
	 * Update the job queue if the file is not already in the job queue:
	 * @param $file File object
	 * @param $transcodeKey String transcode key
	 */
	public static function updateJobQueue( &$file, $transcodeKey ) {
		$fileName = $file->getTitle()->getDbKey();
		$db = $file->repo->getMasterDB();

		$transcodeState = self::getTranscodeState( $file, $db );

		if ( !self::isTranscodeEnabled( $file, $transcodeKey ) ) {
			return;
		}

		// If the job hasn't been added yet, attempt to do so
		if ( !isset( $transcodeState[ $transcodeKey ] ) ) {
			$db->insert(
				'transcode',
				[
					'transcode_image_name' => $fileName,
					'transcode_key' => $transcodeKey,
					'transcode_time_addjob' => $db->timestamp(),
					'transcode_error' => "",
					'transcode_final_bitrate' => 0
				],
				__METHOD__,
				[ 'IGNORE' ]
			);

			if ( !$db->affectedRows() ) {
				// There is already a row for that job added by another request, no need to continue
				return;
			}

			$job = new WebVideoTranscodeJob( $file->getTitle(), [
				'transcodeMode' => 'derivative',
				'transcodeKey' => $transcodeKey,
			] );

			if ( $job->insert() ) {
				// Clear the state cache ( now that we have updated the page )
				self::clearTranscodeCache( $fileName );
			} else {
				// Adding job failed, update transcode row
				$db->update(
					'transcode',
					[
						'transcode_time_error' => $db->timestamp(),
						'transcode_error' => "Failed to insert Job."
					],
					[
						'transcode_image_name' => $fileName,
						'transcode_key' => $transcodeKey,
					],
					__METHOD__,
					[ 'LIMIT' => 1 ]
				);
			}
		}
	}

	/**
	 * Transforms the size per a given "maxSize"
	 *  if maxSize is > file, file size is used
	 * @param $file File
	 * @param $targetMaxSize int
	 * @return array
	 */
	public static function getMaxSizeTransform( &$file, $targetMaxSize ) {
		$maxSize = self::getMaxSize( $targetMaxSize );
		$sourceWidth = intval( $file->getWidth() );
		$sourceHeight = intval( $file->getHeight() );
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
				$targetWidth = intval( $targetHeight * $sourceAspect );
			}
		} else {
			if ( $sourceWidth > $maxSize['width'] ) {
				$targetWidth = $maxSize['width'];
				$targetHeight = intval( $targetWidth / $sourceAspect );
				// some players do not like uneven frame sizes
			}
		}
		// some players do not like uneven frame sizes
		$targetWidth += $targetWidth%2;
		$targetHeight += $targetHeight%2;
		return [ $targetWidth, $targetHeight ];
	}

	/**
	 * Test if a given transcode target is larger than the source file
	 *
	 * @param $file File object
	 * @param $targetMaxSize string
	 * @return bool
	 */
	public static function isTargetLargerThanFile( &$file, $targetMaxSize ) {
		$maxSize = self::getMaxSize( $targetMaxSize );
		$sourceWidth = $file->getWidth();
		$sourceHeight = $file->getHeight();
		$sourceAspect = intval( $sourceWidth ) / intval( $sourceHeight );
		if ( $sourceAspect <= $maxSize['aspect'] ) {
			return ( $maxSize['height'] > $sourceHeight );
		} else {
			return ( $maxSize['width'] > $sourceWidth );
		}
	}

	/**
	 * Is the given transcode key the smallest configured transcode for
	 * its video codec?
	 */
	public static function isSmallestTranscodeForCodec( $transcodeKey ) {
		global $wgEnabledTranscodeSet;

		$settings = self::$derivativeSettings[$transcodeKey];
		$vcodec = $settings['videoCodec'];
		$maxSize = self::getMaxSize( $settings['maxSize'] );

		foreach ( $wgEnabledTranscodeSet as $tKey ) {
			$tsettings = self::$derivativeSettings[$tKey];
			if ( $tsettings['videoCodec'] === $vcodec ) {
				$tmaxSize = self::getMaxSize( $tsettings['maxSize'] );
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
	 * @param $targetMaxSize string
	 * @return array
	 */
	public static function getMaxSize( $targetMaxSize ) {
		$maxSize = [];
		$targetMaxSize = explode( 'x', $targetMaxSize );
		$maxSize['width'] = intval( $targetMaxSize[0] );
		if ( count( $targetMaxSize ) == 1 ) {
			$maxSize['height'] = intval( $targetMaxSize[0] );
		} else {
			$maxSize['height'] = intval( $targetMaxSize[1] );
		}
		// check for zero size ( audio )
		if ( $maxSize['width'] === 0 || $maxSize['height'] == 0 ) {
			return 0;
		}
		$maxSize['aspect'] = $maxSize['width'] / $maxSize['height'];
		return $maxSize;
	}
}
