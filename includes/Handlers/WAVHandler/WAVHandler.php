<?php

namespace MediaWiki\TimedMediaHandler\Handlers\WAVHandler;

use MediaWiki\FileRepo\File\File;
use MediaWiki\Status\Status;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * WAV handler
 */
class WAVHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
	public function getMetadataType( $file ) {
		return 'wav';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'audio/wav';
	}

	/** @inheritDoc */
	public function verifyUpload( $fileName ) {
		$metadata = $this->getID3( $fileName );

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		$audioCodec = $metadata[ 'audio' ][ 'codec' ] ?? false;
		if (
			$audioFormat === 'wav'
			&& ( $audioCodec === 'Pulse Code Modulation (PCM)' ||
				$audioCodec === 'IEEE Float' )
		) {
			return Status::newGood();
		}

		return Status::newFatal( 'timedmedia-wav-pcm-required' );
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
		if ( $audioFormat === 'wav' ) {
			$streamTypes[] = 'WAV';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage(
			'timedmedia-wav-short-audio',
			)->timeperiodParams(
				$this->getLength( $file )
			)->text();
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
			'timedmedia-wav-long-audio'
			)->timeperiodParams(
				$this->getLength( $file )
			)->bitrateParams(
				$this->getBitRate( $file )
			)->sizeParams(
				$file->getSize()
			)->text();
	}

}
