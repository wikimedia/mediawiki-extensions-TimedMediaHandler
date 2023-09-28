<?php
/**
 * Multivariant playlist generator
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

/**
 * Generator for HLS multivariant playlists, which refer to
 * various tracks of different types/features/languages/etc
 *
 * The client then selects from available video tracks, and
 * picks matching audio.
 *
 * The HLS extended .m3u8 playlist format is described in
 * informative RFC 8216.
 */
class Multivariant {
	private string $filename;
	private array $tracks;

	public static function isStreamingAudio( array $options ): bool {
		$streaming = $options['streaming'] ?? '';
		$novideo = $options['novideo'] ?? null;
		return $streaming === 'hls' && $novideo;
	}

	public static function isStreamingVideo( array $options ): bool {
		$streaming = $options['streaming'] ?? '';
		$noaudio = $options['noaudio'] ?? null;
		return $streaming === 'hls' && $noaudio;
	}

	/**
	 * Internal map of codec string types we use to HLS-friendly
	 * MPEG-4 style codec name mappings.
	 * @var array
	 */
	private static $hlsCodecMap = [
		'opus' => 'Opus',
	];

	public static function hlsCodec( array $options ): string {
		$type = $options['type'] ?? '';
		$matches = [];
		if ( preg_match( '/^\w+\/\w+;(?:\s*)codecs="(.*?)"/', $type, $matches ) ) {
			// Warning: assumes a single track, single codec for streaming!
			$codec = $matches[1];
			return self::$hlsCodecMap[ $codec ] ?? $codec;
		}
		if ( $type === 'audio/mpeg' ) {
			// MPEG-1 layer 3
			return 'mp4a.6b';
		}
		throw new Exception( "Invalid streaming codec definition for type: $type" );
	}

	/**
	 * Validates and quotes a string for an extended m3u8
	 * attribute list.
	 *
	 * Per RFC 8216 4.2 Attribute Lists
	 * https://datatracker.ietf.org/doc/html/rfc8216#section-4.2
	 *
	 * quoted-string: a string of characters within a pair of double
	 * quotes (0x22).  The following characters MUST NOT appear in a
	 * quoted-string: line feed (0xA), carriage return (0xD), or double
	 * quote (0x22).  Quoted-string AttributeValues SHOULD be constructed
	 * so that byte-wise comparison is sufficient to test two quoted-
	 * string AttributeValues for equality.  Note that this implies case-
	 * sensitive comparison.
	 *
	 * Note there is no provision given for escaping the forbidden chars;
	 * rather than throwing an exception we'll simply strip them. The most
	 * likely place this is to come up is translations of language or
	 * format names.
	 */
	public static function quote( string $val ): string {
		$val = str_replace(
			[ "\r\n", "\r", "\n", "\"" ],
			[ " ", " ", " ", "'" ],
			$val
		);
		return "\"$val\"";
	}

	private static function m3uLine( string $type, array $opts ): string {
		$items = [];
		foreach ( $opts as $key => $val ) {
			$items[] = "$key=$val";
		}
		return "#$type:" . implode( ",", $items );
	}

	public function __construct( string $filename, array $tracks ) {
		$this->filename = $filename;
		$this->tracks = $tracks;
	}

	/**
	 * @return string m3u8 output
	 */
	public function playlist(): string {
		$out = [];
		$out[] = '#EXTM3U';

		$audio = [];
		foreach ( $this->tracks as $key ) {
			$options = WebVideoTranscode::$derivativeSettings[$key] ?? [];
			if ( self::isStreamingAudio( $options ) ) {
				$codec = self::hlsCodec( $options );
				$audio[$key] = $codec;
				// max ?
				$channels = strval( $options['channels'] ?? 2 );
				$audioFile = wfUrlencode( "{$this->filename}.{$key}.m3u8" );

				$name = wfMessage( 'timedmedia-derivative-' . $key )->text();

				$out[] = $this->m3uLine( 'EXT-X-MEDIA', [
					'TYPE' => 'AUDIO',
					'GROUP-ID' => self::quote( $key ),
					'NAME' => self::quote( $name ),
					'AUTOSELECT' => 'YES',
					'DEFAULT' => 'YES',
					'CHANNELS' => self::quote( $channels ),
					'URI' => self::quote( $audioFile ),
				] );
			}
		}

		foreach ( $this->tracks as $key ) {
			$options = WebVideoTranscode::$derivativeSettings[$key] ?? [];
			if ( self::isStreamingVideo( $options ) ) {
				$codec = self::hlsCodec( $options );
				$bandwidth = WebVideoTranscode::expandRate( $options['videoBitrate'] ?? '0' );
				$resolution = $options['maxSize'] ?? (
					( $options['width'] ?? '0' ) .
					'x' .
					( $options['height'] ?? '0' )
				);
				// max
				$videoFile = wfUrlencode( "{$this->filename}.{$key}.m3u8" );

				$line = [
					'BANDWIDTH' => $bandwidth,
					'RESOLUTION' => $resolution,
				];
				if ( count( $audio ) ) {
					foreach ( $audio as $audioKey => $audioCodec ) {
						$line['CODECS'] = self::quote( "$codec,$audioCodec" );
						$line['AUDIO'] = self::quote( $audioKey );
						$out[] = self::m3uLine( 'EXT-X-STREAM-INF', $line );
						$out[] = $videoFile;
					}
				} else {
					$line['CODECS'] = self::quote( $codec );
					$out[] = self::m3uLine( 'EXT-X-STREAM-INF', $line );
					$out[] = $videoFile;
				}
			}
		}

		return implode( "\n", $out );
	}
}
