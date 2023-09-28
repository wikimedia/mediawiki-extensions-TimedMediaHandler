<?php
/**
 * Re-queue selected, or all, transcodes
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . "/TimedMediaMaintenance.php";

class DumpMetadata extends TimedMediaMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "pretty", "pretty-print JSON (adds line breaks)" );
		$this->addDescription( "dump media file metadata as JSON per line" );
	}

	public function execute() {
		$this->output( "Dumping media metadata:\n" );
		parent::execute();
	}

	/**
	 * @param File $file
	 */
	public function processFile( File $file ) {
		$name = $file->getName();
		$handler = $file->getHandler();
		$meta = $file->getMetadataArray();
		$data = [
			'filename' => $name,
			'metadata' => $meta,
		];

		$flags = JSON_INVALID_UTF8_SUBSTITUTE;
		if ( $this->hasOption( 'pretty' ) ) {
			$flags |= JSON_PRETTY_PRINT;
		}
		$json = json_encode( $data, $flags );
		if ( !$json ) {
			die( "$name: invalid JSON output\n" );
		}
		echo "$json\n";
	}
}

// Tells it to run the class
$maintClass = DumpMetadata::class;
require_once RUN_MAINTENANCE_IF_MAIN;
