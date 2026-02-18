<?php
/**
 * Base class for streaming segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use LogicException;

/**
 * Base class for reading a media file, segmenting it, and writing
 * out an HLS playlist for that specific track file.
 *
 * The HLS extended .m3u8 playlist format is described in
 * informative RFC 8216.
 */
abstract class Segmenter {

	/** @var array<int|string,array> */
	protected array $segments;

	public function __construct(
		protected readonly string $filename,
		?array $segments = null,
	) {
		$this->segments = $segments ?? [];
		if ( !$this->segments ) {
			$this->parse();
		}
	}

	/**
	 * Fill the segments from the underlying file
	 */
	abstract protected function parse(): void;

	/**
	 * Consolidate adjacent segments to approach the target segment length.
	 */
	public function consolidate( float $target ): void {
		$out = [];
		$n = count( $this->segments );
		$init = $this->segments['init'] ?? null;
		if ( $init ) {
			$n--;
			$out['init'] = $init;
		}
		if ( $n <= 1 ) {
			return;
		}

		$i = 0;
		while ( $i < $n ) {
			$segment = $this->segments[$i++];
			$start = $segment['start'];
			$size = $segment['size'];
			$timestamp = $segment['timestamp'];
			$duration = $segment['duration'];

			// Append segments until we get close
			while ( $i < $n && $duration < $target ) {
				$segment = $this->segments[$i];
				$total = $duration + $segment['duration'];
				if ( $total >= $target ) {
					$after = $total - $target;
					$before = $target - $duration;
					if ( $before < $after ) {
						// Break segment early
						break;
					}
				}
				$duration += $segment['duration'];
				$size += $segment['size'];
				$i++;
			}

			// Save out a segment
			$out[] = [
				'start' => $start,
				'size' => $size,
				'timestamp' => $timestamp,
				'duration' => $duration,
			];
		}
		$this->segments = $out;
	}

	/**
	 * Modify the media file and segments in-place to insert any
	 * tweaks needed for the file to stream correctly.
	 *
	 * This is used by MP3Segmenter to insert ID3 timestamps.
	 */
	public function rewrite(): void {
		// no-op in default; fragmented .mp4 can be left as-is
	}

	public function playlist( float $target, string $filename ): string {
		$url = wfUrlencode( $filename );

		$segments = [];
		foreach ( $this->segments as $i => $segment ) {
			if ( $i === 'init' ) {
				array_unshift( $segments,
					"#EXT-X-MAP:URI=\"$url\",BYTERANGE=\"{$segment['size']}@{$segment['start']}\""
				);
			} else {
				array_push( $segments,
					"#EXTINF:{$segment['duration']},",
					"#EXT-X-BYTERANGE:{$segment['size']}@{$segment['start']}",
					$url
				);
			}
		}

		return implode( "\n", [
			'#EXTM3U',
			'#EXT-X-VERSION:7',
			"#EXT-X-TARGETDURATION:$target",
			'#EXT-MEDIA-SEQUENCE:0',
			'#EXT-PLAYLIST-TYPE:VOD',
			...$segments,
			'#EXT-X-ENDLIST',
		] );
	}

	public static function segment( string $filename ): Segmenter {
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		return match ( $ext ) {
			'mp3' => new MP3Segmenter( $filename ),
			'mp4',
			'm4v',
			'm4a',
			'mov',
			'3gp' => new MP4Segmenter( $filename ),
			default => throw new LogicException( "Unexpected streaming file extension $ext" )
		};
	}
}
