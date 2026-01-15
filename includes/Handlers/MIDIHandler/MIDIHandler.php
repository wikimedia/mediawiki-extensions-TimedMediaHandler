<?php

namespace MediaWiki\TimedMediaHandler\Handlers\MIDIHandler;

use MediaWiki\FileRepo\File\File;
use MediaWiki\Status\Status;
use MediaWiki\TimedMediaHandler\Handlers\ID3Handler\ID3Handler;

/**
 * MIDI handler
 */
class MIDIHandler extends ID3Handler {

	/**
	 * @param File $file
	 * @return string
	 */
	public function getMetadataType( $file ) {
		return 'midi';
	}

	/**
	 * @param File $file
	 * @return string
	 */
	public function getWebType( $file ) {
		return 'audio/midi';
	}

	/** @inheritDoc */
	public function verifyUpload( $fileName ) {
		$metadata = $this->getID3( $fileName );

		$audioFormat = $metadata[ 'audio' ][ 'dataformat' ] ?? false;
		if ( $audioFormat === 'midi' ) {
			return Status::newGood();
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getID3( $path ) {
		$id3 = parent::getID3( $path );

		// getID3 fails to calculate playtime for MIDI files without tempo events T414645
		// This corrects the duration, until the time this is fixed upstream
		if ( isset( $id3['midi']['totalticks'] ) && ( $id3['playtime_seconds'] ?? 0 ) <= 0 ) {
			$ticksPerQuarterNote = $id3['midi']['raw']['ticksperqnote'] ?? ( 4 * 120 );
			if ( $ticksPerQuarterNote > 0 ) {
				$totalQuarterNotes = $id3['midi']['totalticks'] / $ticksPerQuarterNote;
				// The MIDI standard specifies a default tempo of 120 BPM,
				// which is 0.5 seconds per quarter note.
				$id3['playtime_seconds'] = $totalQuarterNotes * 0.5;
			}
		}

		return $id3;
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
		if ( $audioFormat === 'midi' ) {
			$streamTypes[] = 'MIDI';
		}

		return $streamTypes;
	}

	/**
	 * @param File $file
	 * @return string HTML
	 */
	public function getShortDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		return wfMessage( 'timedmedia-midi-short-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->escaped();
	}

	/**
	 * @param File $file
	 * @return string HTML
	 */
	public function getLongDesc( $file ) {
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getLongDesc( $file );
		}
		return wfMessage( 'timedmedia-midi-long-audio' )
			->timeperiodParams( $this->getLength( $file ) )
			->bitrateParams( $this->getBitRate( $file ) )
			->sizeParams( $file->getSize() )
			->escaped();
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasAudio( $file ) {
		$metadata = $file->getMetadataArray();
		return ( $metadata['audio'] ?? null ) !== null || ( $metadata['midi'] ?? null ) !== null;
	}

	/**
	 * @param File $file
	 * @return int
	 */
	public function getAudioChannels( $file ) {
		$metadata = $file->getMetadataArray();
		if ( isset( $metadata['midi']['raw']['tracks'] ) ) {
			return (int)$metadata['midi']['raw']['tracks'];
		}
		return parent::getAudioChannels( $file );
	}

}
