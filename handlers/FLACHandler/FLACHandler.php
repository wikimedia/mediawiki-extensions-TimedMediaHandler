<?php
/**
 * FLAC handler
 */
class FLACHandler extends ID3Handler {

	/**
	 * @param $file File
	 * @return string
	 */
	function getMetadataType( $file ) {
		return 'flac';
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getWebType( $file ) {
		return 'audio/flac';
	}

	/**
	 * @param $file File
	 * @return array|bool
	 */
	function getStreamTypes( $file ) {
		$streamTypes = array();
		$metadata = $this->unpackMetadata( $file->getMetadata() );

		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}

		if( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'flac' ){
			$streamTypes[] =  'FLAC';
		}

		return $streamTypes;
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getShortDesc( $file ) {
		global $wgLang;

		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-flac-short-audio',
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) )->text();
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getLongDesc( $file ) {
		global $wgLang;
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage('timedmedia-flac-long-audio',
			$wgLang->formatTimePeriod( $this->getLength($file) ),
			$wgLang->formatBitrate( $this->getBitRate( $file ) )
		)->text();

	}

}
