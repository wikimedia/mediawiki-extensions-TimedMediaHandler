<?php
/**
 * MPEG-DASH manifest reader
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;

/**
 * Reads the static, single-period manifests that ffmpeg's dash muxer writes
 * with -use_template 1 -use_timeline 1, and lists the segments of each
 * Representation. Other manifest layouts are rejected.
 */
class DashManifest {
	private const NS = 'urn:mpeg:dash:schema:mpd:2011';

	/**
	 * Segment file names become storage paths and playlist URIs, so allow
	 * only plain names in the same directory.
	 */
	private const SAFE_NAME = '/^[A-Za-z0-9][A-Za-z0-9._-]*$/';

	/**
	 * @param string $xml
	 * @return DashRepresentation[]
	 * @throws RuntimeException
	 */
	public static function parse( string $xml ): array {
		$doc = new DOMDocument();
		// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ( !@$doc->loadXML( $xml, LIBXML_NONET ) ) {
			throw new RuntimeException( 'Invalid MPEG-DASH manifest' );
		}
		$xpath = new DOMXPath( $doc );
		$xpath->registerNamespace( 'd', self::NS );

		$periods = $xpath->query( '/d:MPD/d:Period' );
		if ( $periods->length !== 1 ) {
			throw new RuntimeException( 'MPEG-DASH manifest must have exactly one Period' );
		}

		$representations = [];
		/** @var DOMElement $set */
		foreach ( $xpath->query( 'd:AdaptationSet', $periods->item( 0 ) ) as $set ) {
			/** @var DOMElement $rep */
			foreach ( $xpath->query( 'd:Representation', $set ) as $rep ) {
				$representations[] = self::parseRepresentation( $xpath, $set, $rep );
			}
		}
		return $representations;
	}

	private static function parseRepresentation(
		DOMXPath $xpath,
		DOMElement $set,
		DOMElement $rep
	): DashRepresentation {
		// Most attributes may be on the Representation or inherited from the AdaptationSet
		$attr = static function ( string $name ) use ( $set, $rep ): ?string {
			foreach ( [ $rep, $set ] as $element ) {
				if ( $element->hasAttribute( $name ) ) {
					return $element->getAttribute( $name );
				}
			}
			return null;
		};
		$int = static function ( string $name ) use ( $attr ): ?int {
			$value = $attr( $name );
			return $value === null ? null : (int)$value;
		};

		$id = $rep->getAttribute( 'id' );
		$contentType = $attr( 'contentType' ) ?? explode( '/', $attr( 'mimeType' ) ?? '' )[0];
		if ( $contentType !== 'audio' && $contentType !== 'video' ) {
			throw new RuntimeException( "Unsupported content type '$contentType' in representation '$id'" );
		}

		$channels = null;
		$channelConfig = $xpath->query( 'd:AudioChannelConfiguration', $rep )->item( 0 )
			?? $xpath->query( 'd:AudioChannelConfiguration', $set )->item( 0 );
		if ( $channelConfig instanceof DOMElement ) {
			$channels = (int)$channelConfig->getAttribute( 'value' );
		}

		$template = $xpath->query( 'd:SegmentTemplate', $rep )->item( 0 )
			?? $xpath->query( 'd:SegmentTemplate', $set )->item( 0 );
		if ( !$template instanceof DOMElement ) {
			throw new RuntimeException( "No SegmentTemplate in representation '$id'" );
		}
		$timeline = $xpath->query( 'd:SegmentTimeline/d:S', $template );
		if ( !$timeline->length ) {
			throw new RuntimeException( "No SegmentTimeline in representation '$id'" );
		}

		$timescale = (int)( $template->getAttribute( 'timescale' ) ?: 1 );
		$media = $template->getAttribute( 'media' );
		$initialization = self::fillTemplate( $template->getAttribute( 'initialization' ), $id, 0 );

		$segments = [];
		$number = (int)( $template->getAttribute( 'startNumber' ) ?: 1 );
		/** @var DOMElement $s */
		foreach ( $timeline as $s ) {
			$duration = (int)$s->getAttribute( 'd' );
			$repeat = (int)$s->getAttribute( 'r' );
			if ( $duration <= 0 || $repeat < 0 ) {
				// r="-1" (repeat until the next S or the end) is not written by ffmpeg for VOD
				throw new RuntimeException( "Unsupported SegmentTimeline entry in representation '$id'" );
			}
			for ( $i = 0; $i <= $repeat; $i++ ) {
				$segments[] = [
					self::fillTemplate( $media, $id, $number ),
					(float)( $duration / $timescale ),
				];
				$number++;
			}
		}

		return new DashRepresentation(
			$id,
			$contentType,
			$attr( 'codecs' ) ?? '',
			$int( 'width' ),
			$int( 'height' ),
			$attr( 'frameRate' ),
			$channels,
			$initialization,
			$segments
		);
	}

	/**
	 * Fill in the SegmentTemplate identifiers that ffmpeg uses. Others
	 * ($Time$, $Bandwidth$) are left in place and fail the name check.
	 */
	private static function fillTemplate( string $template, string $id, int $number ): string {
		$name = preg_replace_callback(
			'/\$Number(?:%0(\d+)d)?\$/',
			static fn ( array $m ) => sprintf( '%0' . ( $m[1] ?? '1' ) . 'd', $number ),
			str_replace( '$RepresentationID$', $id, $template )
		);
		if ( !preg_match( self::SAFE_NAME, $name ) ) {
			throw new RuntimeException( "Invalid segment name in MPEG-DASH manifest: $name" );
		}
		return $name;
	}
}
