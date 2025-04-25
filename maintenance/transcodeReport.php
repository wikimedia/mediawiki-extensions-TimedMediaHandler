<?php
/**
 * Report on number and size of transcodes.
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

use MediaWiki\FileRepo\File\File;
use MediaWiki\MainConfigNames;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;
use MediaWiki\Title\Title;

class TranscodeReport extends Maintenance {

	private bool $detail = false;
	private bool $histogram = false;
	private bool $outliers = false;

	/** @var int[] */
	private array $count = [];
	/** @var float[] */
	private array $duration = [];
	/** @var int[] */
	private array $size = [];

	/**
	 * @var int Don't count files claiming longer than 12hr duration
	 * These are (almost?) always broken files.
	 * Units in seconds.
	 */
	private const REALLY_LONG = 12 * 3600;

	/** @var float[] max size for histogram breakdowns by res */
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
	private int $buckets = 25;
	private array $histo = [];

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
		$dbr = $this->getServiceContainer()->getDBLoadBalancerFactory()->getReplicaDatabase();
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
		$migrationStage = $this->getConfig()->get( MainConfigNames::FileSchemaMigrationStage );
		if ( $migrationStage & SCHEMA_COMPAT_READ_OLD ) {
			$queryBuilder = $dbr->newSelectQueryBuilder()
				->select( 'img_name' )
				->from( 'image' )
				->where( [ 'img_media_type' => $types ] )
				->orderBy( [ 'img_media_type', 'img_name' ] );
		} else {
			$queryBuilder = $dbr->newSelectQueryBuilder()
				->field( 'file_name', 'img_name' )
				->from( 'file' )
				->join( 'filetypes', null, 'file_type = ft_id' )
				->where( [ 'ft_media_type' => $types ] )
				->orderBy( [ 'file_type', 'file_name' ] );
		}
		$res = $queryBuilder->caller( __METHOD__ )->fetchResultSet();

		$localRepo = $this->getServiceContainer()->getRepoGroup()->getLocalRepo();
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
						$res = (int)$key;
						$a = floor( $bucket * $this->max[$res] / $this->buckets );
						$b = floor( ( $bucket + 1 ) * $this->max[$res] / $this->buckets ) - 1;

						$lang = $this->getServiceContainer()->getLanguageFactory()->getLanguage( 'en' );
						$aa = str_pad( $lang->formatBitrate( (int)$a ), 10, " ", STR_PAD_LEFT );
						$bb = str_pad( $lang->formatBitrate( (int)$b ), 10, " ", STR_PAD_LEFT );
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

	private function processFile( File $file ): void {
		$dbw = $this->getServiceContainer()->getDBLoadBalancerFactory()->getPrimaryDatabase();

		// Transcode table doesn't carry the file size, but does carry the final bitrate.
		$handler = $file->getHandler();
		$duration = $handler->getLength( $file );

		if ( !$this->outliers ) {
			if ( $duration === 0 ) {
				// ignore outliers with 0 duration
				// found a lot of these in .ogvs imported from open science data
				return;
			}
			if ( $duration > self::REALLY_LONG ) {
				// ignore outliers with super-long durations
				// these are mostly weird trims of long live videos
				return;
			}
		}

		$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
		foreach ( $state as $key => $item ) {
			if ( $item && $item['time_success'] ) {
				$name = $file->getName();
				$bitrate = (int)$item['final_bitrate'];
				$size = (int)( $bitrate * $duration / 8 );

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

	/**
	 * @param string|int $key
	 * @param int $bitrate
	 */
	private function bucket( $key, int $bitrate ): int {
		$res = (int)$key;
		$target = ( $bitrate / $this->max[$res] ) * $this->buckets;
		if ( $target < 0 ) {
			return 0;
		}
		if ( $target >= $this->buckets ) {
			return $this->buckets - 1;
		}
		return (int)floor( $target );
	}

	/**
	 * @param string|int $key
	 * @param float $duration
	 * @param int $bitrate
	 */
	private function recordForHistogram( $key, float $duration, int $bitrate ): void {
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

// Tells it to run the class
$maintClass = TranscodeReport::class;
require_once RUN_MAINTENANCE_IF_MAIN;
