<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MIDIHandler;

use File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;
use Status;

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

		if (
			isset( $metadata['audio'] )
			&& $metadata['audio']['dataformat'] === 'midi'
		) {
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

		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] === 'midi' ) {
			$streamTypes[] = 'MIDI';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getShortDesc( $file ) {
		global $wgLang;

		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}

		return wfMessage( 'timedmedia-midi-short-audio',
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) )->text();
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}

		return wfMessage(
			'timedmedia-midi-long-audio'
			)->timeperiodParams(
				$this->getLength( $file )
			)->bitrateParams(
				$this->getBitRate( $file )
			)->sizeParams(
				$file->getSize()
			)->text();
	}

}
