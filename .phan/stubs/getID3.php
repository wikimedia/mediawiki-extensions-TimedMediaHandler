<?php

/**
 * A phan stub for class getid3 from package james-heinrich/getid3
 * @phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName,Squiz.Classes.ValidClassName
 */

define( 'GETID3_OS_ISWINDOWS', false );
define( 'GETID3_INCLUDEPATH', '' );
define( 'ENT_SUBSTITUTE', 8 );
define( 'GETID3_TEMP_DIR', '' );

class getID3 {

	/**
	 * @var string
	 */
	public $encoding = 'UTF-8';

	/**
	 * @var string
	 */
	public $encoding_id3v1 = 'ISO-8859-1';

	/**
	 * @var bool
	 */
	public $option_tag_id3v1 = true;

	/**
	 * @var bool
	 */
	public $option_tag_id3v2 = true;

	/**
	 * @var bool
	 */
	public $option_tag_lyrics3 = true;

	/**
	 * @var bool
	 */
	public $option_tag_apetag = true;

	/**
	 * @var bool
	 */
	public $option_tags_process = true;

	/**
	 * @var bool
	 */
	public $option_tags_html = true;

	/**
	 * @var bool
	 */
	public $option_extra_info = true;

	/**
	 * @var bool|string
	 */
	public $option_save_attachments = true;

	/**
	 * @var bool
	 */
	public $option_md5_data = false;

	/**
	 * @var bool
	 */
	public $option_md5_data_source = false;

	/**
	 * @var bool
	 */
	public $option_sha1_data = false;

	/**
	 * @var bool|null
	 */
	public $option_max_2gb_check;

	/**
	 * @var int
	 */
	public $option_fread_buffer_size = 32768;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var resource
	 */
	public $fp;

	/**
	 * @var array
	 */
	public $info;

	/**
	 * @var string
	 */
	public $tempdir = GETID3_TEMP_DIR;

	/**
	 * @var int
	 */
	public $memory_limit = 0;

	/**
	 * @var string
	 */
	protected $startup_error = '';

	/**
	 * @var string
	 */
	protected $startup_warning = '';

	const VERSION = '';
	const FREAD_BUFFER_SIZE = 32768;

	const ATTACHMENTS_NONE = false;
	const ATTACHMENTS_INLINE = true;

	public function __construct() {
	}

	/**
	 * @return string
	 */
	public function version() {
	}

	/**
	 * @return int
	 */
	public function fread_buffer_size() {
	}

	/**
	 * @param array $optArray
	 * @return bool
	 */
	public function setOption( $optArray ) {
	}

	/**
	 * @param string $filename
	 * @param int|null $filesize
	 * @param resource|null $fp
	 * @return bool
	 */
	public function openfile( $filename, $filesize = null, $fp = null ) {
	}

	/**
	 * @param string $filename
	 * @param int|null $filesize
	 * @param string $original_filename
	 * @param resource|null $fp
	 * @return array
	 */
	public function analyze( $filename, $filesize = null, $original_filename = '', $fp = null ) {
	}

	/**
	 * @param string $message
	 * @return array
	 */
	public function error( $message ) {
	}

	/**
	 * @param string $message
	 * @return bool
	 */
	public function warning( $message ) {
	}

	/**
	 * @return array
	 */
	public function GetFileFormatArray() {
	}

	/**
	 * @param string &$filedata
	 * @param string $filename
	 * @return mixed|false
	 */
	public function GetFileFormat( &$filedata, $filename = '' ) {
	}

	/**
	 * @param array &$array
	 * @param string $encoding
	 */
	public function CharConvert( &$array, $encoding ) {
	}

	/**
	 * @return bool
	 */
	public function HandleAllTags() {
	}

	/**
	 * @param string $algorithm
	 * @return array|bool
	 */
	public function getHashdata( $algorithm ) {
	}

	public function ChannelsBitratePlaytimeCalculations() {
	}

	/**
	 * @return bool
	 */
	public function CalculateCompressionRatioVideo() {
	}

	/**
	 * @return bool
	 */
	public function CalculateCompressionRatioAudio() {
	}

	/**
	 * @return bool
	 */
	public function CalculateReplayGain() {
	}

	/**
	 * @return bool
	 */
	public function ProcessAudioStreams() {
	}

	/**
	 * @return string|bool
	 */
	public function getid3_tempnam() {
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function include_module( $name ) {
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public static function is_writable( $filename ) {
	}

}
