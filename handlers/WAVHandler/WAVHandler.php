<?php
/**
 * WAV handler
 */
class WAVHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
	function getMetadataType( $file ) {
		return 'wav';
	}

	/**
	 * @param File $file
	 * @return String
	 */
	function getWebType( $file ) {
		return 'audio/wav';
	}

	function verifyUpload( $filename ) {
		$metadata = $this->getID3( $filename );

		if (
			isset( $metadata['audio'] )
			&& $metadata['audio']['dataformat'] == 'wav'
			&& ( $metadata['audio']['codec'] == 'Pulse Code Modulation (PCM)' ||
				$metadata['audio']['codec'] == 'IEEE Float' )
		) {
			return Status::newGood();
		}

		return Status::newFatal( 'timedmedia-wav-pcm-required' );
	}
	/**
	 * @param File $file
	 * @return array|bool
	 */
	function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $this->unpackMetadata( $file->getMetadata() );

		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}

		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'wav' ) {
			$streamTypes[] = 'WAV';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return String
	 */
	function getShortDesc( $file ) {
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
	 * @return String
	 */
	function getLongDesc( $file ) {
		global $wgLang;
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-wav-long-audio',
			$wgLang->formatTimePeriod( $this->getLength( $file ) ),
			$wgLang->formatBitrate( $this->getBitRate( $file ) )
		)->text();
	}

}
