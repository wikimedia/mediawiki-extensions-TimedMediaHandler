<?php
/**
 * Multivariant playlist generator
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use RuntimeException;

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
	private const CODEC_JPEG = 'jpeg';
	private const CODEC_MPEG4 = 'mp4v.20.5';
	private const CODEC_MP3  = 'mp4a.6b';
	private const CODEC_OPUS = 'Opus';
	private const MIME_MP3 = 'audio/mpeg';

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
	private static array $hlsCodecMap = [
		'opus' => self::CODEC_OPUS,
	];

	public static function hlsCodec( array $options ): string {
		$type = $options['type'] ?? '';
		$matches = [];
		if ( preg_match( '/^\w+\/\w+;\s*codecs="(.*?)"/', $type, $matches ) ) {
			// Warning: assumes a single track, single codec for streaming!
			$codec = $matches[1];
			return self::$hlsCodecMap[ $codec ] ?? $codec;
		}
		if ( $type === self::MIME_MP3 ) {
			// MPEG-1 layer 3
			return self::CODEC_MP3;
		}
		throw new RuntimeException( "Invalid streaming codec definition for type: $type" );
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

	public function __construct(
		private readonly string $filename,
		private readonly array $tracks,
	) {
	}

	/**
	 * @return string m3u8 output
	 */
	public function playlist(): string {
		$out = [ '#EXTM3U' ];

		$audio = [];
		foreach ( $this->tracks as $key ) {
			$options = WebVideoTranscode::$derivativeSettings[$key] ?? [];
			if ( !self::isStreamingAudio( $options ) ) {
				continue;
			}
			$codec = self::hlsCodec( $options );
			$audio[$key] = $codec;
			// max ?
			$channels = (string)( $options['channels'] ?? 2 );
			$audioFile = wfUrlencode( "$this->filename.$key.m3u8" );

			$name = wfMessage( 'timedmedia-derivative-' . $key )->text();

			$out[] = self::m3uLine( 'EXT-X-MEDIA', [
				'TYPE' => 'AUDIO',
				'GROUP-ID' => self::quote( $key ),
				'NAME' => self::quote( $name ),
				'AUTOSELECT' => 'YES',
				'DEFAULT' => 'YES',
				'CHANNELS' => self::quote( $channels ),
				'URI' => self::quote( $audioFile ),
			] );
		}

		foreach ( $this->tracks as $key ) {
			$options = WebVideoTranscode::$derivativeSettings[$key] ?? [];
			if ( !self::isStreamingVideo( $options ) ) {
				continue;
			}
			$codec = self::hlsCodec( $options );
			$bandwidth = WebVideoTranscode::expandRate( $options['videoBitrate'] ?? '0' );
			$resolution = $options['maxSize'] ?? (
				( $options['width'] ?? '0' ) .
				'x' .
				( $options['height'] ?? '0' )
			);
			// max
			$videoFile = wfUrlencode( "$this->filename.$key.m3u8" );

			$base = [
				'BANDWIDTH' => $bandwidth,
				'RESOLUTION' => $resolution,
			];
			if ( count( $audio ) ) {
				foreach ( $audio as $audioKey => $audioCodec ) {
					$line = $base;
					if ( !( $codec === self::CODEC_JPEG || $codec === self::CODEC_MPEG4 )
						|| ( $audioCodec !== self::CODEC_MP3 )
					) {
						// Backwards-compatibility hack for iOS 10-15
						// Until iOS 16, the system HLS player was very picky
						// about what codecs you passed in for filtering even
						// if it would play several like jpeg, h263, and mp4v
						// that it didn't allow listing.
						// iOS 16 and later allow these and vp09 if they're
						// supported by the system.
						// Our higher-resolution better-bandwidth VP9 tracks
						// will always take precedence on newer, supporting
						// devices.
						$line['CODECS'] = self::quote( "$codec,$audioCodec" );
					}
					$line['AUDIO'] = self::quote( $audioKey );
					$out[] = self::m3uLine( 'EXT-X-STREAM-INF', $line );
					$out[] = $videoFile;
				}
			} else {
				$line = $base;
				if ( !( $codec === self::CODEC_JPEG || $codec === self::CODEC_MPEG4 ) ) {
					// Backwards-compatibility hack for iOS 10-15, see above.
					$line['CODECS'] = self::quote( $codec );
				}
				$out[] = self::m3uLine( 'EXT-X-STREAM-INF', $line );
				$out[] = $videoFile;
			}
		}
		return implode( "\n", $out );
	}
}
