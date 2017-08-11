<?php
/**
 * Mp3 handler
 */
class Mp3Handler extends ID3Handler {

	/**
	 * @param $image
	 * @return string
	 */
	function getMetadataType( $image ) {
		return 'mp3';
	}
	/**
	 * @param $file File
	 */
	function getWebType( $file ) {
		return 'audio/mpeg';
	}
	/**
	 * @param $file File
	 * @return array|bool
	 */
	function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = self::unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'mp3' ) {
			$streamTypes[] = 'MP3';
		}
		return $streamTypes;
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getShortDesc( $file ) {
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
	 * @param $file File
	 * @return String
	 */
	function getLongDesc( $file ) {
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
			)->text();
	}

}
