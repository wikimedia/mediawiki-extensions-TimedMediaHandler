<?php
/**
 * Report on number and size of transcodes.
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\MediaWikiServices;

class TranscodeReport extends Maintenance {

	private $detail = false;
	private $histogram = false;
	private $outliers = false;

	private $count = [];
	private $duration = [];
	private $size = [];

	// Don't count files claiming longer than 12hr duration
	// These are (almost?) always broken files.
	private $insaneDuration = 12 * 3600;

	// max size for histogram breakdowns by res
	private $max = [
		120 => 0.125 * 1000 * 1000,
		160 => 0.2 * 1000 * 1000,
		180 => 0.25 * 1000 * 1000,
		240 => 0.5 * 1000 * 1000,
		360 => 0.75 * 1000 * 1000,
		480 => 1.25 * 1000 * 1000,
		720 => 2.5 * 1000 * 1000,
		1080 => 5 * 1000 * 1000,
		1440 => 10 * 1000 * 1000,
		2160 => 20 * 1000 * 1000,
	];
	private $buckets = 25;
	private $histo = [];

	public function __construct() {
		parent::__construct();
		$this->addOption( "audio", "process audio files (defaults to all media types)", false, false );
		$this->addOption( "video", "process video files (defaults to all media types)", false, false );
		$this->addOption( "detail", "produce a detailed CSV report of all files", false, false );
		$this->addOption( "histogram",
			"produce a breakdown of bitrates for each video transcode resolution", false, false );
		$this->addOption( "outliers", "include outliers with 0 or very long durations", false, false );
		$this->addDescription( "build and show a CSV report on transcode derivative files." );
		$this->requireExtension( 'TimedMediaHandler' );
	}

	public function execute() {
		$dbr = wfGetDB( DB_REPLICA );
		$types = [];
		if ( $this->hasOption( 'audio' ) ) {
			$types[] = 'AUDIO';
		}
		if ( $this->hasOption( 'video' ) || $this->hasOption( 'histogram' ) ) {
			$types[] = 'VIDEO';
		}
		$this->detail = $this->hasOption( 'detail' );
		$this->histogram = $this->hasOption( 'histogram' );
		$this->outliers = $this->hasOption( 'outliers' );

		if ( $this->detail ) {
			$this->output( "file\tduration\tkey\tsize\tbitrate\n" );
		} elseif ( $this->histogram ) {
			$this->output( "bitrate histogram per key\n" );
		} else {
			$this->output( "key\tcount\ttotal duration\ttotal size\tavg bitrate\n" );
		}

		if ( !$types ) {
			// Default to all if none specified
			$types = [ 'AUDIO', 'VIDEO' ];
		}
		$where = [ 'img_media_type' => $types ];
		$opts = [ 'ORDER BY' => 'img_media_type,img_name' ];
		$res = $dbr->select( 'image', [ 'img_name' ], $where, __METHOD__, $opts );
		$localRepo = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->img_name, NS_FILE );
			$file = $localRepo->newFile( $title );
			$handler = $file ? $file->getHandler() : null;
			if ( $file && $handler && $handler instanceof TimedMediaHandler ) {
				$this->processFile( $file );
			}
		}

		$keys = array_keys( $this->count );
		natsort( $keys );
		if ( $this->detail ) {
			// Already printed out detail as we found files.
		} elseif ( $this->histogram ) {
			// Print out a histogram of sizes per key.
			foreach ( $keys as $key ) {
				$this->output( "\n$key\n" );
				if ( isset( $this->histo[$key] ) ) {
					$max = array_sum( $this->histo[$key] );
					$minBucket = min( array_keys( $this->histo[$key] ) );
					$maxBucket = max( array_keys( $this->histo[$key] ) );
					for ( $bucket = $minBucket; $bucket < $maxBucket; $bucket++ ) {
						$res = intval( $key );
						$a = floor( $bucket * $this->max[$res] / $this->buckets );
						$b = floor( ( $bucket + 1 ) * $this->max[$res] / $this->buckets ) - 1;

						global $wgLang;
						$aa = str_pad( $wgLang->formatBitrate( (int)$a ), 10, " ", STR_PAD_LEFT );
						$bb = str_pad( $wgLang->formatBitrate( (int)$b ), 10, " ", STR_PAD_LEFT );
						$legend = "$aa - $bb";

						$val = $this->histo[$key][$bucket] ?? 0;
						$bar = str_repeat( '*', ceil( $val * 72 / $max ) );

						$this->output( $legend . " : " . $bar . "\n" );
					}
				}
			}
		} else {
			foreach ( $keys as $key ) {
				$count = $this->count[$key];
				$duration = $this->duration[$key];
				$size = $this->size[$key];
				$bitrate = $size * 8.0 / $duration;
				$this->output( "$key\t$count\t$duration\t$size\t$bitrate\n" );
			}
		}
	}

	private function processFile( File $file ) {
		$dbw = wfGetDB( DB_MASTER );

		// Transcode table doesn't carry the file size, but does carry the final bitrate.
		$handler = $file->getHandler();
		$duration = $handler->getLength( $file );

		if ( !$this->outliers ) {
			if ( $duration == 0 ) {
				// ignore outliers with 0 duration
				// found a lot of these in .ogvs imported from open science data
				return;
			}
			if ( $duration > $this->insaneDuration ) {
				// ignore outliers with super-long durations
				// these are mostly weird trims of long live videos
				return;
			}
		}

		$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
		foreach ( $state as $key => $item ) {
			if ( $item && $item['time_success'] ) {
				$name = $file->getName();
				$bitrate = intval( $item['final_bitrate'] );
				$size = intval( $bitrate * $duration / 8 );

				if ( $this->detail ) {
					$this->output( "$name\t$duration\t$key\t$size\t$bitrate\n" );
				} else {
					if ( !isset( $this->count[$key] ) ) {
						$this->count[$key] = 0;
					}
					if ( !isset( $this->duration[$key] ) ) {
						$this->duration[$key] = 0.0;
					}
					if ( !isset( $this->size[$key] ) ) {
						$this->size[$key] = 0;
					}

					$this->count[$key]++;
					$this->duration[$key] += $duration;
					$this->size[$key] += $size;

					if ( $this->histogram ) {
						$this->recordForHistogram( $key, $duration, $bitrate );
					}
				}
			}
		}
	}

	private function bucket( $key, $bitrate ) {
		$res = intval( $key );
		$target = ( $bitrate / $this->max[$res] ) * $this->buckets;
		if ( $target < 0 ) {
			return 0;
		}
		if ( $target >= $this->buckets ) {
			return $this->buckets - 1;
		}
		return intval( floor( $target ) );
	}

	private function recordForHistogram( $key, $duration, $bitrate ) {
		if ( !isset( $this->histo[$key] ) ) {
			$this->histo[$key] = [];
		}

		$bucket = $this->bucket( $key, $bitrate );

		if ( !isset( $this->histo[$key][$bucket] ) ) {
			$this->histo[$key][$bucket] = $duration;
		} else {
			$this->histo[$key][$bucket] += $duration;
		}
	}
}

$maintClass = TranscodeReport::class; // Tells it to run the class
require_once RUN_MAINTENANCE_IF_MAIN;
