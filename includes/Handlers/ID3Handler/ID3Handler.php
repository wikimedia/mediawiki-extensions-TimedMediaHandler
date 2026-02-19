<?php

namespace MediaWiki\TimedMediaHandler\Handlers\ID3Handler;

use getID3;
use MediaWiki\FileRepo\File\File;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;

/**
 * getID3 Metadata handler
 */
class ID3Handler extends TimedMediaHandler {
	// XXX match GETID3_VERSION ( too bad version is not a getter )
	private const METADATA_VERSION = 2;

	/**
	 * @param string $path
	 * @return array
	 */
	protected function getID3( $path ) {
		// Create new id3 object:
		$getID3 = new getID3();

		// Don't grab stuff we don't use:
		// Read and process ID3v1 tags
		$getID3->option_tag_id3v1 = false;
		// Read and process ID3v2 tags
		$getID3->option_tag_id3v2 = false;
		// Read and process Lyrics3 tags
		$getID3->option_tag_lyrics3 = false;
		// Read and process APE tags
		$getID3->option_tag_apetag = false;
		// Copy tags to root key 'tags' and encode to $this->encoding
		$getID3->option_tags_process = false;
		// Copy tags to root key 'tags_html' properly translated from various encodings to HTML entities
		$getID3->option_tags_html = false;

		// Analyze file to get metadata structure:
		$id3 = $getID3->analyze( $path );

		// remove file paths
		unset( $id3['filename'] );
		unset( $id3['filepath'] );
		unset( $id3['filenamepath'] );

		// Update the version
		$id3['version'] = self::METADATA_VERSION;

		return $id3;
	}

	/** @inheritDoc */
	public function getSizeAndMetadata( $state, $path ) {
		$metadata = $this->getID3( $path );
		$results = [
			'metadata' => $metadata
		];
		$size = $this->getSizeFromMetadata( $metadata );

		if ( $size[0] && $size[1] ) {
			$results['width'] = $size[0];
			$results['height'] = $size[1];
		}
		return $results;
	}

	/**
	 * Retrieve width x height from metadata fetched in getSizeAndMetadata()
	 * @param array $metadata
	 * @return array
	 */
	protected function getSizeFromMetadata( $metadata ) {
		return [ false, false ];
	}

	/** @inheritDoc */
	public function isFileMetadataValid( $image ) {
		$metadata = $image->getMetadataArray();

		if ( !$metadata || isset( $metadata['error'] ) ) {
			wfDebug( __METHOD__ . " invalid id3 metadata" );
			return self::METADATA_BAD;
		}

		if ( !isset( $metadata['version'] )
			|| $metadata['version'] !== self::METADATA_VERSION
		) {
			wfDebug( __METHOD__ . " old but compatible id3 metadata" );
			return self::METADATA_COMPATIBLE;
		}

		return self::METADATA_GOOD;
	}

	/**
	 * @param File $file
	 * @return int
	 */
	public function getBitrate( $file ) {
		$metadata = $file->getMetadataArray();
		return (int)( $metadata['bitrate'] ?? 0 );
	}

	/** @inheritDoc */
	public function getLength( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['playtime_seconds'] ?? 0.0 );
	}

	/** @inheritDoc */
	public function getFramerate( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['video']['frame_rate'] ?? 0.0 );
	}

	/** @inheritDoc */
	public function isInterlaced( $file ) {
		$metadata = $file->getMetadataArray();
		return (bool)( $metadata['video']['interlaced'] ?? false );
	}

	/** @inheritDoc */
	public function hasVideo( $file ) {
		$metadata = $file->getMetadataArray();
		return ( $metadata['video'] ?? null ) !== null;
	}

	/** @inheritDoc */
	public function hasAudio( $file ) {
		$metadata = $file->getMetadataArray();
		return ( $metadata['audio'] ?? null ) !== null;
	}

	/** @inheritDoc */
	public function getAudioChannels( $file ) {
		$metadata = $file->getMetadataArray();
		return (int)( $metadata['audio']['channels'] ?? 0 );
	}

}
