<?php
/**
 * HLS playlists for the segments of an MPEG-DASH manifest
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Closure;
use RuntimeException;

/**
 * Generates HLS playlists (RFC 8216) that play the fragmented MP4 segments of
 * an MPEG-DASH manifest, so one set of segments serves dash.js and native HLS
 * players.
 *
 * ffmpeg's own -hls_playlist output is not used: it leaves out CODECS for
 * some setups, writes wrong BANDWIDTH values and adds wall-clock
 * EXT-X-PROGRAM-DATE-TIME tags.
 */
class DashToHls {
	/** EXT-X-MAP outside of I-frame playlists needs version 6 */
	private const VERSION = 6;

	private const AUDIO_GROUP = 'audio';

	/**
	 * @param DashRepresentation[] $representations
	 * @param Closure(DashRepresentation):string $mediaPlaylistName File name of the
	 *   media playlist of a representation, relative to the multivariant playlist
	 * @param array<string,int> $segmentSizes Size in bytes of each segment file
	 */
	public function __construct(
		private readonly array $representations,
		private readonly Closure $mediaPlaylistName,
		private readonly array $segmentSizes,
	) {
	}

	/**
	 * @return array<string,string> Media playlist file name => playlist
	 */
	public function mediaPlaylists(): array {
		$playlists = [];
		foreach ( $this->representations as $rep ) {
			$playlists[ ( $this->mediaPlaylistName )( $rep ) ] = $this->mediaPlaylist( $rep );
		}
		return $playlists;
	}

	public function mediaPlaylist( DashRepresentation $rep ): string {
		if ( !$rep->segments ) {
			throw new RuntimeException( "No segments in representation '{$rep->id}'" );
		}
		// RFC 8216 4.3.3.1: each EXTINF rounded to the nearest integer must not exceed it
		$target = max( 1, (int)round( max( array_column( $rep->segments, 1 ) ) ) );
		$out = [
			'#EXTM3U',
			'#EXT-X-VERSION:' . self::VERSION,
			'#EXT-X-TARGETDURATION:' . $target,
			'#EXT-X-PLAYLIST-TYPE:VOD',
			'#EXT-X-INDEPENDENT-SEGMENTS',
			'#EXT-X-MAP:URI=' . Multivariant::quote( $rep->initialization ),
		];
		foreach ( $rep->segments as [ $name, $duration ] ) {
			$out[] = sprintf( '#EXTINF:%.3f,', $duration );
			$out[] = $name;
		}
		$out[] = '#EXT-X-ENDLIST';
		return implode( "\n", $out ) . "\n";
	}

	public function multivariantPlaylist(): string {
		$audioReps = array_filter( $this->representations, static fn ( $rep ) => $rep->isAudio() );
		$videoReps = array_filter( $this->representations, static fn ( $rep ) => $rep->isVideo() );

		$out = [
			'#EXTM3U',
			'#EXT-X-VERSION:' . self::VERSION,
			'#EXT-X-INDEPENDENT-SEGMENTS',
		];

		$audioCodecs = [];
		$maxAudio = null;
		$maxAudioBitRate = null;
		$default = true;
		foreach ( $audioReps as $rep ) {
			$audioCodecs[] = self::hlsCodec( $rep->codecs );
			$attrs = [
				'TYPE' => 'AUDIO',
				'GROUP-ID' => Multivariant::quote( self::AUDIO_GROUP ),
				'NAME' => Multivariant::quote( "audio-{$rep->id}" ),
				'DEFAULT' => $default ? 'YES' : 'NO',
				'AUTOSELECT' => 'YES',
			];
			if ( $rep->channels ) {
				$attrs['CHANNELS'] = Multivariant::quote( (string)$rep->channels );
			}
			$attrs['URI'] = Multivariant::quote( ( $this->mediaPlaylistName )( $rep ) );
			$out[] = Multivariant::m3uLine( 'EXT-X-MEDIA', $attrs );
			$default = false;
			// A variant's bandwidth includes the largest audio track it may be played with
			$audioBitRate = $this->bitRates( $rep )[0];
			if ( $maxAudioBitRate === null || $audioBitRate > $maxAudioBitRate ) {
				$maxAudioBitRate = $audioBitRate;
				$maxAudio = $rep;
			}
		}
		$audioCodecs = array_values( array_unique( $audioCodecs ) );

		foreach ( $videoReps as $rep ) {
			$attrs = $this->bandwidthAttributes( $maxAudio ? [ $rep, $maxAudio ] : [ $rep ] );
			$attrs['CODECS'] = Multivariant::quote(
				implode( ',', array_merge( [ self::hlsCodec( $rep->codecs ) ], $audioCodecs ) ) );
			if ( $rep->width && $rep->height ) {
				$attrs['RESOLUTION'] = "{$rep->width}x{$rep->height}";
			}
			$frameRate = self::frameRate( $rep->frameRate );
			if ( $frameRate !== null ) {
				$attrs['FRAME-RATE'] = $frameRate;
			}
			if ( $audioReps ) {
				$attrs['AUDIO'] = Multivariant::quote( self::AUDIO_GROUP );
			}
			$out[] = Multivariant::m3uLine( 'EXT-X-STREAM-INF', $attrs );
			$out[] = ( $this->mediaPlaylistName )( $rep );
		}
		return implode( "\n", $out ) . "\n";
	}

	/**
	 * @param DashRepresentation[] $reps Tracks that play together
	 * @return array<string,int> BANDWIDTH and AVERAGE-BANDWIDTH
	 */
	private function bandwidthAttributes( array $reps ): array {
		$peak = 0;
		$average = 0;
		foreach ( $reps as $rep ) {
			[ $repPeak, $repAverage ] = $this->bitRates( $rep );
			$peak += $repPeak;
			$average += $repAverage;
		}
		return [ 'BANDWIDTH' => $peak, 'AVERAGE-BANDWIDTH' => $average ];
	}

	/**
	 * Peak and average segment bit rates (RFC 8216 4.3.4.2) from the segment sizes
	 *
	 * @return array{0:int,1:int}
	 */
	private function bitRates( DashRepresentation $rep ): array {
		$peak = 0;
		$bits = 0;
		$duration = 0;
		$last = count( $rep->segments ) - 1;
		foreach ( $rep->segments as $i => [ $name, $segmentDuration ] ) {
			if ( !isset( $this->segmentSizes[$name] ) ) {
				throw new RuntimeException( "Unknown size of segment '$name'" );
			}
			$segmentBits = $this->segmentSizes[$name] * 8;
			$bits += $segmentBits;
			$duration += $segmentDuration;
			// The last segment can be a fraction of a second long, so its
			// rate is noise. Skip it unless it is the only one.
			if ( $i < $last || $last === 0 ) {
				$peak = max( $peak, $segmentBits / $segmentDuration );
			}
		}
		return [ (int)ceil( $peak ), (int)ceil( $bits / $duration ) ];
	}

	private static function hlsCodec( string $codec ): string {
		return Multivariant::HLS_CODEC_MAP[$codec] ?? $codec;
	}

	/**
	 * @param string|null $frameRate DASH frame rate, e.g. "30" or "30000/1001"
	 * @return string|null HLS decimal-floating-point
	 */
	private static function frameRate( ?string $frameRate ): ?string {
		if ( $frameRate === null || !preg_match( '/^(\d+)(?:\/(\d+))?$/', $frameRate, $m ) ) {
			return null;
		}
		$denominator = (int)( $m[2] ?? 1 );
		return $denominator ? sprintf( '%.3f', (int)$m[1] / $denominator ) : null;
	}
}
