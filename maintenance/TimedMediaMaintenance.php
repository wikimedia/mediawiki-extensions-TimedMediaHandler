<?php

use MediaWiki\MainConfigNames;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWiki\Title\Title;
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
		$dbr = $this->getServiceContainer()->getDBLoadBalancerFactory()->getReplicaDatabase();
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
		$migrationStage = $this->getConfig()->get(
			MainConfigNames::FileSchemaMigrationStage
		);
		if ( $migrationStage & SCHEMA_COMPAT_READ_OLD ) {
			$queryBuilder = $dbr->newSelectQueryBuilder()
				->select( [ 'img_name' ] )
				->from( 'image' )
				->where( [ 'img_media_type' => $types ] )
				->orderBy( [ 'img_media_type', 'img_name' ], SelectQueryBuilder::SORT_ASC );
		} else {
			$queryBuilder = $dbr->newSelectQueryBuilder()
				->field( 'file_name', 'img_name' )
				->from( 'file' )
				->join( 'filetypes', null, 'file_type = ft_id' )
				->where( [ 'ft_media_type' => $types, 'file_deleted' => 0 ] )
				->orderBy( [ 'file_type', 'file_name' ], SelectQueryBuilder::SORT_ASC );
		}

		if ( $this->hasOption( 'mime' ) ) {
			[ $major, $minor ] = File::splitMime( $this->getOption( 'mime' ) );
			if ( $migrationStage & SCHEMA_COMPAT_READ_OLD ) {
				$queryBuilder->andWhere( [
					'img_major_mime' => $major,
					'img_minor_mime' => $minor
				] );
			} else {
				$queryBuilder->andWhere( [
					'ft_major_mime' => $major,
					'ft_minor_mime' => $minor
				] );
			}
		}

		if ( $this->hasOption( 'file' ) ) {
			$title = Title::newFromText( $this->getOption( 'file' ), NS_FILE );
			if ( !$title ) {
				$this->error( "Invalid --file option provided" );
				return;
			}
			if ( $migrationStage & SCHEMA_COMPAT_READ_OLD ) {
				$queryBuilder->andWhere( [ 'img_name' => $title->getDBkey() ] );
			} else {
				$queryBuilder->andWhere( [ 'file_name' => $title->getDBkey() ] );
			}
		}
		if ( $this->hasOption( 'start' ) ) {
			$title = Title::newFromText( $this->getOption( 'start' ), NS_FILE );
			if ( !$title ) {
				$this->error( "Invalid --start option provided" );
				return;
			}
			if ( $migrationStage & SCHEMA_COMPAT_READ_OLD ) {
				$queryBuilder->andWhere( $dbr->expr( 'img_name', '>=', $title->getDBkey() ) );
			} else {
				$queryBuilder->andWhere( $dbr->expr( 'file_name', '>=', $title->getDBkey() ) );
			}
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
	}

	/**
	 * @param File $file
	 */
	abstract public function processFile( File $file );
}
