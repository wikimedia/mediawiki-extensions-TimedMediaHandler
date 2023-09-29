<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MPEGHandler;

use File;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * MPEG 1/2 video handler
 */
class MPEGHandler extends ID3Handler {

	/**
	 * @param File $image
	 * @return string
	 */
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
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		if ( isset( $metadata['audio']['dataformat'] ) ) {
			if ( $metadata['audio']['dataformat'] === 'mp2' ) {
				$streamTypes[] = 'MPEG-2';
			} else {
				$streamTypes[] = $metadata['audio']['dataformat'];
			}
		}
		if ( isset( $metadata['video']['codec'] ) ) {
			$streamTypes[] = $metadata['video']['codec'];
		}

		return $streamTypes;
	}

	/**
	 * Get the "media size"
	 * @param File $file
	 * @param string $path
	 * @param string|false $metadata
	 * @return array|false
	 */
	public function getImageSize( $file, $path, $metadata = false ) {
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );
		if ( isset( $metadata['error'] ) ) {
			return false;
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

	/**
	 * @param File $file
	 * @return string
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-mpeg-short-video', implode( '/', $streamTypes )
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
			'timedmedia-mpeg-long-video',
			implode( '/', $streamTypes )
			)->timeperiodParams(
				$this->getLength( $file )
			)->bitrateParams(
				$this->getBitRate( $file )
			)->numParams(
				$file->getWidth(),
				$file->getHeight()
			)->sizeParams(
				$file->getSize()
			)->text();
	}

}
