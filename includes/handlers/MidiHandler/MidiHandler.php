<?php
/**
 * MIDI handler
 */
class MidiHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
	public function getMetadataType( $file ) {
		return 'midi';
	}

	/**
	 * @param File $file
	 * @return String
	 */
	public function getWebType( $file ) {
		return 'audio/midi';
	}

	public function verifyUpload( $filename ) {
		$metadata = $this->getID3( $filename );

		if (
			isset( $metadata['audio'] )
			&& $metadata['audio']['dataformat'] == 'midi'
		) {
			return Status::newGood();
		}
	}

	/**
	 * @param File $file
	 * @return array|bool
	 */
	public function getStreamTypes( $file ) {
		$streamTypes = [];
		$metadata = $this->unpackMetadata( $file->getMetadata() );

		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}

		if ( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'midi' ) {
			$streamTypes[] = 'MIDI';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return String
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
	 * @return String
	 */
	public function getLongDesc( $file ) {
		global $wgLang;
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}

		return wfMessage( 'timedmedia-midi-long-audio',
			$wgLang->formatTimePeriod( $this->getLength( $file ) ),
			$wgLang->formatBitrate( $this->getBitRate( $file ) )
		)->text();
	}

}
