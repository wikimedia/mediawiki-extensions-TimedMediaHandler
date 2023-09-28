<?php
/**
 * .m3u8 playlist generation for HLS (HTTP Live Streaming)
 *
 * @file
 * @ingroup HLS
 */

namespace MediaWiki\TimedMediaHandler\HLS;

use Exception;

/**
 * Read a fragmented MP4/ISO BMFF/QuickTime media file and
 * segment it into the init segment and one per fragment,
 * allowing creation of a streaming playlist.
 *
 * Non-fragmented MP4 is not supported by this reader code
 * at this time.
 *
 * Short fragments may be consolidated together based on
 * a target segment duration with the consolidate() method.
 *
 * Because the relevant ISO standards are not redistributable,
 * artisinal 3rd-party documentation was sourced via web searches
 * and from the FFMPEG libavformat source code.
 */
class MP4Segmenter extends Segmenter {

	private const MOV_TFHD_BASE_DATA_OFFSET = 0x01;
	private const MOV_TFHD_STSD_ID          = 0x02;
	private const MOV_TFHD_DEFAULT_DURATION = 0x08;

	private const MOV_TRUN_DATA_OFFSET        = 0x01;
	private const MOV_TRUN_FIRST_SAMPLE_FLAGS = 0x04;
	private const MOV_TRUN_SAMPLE_DURATION    = 0x100;
	private const MOV_TRUN_SAMPLE_SIZE        = 0x200;
	private const MOV_TRUN_SAMPLE_FLAGS       = 0x400;
	private const MOV_TRUN_SAMPLE_CTS         = 0x800;

	protected function parse(): void {
		$file = fopen( $this->filename, 'rb' );
		$stream = new OwningStreamReader( $file );

		$mp4 = new MP4Reader( $file );
		$eof = null;
		$start = null;
		$timestamp = 0.0;
		$duration = 0.0;
		$timescale = 0;
		$dts = 0;
		$first_pts = 0;
		$max_pts = 0;
		$init = null;

		/*
		Need to:
		- find the end of the moov; everything up to that is the initialization segment
		- https://www.w3.org/TR/mse-byte-stream-format-isobmff/#iso-init-segments
		- find the start of each styp+moof fragment
		- https://www.w3.org/TR/mse-byte-stream-format-isobmff/#iso-media-segments
		- find the start timestamp of each moof fragment
		- find the duration of each moof fragment
		*/
		$ftyp = $mp4->readBox();
		if ( $ftyp->type != 'ftyp' ) {
			throw new Exception( "Invalid MP4/ISO BMFF input file '{$ftyp->type}'" );
		}

		$moov = $mp4->expectBox( 'moov' );
		$trak = $moov->expectBox( 'trak' );
		$mdia = $trak->expectBox( 'mdia' );
		$mdhd = $mdia->expectBox( 'mdhd' );
		$version = $mdhd->read8();
		// flags
		$mdhd->read24();
		if ( $version === 1 ) {
			$skip = $mdhd->read( 16 );
		} else {
			$skip = $mdhd->read( 8 );
		}

		// This bit we actually need!
		$timescale = $mdhd->read32();

		while ( true ) {
			$moof = $mp4->findBox( 'moof' );
			if ( !$moof ) {
				break;
			}
			$start = $moof->start;
			$default_sample_duration = 0;
			$first_pts = 0;
			$max_pts = 0;
			if ( !$init ) {
				$init = [
					'start' => 0,
					'size' => $start,
					'timestamp' => 0.0,
					'duration' => 0.0,
				];
				$this->segments['init'] = $init;
			}
			$traf = $moof->expectBox( 'traf' );
			$tfhd = $traf->expectBox( 'tfhd' );
			// version
			$tfhd->read8();
			$flags = $tfhd->read24();

			$track_id = $tfhd->read32();
			if ( $flags & self::MOV_TFHD_BASE_DATA_OFFSET ) {
				$tfhd->read64();
			}
			if ( $flags & self::MOV_TFHD_STSD_ID ) {
				$tfhd->read32();
			}
			if ( $flags & self::MOV_TFHD_DEFAULT_DURATION ) {
				$default_sample_duration = $tfhd->read32();
			}

			$trun = $traf->expectBox( 'trun' );
			// version
			$trun->read8();
			$flags   = $trun->read24();
			$entries = $trun->read32();
			if ( $flags & self::MOV_TRUN_DATA_OFFSET ) {
				$trun->read32();
			}
			if ( $flags & self::MOV_TRUN_FIRST_SAMPLE_FLAGS ) {
				$trun->read32();
			}
			for ( $i = 0; $i < $entries; $i++ ) {
				$pts = $dts;
				$sample_duration = $default_sample_duration;
				if ( $flags & self::MOV_TRUN_SAMPLE_DURATION ) {
					$sample_duration = $trun->read32();
				}
				if ( $flags & self::MOV_TRUN_SAMPLE_SIZE ) {
					$trun->read32();
				}
				if ( $flags & self::MOV_TRUN_SAMPLE_FLAGS ) {
					$trun->read32();
				}
				if ( $flags & self::MOV_TRUN_SAMPLE_CTS ) {
					$pts += $trun->read32();
				}
				if ( $i == 0 ) {
					$first_pts = $pts;
				}
				$max_pts = max( $max_pts, $pts + $sample_duration );
				$dts += $sample_duration;
			}
			$mdat = $mp4->expectBox( 'mdat' );
			array_push( $this->segments, [
				'start' => $start,
				'size' => $mdat->end() - $start,
				'timestamp' => $first_pts / $timescale,
				'duration' => ( $max_pts - $first_pts ) / $timescale,
			] );
		}
	}
}
