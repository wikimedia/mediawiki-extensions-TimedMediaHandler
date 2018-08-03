<?php
/**
 * Report on number and size of transcodes.
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

class TranscodeReport extends Maintenance {

	private $detail = false;
	private $count = [];
	private $duration = [];
	private $size = [];

	public function __construct() {
		parent::__construct();
		$this->addOption( "audio", "process audio files (defaults to all media types)", false, false );
		$this->addOption( "video", "process video files (defaults to all media types)", false, false );
		$this->addOption( "detail", "produce a detailed CSV report of all files", false, false );
		$this->mDescription = "build and show a CSV report on transcode derivative files.";
	}

	public function execute() {
		$dbr = wfGetDB( DB_REPLICA );
		$types = [];
		if ( $this->hasOption( 'audio' ) ) {
			$types[] = 'AUDIO';
		}
		if ( $this->hasOption( 'video' ) ) {
			$types[] = 'VIDEO';
		}
		$this->detail = $this->hasOption( 'detail' );

		if ( $this->detail ) {
			$this->output( "file\tduration\tkey\tsize\tbitrate\n" );
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
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->img_name, NS_FILE );
			$file = wfLocalFile( $title );
			$handler = $file ? $file->getHandler() : null;
			if ( $file && $handler && $handler instanceof TimedMediaHandler ) {
				$this->processFile( $file );
			}
		}

		if ( !$this->detail ) {
			$keys = array_keys( $this->count );
			natsort( $keys );
			foreach ( $keys as $key ) {
				$count = $this->count[$key];
				$duration = $this->duration[$key];
				$size = $this->size[$key];
				$bitrate = $size * 8.0 / $duration;
				$this->output( "$key\t$count\t$duration\t$size\t$bitrate\n" );
			}
		}
	}

	public function processFile( File $file ) {
		$dbw = wfGetDB( DB_MASTER );

		// Transcode table doesn't carry the file size, but does carry the final bitrate.
		$handler = $file->getHandler();
		$duration = $handler->getLength( $file );

		$state = WebVideoTranscode::getTranscodeState( $file, $dbw );
		foreach ( $state as $key => $item ) {
			if ( $item && $item['time_success'] ) {
				$name = $file->getName();
				$key = $key;
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
				}
			}
		}
	}
}

$maintClass = 'TranscodeReport'; // Tells it to run the class
require_once RUN_MAINTENANCE_IF_MAIN;
