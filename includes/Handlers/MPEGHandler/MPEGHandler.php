<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MPEGHandler;

use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * MPEG 1/2 video handler
 */
class MPEGHandler extends ID3Handler {

	/** @inheritDoc */
	public function getMetadataType( $image ) {
		return 'mpeg';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'video/mpeg';
	}

	/**
	 * @param File $file
	 * @return string[]|false
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $file->getMetadataArray();

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'mp2' ) {
			$streamTypes[] = 'MPEG-2';
		} elseif ( $audioFormat ) {
			$streamTypes[] = $audioFormat;
		}
		$videoCodec = $metadata[ 'video' ][ 'codec' ] ?? false;
		if ( $videoCodec ) {
			$streamTypes[] = $videoCodec;
		}

		return $streamTypes;
	}

	/** @inheritDoc */
	protected function getSizeFromMetadata( $metadata ) {
		// Just return the size of the first video stream
		if ( isset( $metadata['error'] ) ) {
			return [ false, false ];
		}
		if (
			isset( $metadata['video']['resolution_x'] ) &&
			isset( $metadata['video']['resolution_y'] ) &&
			isset( $metadata['video']['pixel_aspect_ratio'] )
		) {
			$width = $metadata['video']['resolution_x'];
			$height = $metadata['video']['resolution_y'];
			$aspect = $metadata['video']['pixel_aspect_ratio'];
			$width = (int)( $width * $aspect );
			return [
				$width,
				$height
			];
		}
		return [ false, false ];
	}

	/** @inheritDoc */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-mpeg-short-video' )
			->params( implode( '/', $streamTypes ) )
			->timeperiodParams( $this->getLength( $file ) )
			->escaped();
	}

	/** @inheritDoc */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-mpeg-long-video' )
			->params( implode( '/', $streamTypes ) )
			->timeperiodParams( $this->getLength( $file ) )
			->bitrateParams( $this->getBitRate( $file ) )
			->numParams( $file->getWidth(), $file->getHeight() )
			->sizeParams( $file->getSize() )
			->escaped();
	}

}
