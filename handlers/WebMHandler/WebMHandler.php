<?php 
/**
 * WebM handler
 */
class WebMHandler extends TimedMediaHandler {	
	const METADATA_VERSION = 1;
	
	function getMetadata( $image, $path ) {
		$metadata = array( 'version' => self::METADATA_VERSION );		
		
		$getID3 = new getID3();
		
		// Don't grab stuff we don't use: 
		$getID3->option_tag_id3v1         = false;  // Read and process ID3v1 tags
		$getID3->option_tag_id3v2         = false;  // Read and process ID3v2 tags
		$getID3->option_tag_lyrics3       = false;  // Read and process Lyrics3 tags
		$getID3->option_tag_apetag        = false;  // Read and process APE tags
		$getID3->option_tags_process      = false;  // Copy tags to root key 'tags' and encode to $this->encoding
		$getID3->option_tags_html         = false;  // Copy tags to root key 'tags_html' properly translated from various encodings to HTML entities
	
		// Analyze file and store returned data in $ThisFileInfo
		$id3 = $getID3->analyze( $path );
		// Unset some parts of id3 that are too detailed and matroska specific:
		unset( $id3['matroska'] ); 
		// remove file paths
		unset( $id3['filename'] );
		unset( $id3['filepath'] );
		unset( $id3['filenamepath']);
		return serialize( $id3 );
	}
	
	/**
	 * Get the "media size" 
	 *
	 */	 
	function getImageSize( $file, $path, $metadata = false ) {
		global $wgMediaVideoTypes;
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );		
		if ( isset( $metadata['error'] ) || !isset( $metadata['streams'] ) ) {
			return false;
		}
		foreach ( $metadata['video'] as $stream ) {
			return array(
				$stream['resolution_x'],
				$stream['resolution_y']
			);
		}
		return array( false, false );
	}
	
	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}
	
	function getMetadataType( $image = '' ) {
		return 'webm';
	}

	function getStreamTypes( $file ) {
		$streamTypes = '';
		$metadata = self::unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		print_r( $metadata 	);
		die();
		foreach ( $metadata['streams'] as $stream ) {
			$streamTypes[$stream['type']] = true;
		}
		return array_keys( $streamTypes );
	}
	
	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['playtime_seconds'];
		}
	}

}