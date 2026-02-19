<?php

namespace MediaWiki\TimedMediaHandler\Handlers\FLACHandler;

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * FLAC handler
 */
class FLACHandler extends ID3Handler {

	/** @inheritDoc */
	public function getMetadataType( $file ) {
		return 'flac';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'audio/flac';
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $file->getMetadataArray();

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'flac' ) {
			$streamTypes[] = 'FLAC';
		}

		return $streamTypes;
	}

	/** @inheritDoc */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-flac-short-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->escaped();
	}

	/** @inheritDoc */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-flac-long-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->bitrateParams( $this->getBitRate( $file ) )
			->sizeParams( $file->getSize() )
			->escaped();
	}

}
