<?php

namespace MediaWiki\TimedMediaHandler\WebVideoTranscode;

/**
 * Transcode configuration preset for a derivative.
 */
class TranscodePreset {
	/** @var string|null The MIME type of the transcode (e.g., "video/webm; codecs='vp8, vorbis'"). */
	public ?string $type = null;

	/** @var string|null The maximum dimensions of the video (e.g., "1920x1080"). */
	public ?string $maxSize = null;

	/** @var string|null The target video bitrate (e.g., "500k"). Corresponds to ffmpeg option: -b:v */
	public ?string $videoBitrate = null;

	/** @var string|null The minimum video bitrate (e.g., "300k"). Corresponds to ffmpeg option: -minrate */
	public ?string $minrate = null;

	/** @var string|null The maximum video bitrate (e.g., "800k"). Corresponds to ffmpeg option: -maxrate */
	public ?string $maxrate = null;

	/** @var string|null The constant rate factor for video quality (e.g., "23"). Corresponds to ffmpeg option: -crf */
	public ?string $crf = null;

	/** @var string|null The encoding speed preset (e.g., "fast", "medium", "slow"). Corresponds to ffmpeg option: -preset */
	public ?string $speed = null;

	/** @var string|null Whether two-pass encoding is enabled (e.g., "true"). Corresponds to ffmpeg option: -pass */
	public ?string $twopass = null;

	/** @var string|null The codec used for video encoding (e.g., "vp8", "h264"). Corresponds to ffmpeg option: -c:v */
	public ?string $videoCodec = null;

	/** @var string|null The codec used for audio encoding (e.g., "vorbis", "aac"). Corresponds to ffmpeg option: -c:a */
	public ?string $audioCodec = null;

	/** @var string|null The audio sample rate in Hz (e.g., "48000"). Corresponds to ffmpeg option: -ar */
	public ?string $samplerate = null;

	/** @var string|null The number of audio channels (e.g., "2" for stereo). Corresponds to ffmpeg option: -ac */
	public ?string $channels = null;

	/** @var string|null The target audio bitrate (e.g., "128k"). Corresponds to ffmpeg option: -b:a */
	public ?string $audioBitrate = null;

	/** @var string|null The number of slices for video encoding (used for parallel processing). Corresponds to ffmpeg option: -slices */
	public ?string $slices = null;

	/** @var string|null The number of tile columns for video encoding (used for parallel processing). Corresponds to ffmpeg option: -tile-columns */
	public ?string $tileColumns = null;

	/** @var string|null Whether to disable video in the transcode (e.g., "true"). Corresponds to ffmpeg option: -vn */
	public ?string $novideo = null;

	/** @var string|null Whether the transcode is optimized for streaming (e.g., "true"). Corresponds to ffmpeg option: -movflags */
	public ?string $streaming = null;

	/** @var string|null The minimum number of audio channels required. */
	public ?string $minChannels = null;

	/** @var string|null The maximum frame rate of the video (e.g., "30"). Corresponds to ffmpeg option: -r */
	public ?string $fpsmax = null;

	/** @var string|null Whether to disable audio in the transcode (e.g., "true"). Corresponds to ffmpeg option: -an */
	public ?string $noaudio = null;

	/** @var string|null The width of the video in pixels. Corresponds to ffmpeg option: -vf scale=width. */
	public ?string $width = null;

	/** @var string|null The height of the video in pixels. Corresponds to ffmpeg option: -vf scale=height. */
	public ?string $height = null;

	/** @var string|null Whether to enable intra-frame compression (e.g., "true"). Corresponds to ffmpeg option: -intra */
	public ?string $intraframe = null;

	/** @var string[]|null A list of formats from which the video can be remuxed. */
	public ?array $remuxFrom = null;

	/** @var string|null The quality level of the audio (e.g., "high", "medium", "low"). */
	public ?string $audioQuality = null;

	/** @var string|null Whether to disable upscaling of the video (e.g., "true"). */
	public ?string $noUpscaling = null;

	/** @var string|null The aspect ratio of the video (e.g., "16:9"). Corresponds to ffmpeg option: -aspect */
	public ?string $aspect = null;

	/** @var string|null The frame rate of the video (e.g., "24", "29.97"). Corresponds to ffmpeg option: -r */
	public ?string $framerate = null;

	public function __construct( array $settings ) {
		foreach ( $settings as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
	}
}
