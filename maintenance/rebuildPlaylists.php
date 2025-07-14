<?php
/**
 * Rebuild HLS .m3u8 video playlists
 */
// @codeCoverageIgnoreStart
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
		$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";
require_once __DIR__ . "/TimedMediaMaintenance.php";
// @codeCoverageIgnoreEnd

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\WebVideoTranscode\WebVideoTranscode;

class RebuildPlaylists extends TimedMediaMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "dry-run", "don't actually change anything; for testing params" );
		$this->addDescription( "rebuild HTTP Live Streaming .m3u8 playlists of audio/video transcodes." );
	}

	public function execute() {
		$this->output( "Rebuild HLS .m3u8 playlists:\n" );
		parent::execute();
		$this->output( "Finished!\n" );
	}

	/**
	 * @param File $file
	 */
	public function processFile( File $file ) {
		$this->output( $file->getName() . "\n" );
		if ( !$this->hasOption( 'dry-run' ) ) {
			WebVideoTranscode::updateStreamingManifests( $file );
		}
	}
}

// Tells it to run the class
// @codeCoverageIgnoreStart
$maintClass = RebuildPlaylists::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
