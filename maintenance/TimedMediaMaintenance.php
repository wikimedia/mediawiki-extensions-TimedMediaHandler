<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use Wikimedia\Rdbms\SelectQueryBuilder;

abstract class TimedMediaMaintenance extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->addOption( "file", "process only the given file", false, true );
		$this->addOption( "start", "(re)start batch at the given file", false, true );
		$this->addOption( "audio", "process audio files (defaults to all media types)" );
		$this->addOption( "video", "process video files (defaults to all media types)" );
		$this->addOption( "mime", "mime type to filter on (e.g. audio/midi)", false, true );
		$this->requireExtension( 'TimedMediaHandler' );
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
		if ( !$types ) {
			// Default to all if none specified
			$types = [ 'AUDIO', 'VIDEO' ];
		}
		$where = [ 'img_media_type' => $types ];

		if ( $this->hasOption( 'mime' ) ) {
			list( $major, $minor ) = File::splitMime( $this->getOption( 'mime' ) );
			$where['img_major_mime'] = $major;
			$where['img_minor_mime'] = $minor;
		}

		if ( $this->hasOption( 'file' ) ) {
			$title = Title::newFromText( $this->getOption( 'file' ), NS_FILE );
			if ( !$title ) {
				$this->error( "Invalid --file option provided" );
				return;
			}
			$where['img_name'] = $title->getDBkey();
		}
		if ( $this->hasOption( 'start' ) ) {
			$title = Title::newFromText( $this->getOption( 'start' ), NS_FILE );
			if ( !$title ) {
				$this->error( "Invalid --start option provided" );
				return;
			}
			$where[] = 'img_name >= ' . $dbr->addQuotes( $title->getDBkey() );
		}
		$res = $dbr->newSelectQueryBuilder()
			->select( [ 'img_name' ] )
			->from( 'image' )
			->where( $where )
			->orderBy( 'img_media_type,img_name', SelectQueryBuilder::SORT_ASC )
			->caller( __METHOD__ )->fetchResultSet();

		$localRepo = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo();
		foreach ( $res as $row ) {
			$title = Title::newFromText( $row->img_name, NS_FILE );
			$file = $localRepo->newFile( $title );
			$handler = $file ? $file->getHandler() : null;
			if ( $file && $handler && $handler instanceof TimedMediaHandler ) {
				$this->processFile( $file );
			}
		}
	}

	/**
	 * @param File $file
	 */
	abstract public function processFile( File $file );
}
