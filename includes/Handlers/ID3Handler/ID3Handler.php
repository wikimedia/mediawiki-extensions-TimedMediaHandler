<?php

namespace MediaWiki\TimedMediaHandler\Handlers\ID3Handler;

use File;
use getID3;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use Wikimedia\AtEase\AtEase;

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

	/**
	 * @param File $file
	 * @param string $path
	 * @return string
	 */
	public function getMetadata( $file, $path ) {
		$id3 = $this->getID3( $path );
		return serialize( $id3 );
	}

	/**
	 * @param string $metadata
	 * @return false|mixed
	 * @deprecated 1.41 use File::getMetadataArray
	 */
	public function unpackMetadata( $metadata ) {
		AtEase::suppressWarnings();
		$unser = unserialize( $metadata );
		AtEase::restoreWarnings();
		if ( isset( $unser['version'] ) && $unser['version'] === self::METADATA_VERSION ) {
			return $unser;
		}
		return false;
	}

	/**
	 * @param File $file
	 * @return int
	 */
	public function getBitrate( $file ) {
		$metadata = $file->getMetadataArray();
		return (int)( $metadata['bitrate'] ?? 0 );
	}

	/**
	 * @param File $file
	 * @return float
	 */
	public function getLength( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['playtime_seconds'] ?? 0.0 );
	}

	/**
	 * @param File $file
	 * @return float framerate as floating point; 0 indicates no valid rate data
	 */
	public function getFramerate( $file ) {
		$metadata = $file->getMetadataArray();
		return (float)( $metadata['video']['frame_rate'] ?? 0.0 );
	}

	/**
	 * Returns true if the file contains an interlaced video track.
	 * @param File $file
	 * @return bool
	 */
	public function isInterlaced( $file ) {
		$metadata = $file->getMetadataArray();
		return (bool)( $metadata['video']['interlaced'] ?? false );
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasVideo( $file ) {
		$metadata = $file->getMetadataArray();
		return ( $metadata['video'] ?? null ) !== null;
	}

	/**
	 * @param File $file
	 * @return bool
	 */
	public function hasAudio( $file ) {
		$metadata = $file->getMetadataArray();
		return ( $metadata['audio'] ?? null ) !== null;
	}

	/**
	 * @param File $file
	 * @return int
	 */
	public function getAudioChannels( $file ) {
		$metadata = $file->getMetadataArray();
		return (int)( $metadata['audio']['channels'] ?? 0 );
	}

}
