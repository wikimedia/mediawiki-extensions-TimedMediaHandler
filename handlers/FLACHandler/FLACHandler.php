<?php
/**
 * FLAC handler
 */
class FLACHandler extends TimedMediaHandler {
	// XXX match GETID3_VERSION ( too bad version is not a getter )
	const METADATA_VERSION = 2;

	/**
	 * @param $file File
	 * @param $path string
	 * @return string
	 */
	function getMetadata( $file, $path ) {
		// Create new id3 object:
		$getID3 = new getID3();

		// Don't grab stuff we don't use:
		$getID3->option_tag_id3v1         = false;  // Read and process ID3v1 tags
		$getID3->option_tag_id3v2         = false;  // Read and process ID3v2 tags
		$getID3->option_tag_lyrics3       = false;  // Read and process Lyrics3 tags
		$getID3->option_tag_apetag        = false;  // Read and process APE tags
		$getID3->option_tags_process      = false;  // Copy tags to root key 'tags' and encode to $this->encoding
		$getID3->option_tags_html         = false;  // Copy tags to root key 'tags_html' properly translated from various encodings to HTML entities

		// Analyze file to get metadata structure:
		$id3 = $getID3->analyze( $path );

		// remove file paths
		unset( $id3['filename'] );
		unset( $id3['filepath'] );
		unset( $id3['filenamepath']);

		// Update the version
		$id3['version'] = self::METADATA_VERSION;

		return serialize( $id3 );
	}

	/**
	 * @param $metadata
	 * @return bool|mixed
	 */
	function unpackMetadata( $metadata ) {
		wfSuppressWarnings();
		$unser = unserialize( $metadata );
		wfRestoreWarnings();
		if ( isset( $unser['version'] ) && $unser['version'] == self::METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}

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
	 * @return mixed
	 */
	function getBitrate( $file ){
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		return $metadata['bitrate'];
	}

	/**
	 * @param $file File
	 * @return int
	 */
	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['playtime_seconds'];
		}
	}

	/**
	 * @param $file File
	 * @return bool|int
	 */
	function getFramerate( $file ){
		return false;
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
