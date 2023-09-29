<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MP3Handler;

use File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * MP3 handler
 */
class MP3Handler extends ID3Handler {

	/**
	 * @param File $image
	 * @return string
	 */
	public function getMetadataType( $image ) {
		return 'mp3';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'audio/mpeg';
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
		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] === 'mp3' ) {
			$streamTypes[] = 'MP3';
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
		return wfMessage( 'timedmedia-mp3-short-audio'
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
			'timedmedia-mp3-long-audio'
			)->timeperiodParams(
				$this->getLength( $file )
			)->bitrateParams(
				$this->getBitRate( $file )
			)->sizeParams(
				$file->getSize()
			)->text();
	}

}
