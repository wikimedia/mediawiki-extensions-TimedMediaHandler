<?php
/**
 * WebM handler
 */
class Mp4Handler extends TimedMediaHandler {
	// XXX match GETID3_VERSION ( too bad version is not a getter )
	const METADATA_VERSION = 1;

	/**
	 * @param $image File
	 * @param $path string
	 * @return string
	 */
	function getMetadata( $image, $path ) {
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

		// Unset some parts of id3 that are too detailed and matroska specific:
		unset( $id3['quicktime'] );
		// remove file paths
		unset( $id3['filename'] );
		unset( $id3['filepath'] );
		unset( $id3['filenamepath']);

		// Update the version
		$id3['version'] = self::METADATA_VERSION;

		return serialize( $id3 );
	}

	/**
	 * Get the "media size"
	 * @param $file File
	 * @param $path string
	 * @param $metadata bool
	 * @return array|bool
	 */
	function getImageSize( $file, $path, $metadata = false ) {
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );
		if ( isset( $metadata['error'] ) ) {
			return false;
		}
		if( isset( $metadata['video']['resolution_x'])
				&&
			isset( $metadata['video']['resolution_y'])
		){
			return array (
				$metadata['video']['resolution_x'],
				$metadata['video']['resolution_y']
			);
		}
		return array( false, false );
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
	 * @param $image
	 * @return string
	 */
	function getMetadataType( $image ) {
		return 'mp4';
	}

	/**
	 * @param $file File
	 * @return array|bool
	 */
	function getStreamTypes( $file ) {
		$streamTypes = array();
		$metadata = self::unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		if( isset( $metadata['audio'] ) && $metadata['audio']['dataformat'] == 'mp4' ){
			if( isset( $metadata['audio']['codec'] )
				&&
				strpos( $metadata['audio']['codec'] , 'AAC' ) !== false
			){
				$streamTypes[] =  'AAC';
			} else {
				$streamTypes[] = $metadata['audio']['codec'];
			}
		}
		// id3 gives 'V_VP8' for what we call VP8
		if( $metadata['video']['dataformat'] == 'quicktime' ){
			$streamTypes[] =  'h.264';
		}

		return $streamTypes;
	}

	/**
	 * @param $file File
	 * @return mixed
	 */
	function getBitrate( $file ){
		$metadata = self::unpackMetadata( $file->getMetadata() );
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
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			// return the frame rate of the first found video stream:
			if( isset( $metadata['video']['frame_rate'] ) ){
				return $metadata['video']['frame_rate'];
			}
			return false;
		}
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
		return wfMsg( 'timedmedia-mp4-short-video', implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) );
	}

	/**
	 * @param $file File
	 * @return String
	 */
	function getLongDesc( $file ) {
		global $wgLang;
		return wfMsg('timedmedia-mp4-long-video',
			implode( '/', $this->getStreamTypes( $file ) ),
			$wgLang->formatTimePeriod( $this->getLength($file) ),
			$wgLang->formatBitrate( $this->getBitRate( $file ) ),
			$wgLang->formatNum( $file->getWidth() ),
			$wgLang->formatNum( $file->getHeight() )
		);

	}

}
