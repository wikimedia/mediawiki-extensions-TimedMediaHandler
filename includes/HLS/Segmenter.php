<?php
/**
 * Base class for streaming segment readers
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;

/**
 * Base class for reading a media file, segmenting it, and writing
 * out an HLS playlist for that specific track file.
 *
 * The HLS extended .m3u8 playlist format is described in
 * informative RFC 8216.
 */
abstract class Segmenter {

	protected string $filename;
	protected array $segments;

	public function __construct( string $filename, ?array $segments = null ) {
		$this->filename = $filename;
		if ( $segments ) {
			$this->segments = $segments;
		} else {
			$this->segments = [];
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
		$init = $this->segments['init'] ?? false;
		if ( $init ) {
			$n--;
			$out['init'] = $init;
		}
		if ( $n < 2 ) {
			return;
		}

		$first = $this->segments[0];
		$start = $first['start'];
		$size = $first['size'];
		$timestamp = $first['timestamp'];
		$duration = $first['duration'];

		$i = 1;
		while ( $i < $n ) {
			// Append segments until we get close
			while ( $i < $n - 1 && $duration < $target ) {
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

			if ( $i < $n ) {
				$segment = $this->segments[$i];
				$start = $segment['start'];
				$size = $segment['size'];
				$timestamp = $segment['timestamp'];
				$duration = $segment['duration'];
				$i++;
			}
		}
		$out[] = [
			'start' => $start,
			'size' => $size,
			'timestamp' => $timestamp,
			'duration' => $duration,
		];
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
		$lines = [];
		$lines[] = "#EXTM3U";
		$lines[] = "#EXT-X-VERSION:7";
		$lines[] = "#EXT-X-TARGETDURATION:$target";
		$lines[] = "#EXT-MEDIA-SEQUENCE:0";
		$lines[] = "#EXT-PLAYLIST-TYPE:VOD";

		$url = wfUrlencode( $filename );

		$init = $this->segments['init'] ?? false;
		if ( $init ) {
			$lines[] = "#EXT-X-MAP:URI=\"{$url}\",BYTERANGE=\"{$init['size']}@{$init['start']}\"";
		}

		$n = count( $this->segments ) - 1;
		for ( $i = 0; $i < $n; $i++ ) {
			$segment = $this->segments[$i];
			$lines[] = "#EXTINF:{$segment['duration']},";
			$lines[] = "#EXT-X-BYTERANGE:{$segment['size']}@{$segment['start']}";
			$lines[] = "{$url}";
		}

		$lines[] = "#EXT-X-ENDLIST";

		return implode( "\n", $lines );
	}

	public static function segment( string $filename ): Segmenter {
		$ext = strtolower( substr( $filename, strrpos( $filename, '.' ) ) );
		switch ( $ext ) {
			case '.mp3':
				return new MP3Segmenter( $filename );
			case '.mp4':
			case '.m4v':
			case '.m4a':
			case '.mov':
			case '.3gp':
				return new MP4Segmenter( $filename );
			default:
				throw new Exception( "Unexpected streaming file extension $ext" );
		}
	}
}
