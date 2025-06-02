<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MIDIHandler;

use MediaWiki\FileRepo\File\File;
use MediaWiki\Status\Status;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * MIDI handler
 */
class MIDIHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
	public function getMetadataType( $file ) {
		return 'midi';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'audio/midi';
	}

	/** @inheritDoc */
	public function verifyUpload( $fileName ) {
		$metadata = $this->getID3( $fileName );

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'midi' ) {
			return Status::newGood();
		}
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $this->unpackMetadata( $file->getMetadata() );

		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'midi' ) {
			$streamTypes[] = 'MIDI';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return string HTML
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-midi-short-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->escaped();
	}

	/**
	 * @param File $file
	 * @return string HTML
	 */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-midi-long-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->bitrateParams( $this->getBitRate( $file ) )
			->sizeParams( $file->getSize() )
			->escaped();
	}

}
