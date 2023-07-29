<?php

namespace MediaWiki\TimedMediaHandler\Handlers\WAVHandler;

use File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;
use Status;

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

		if (
			isset( $metadata['audio'] )
			&& $metadata['audio']['dataformat'] === 'wav'
			&& ( $metadata['audio']['codec'] === 'Pulse Code Modulation (PCM)' ||
				$metadata['audio']['codec'] === 'IEEE Float' )
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

		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] === 'wav' ) {
			$streamTypes[] = 'WAV';
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
		return wfMessage( 'timedmedia-wav-short-audio',
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
