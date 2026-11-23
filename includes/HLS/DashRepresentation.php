<?php
/**
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

/**
 * One Representation (track) of an MPEG-DASH manifest, with its segment list
 */
class DashRepresentation {
	/**
	 * @param string $id Representation ID
	 * @param string $contentType "audio" or "video"
	 * @param string $codecs RFC 6381 codecs string, e.g. "vp09.00.20.08"
	 * @param int|null $width
	 * @param int|null $height
	 * @param string|null $frameRate e.g. "30/1" or "30000/1001"
	 * @param int|null $channels Number of audio channels
	 * @param string $initialization File name of the initialization segment
	 * @param array<array{0:string,1:float}> $segments File name and duration in seconds
	 *   of each media segment
	 */
	public function __construct(
		public readonly string $id,
		public readonly string $contentType,
		public readonly string $codecs,
		public readonly ?int $width,
		public readonly ?int $height,
		public readonly ?string $frameRate,
		public readonly ?int $channels,
		public readonly string $initialization,
		public readonly array $segments,
	) {
	}

	public function isVideo(): bool {
		return $this->contentType === 'video';
	}

	public function isAudio(): bool {
		return $this->contentType === 'audio';
	}

	/**
	 * @return string[] File names of the initialization segment and all media segments
	 */
	public function getFileNames(): array {
		return array_merge( [ $this->initialization ], array_column( $this->segments, 0 ) );
	}
}
