<?php

namespace MediaWiki\TimedMediaHandler\Handlers\FLACHandler;

use File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * FLAC handler
 */
class FLACHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
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
		$metadata = $this->unpackMetadata( $file->getMetadata() );

		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}

		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] === 'flac' ) {
			$streamTypes[] = 'FLAC';
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
		return wfMessage( 'timedmedia-flac-short-audio',
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
			'timedmedia-flac-long-audio'
			)->timeperiodParams(
				$this->getLength( $file )
			)->bitrateParams(
				$this->getBitRate( $file )
			)->sizeParams(
				$file->getSize()
			)->text();
	}

}
