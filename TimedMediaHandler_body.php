<?php

// TODO: Fix core printable stylesheet. Descendant selectors suck.

class TimedMediaHandler extends MediaHandler {
	const OGG_METADATA_VERSION = 2;

	static $magicDone = false;

	function isEnabled() {
		return true;
	}

	function getParamMap() {
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		return array(
			'img_width' => 'width',
			'timedmedia_noplayer' => 'noplayer',
			'timedmedia_noicon' => 'noicon',
			'timedmedia_thumbtime' => 'thumbtime',
			'timedmedia_starttime'	=> 'start',
			'timedmedia_endtime'	=> 'end',
		);
	}

	function validateParam( $name, $value ) {
		if ( $name == 'thumbtime' || $name == 'start' || $name == 'end' ) {
			if ( $this->parseTimeString( $value ) === false ) {
				return false;
			}
		}
		return true;
	}

	function parseTimeString( $seekString, $length = false ) {
		$parts = explode( ':', $seekString );
		$time = 0;
		for ( $i = 0; $i < count( $parts ); $i++ ) {
			if ( !is_numeric( $parts[$i] ) ) {
				return false;
			}
			$time += intval( $parts[$i] ) * pow( 60, count( $parts ) - $i - 1 );
		}

		if ( $time < 0 ) {
			wfDebug( __METHOD__.": specified negative time, using zero\n" );
			$time = 0;
		} elseif ( $length !== false && $time > $length - 1 ) {
			wfDebug( __METHOD__.": specified near-end or past-the-end time {$time}s, using end minus 1s\n" );
			$time = $length - 1;
		}
		return $time;
	}

	function makeParamString( $params ) {
		if ( isset( $params['thumbtime'] ) ) {
			$time = $this->parseTimeString( $params['thumbtime'] );
			if ( $time !== false ) {
				return 'seek=' . $time;
			}
		}
		return 'mid';
	}

	function parseParamString( $str ) {
		$m = false;
		if ( preg_match( '/^seek=(\d+)$/', $str, $m ) ) {
			return array( 'thumbtime' => $m[0] );
		}
		return array();
	}

	function normaliseParams( $image, &$params ) {
		$timeParam = array('thumbtime', 'start', 'end');
		// Parse time values if endtime or thumbtime can't be more than length -1
		foreach($timeParam as $pn){
			if ( isset( $params[$pn] ) ) {
				$length = $this->getLength( $image );
				$time = $this->parseTimeString( $params[$pn] );
				if ( $time === false ) {
					return false;
				} elseif ( $time > $length - 1 ) {
					$params[$pn] = $length - 1;
				} elseif ( $time <= 0 ) {
					$params[$pn] = 0;
				}
			}
		}
		// Make sure start time is not > than end time
		if(isset($params['start']) && isset($params['end']) ){
			if($params['start'] > $params['end'])
				return false;
		}

		return true;
	}

	function getImageSize( $file, $path, $metadata = false ) {
		global $wgOggVideoTypes;
		// Just return the size of the first video stream
		if ( $metadata === false ) {
			$metadata = $file->getMetadata();
		}
		$metadata = $this->unpackMetadata( $metadata );
		if ( isset( $metadata['error'] ) || !isset( $metadata['streams'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			if ( in_array( $stream['type'], $wgOggVideoTypes ) ) {
				return array(
					$stream['header']['PICW'],
					$stream['header']['PICH']
				);
			}
		}
		return array( false, false );
	}

	function getMetadata( $image, $path ) {
		$metadata = array( 'version' => self::OGG_METADATA_VERSION );

		if ( !class_exists( 'File_Ogg' ) ) {
			require( 'File/Ogg.php' );
		}
		try {
			$f = new File_Ogg( $path );
			$streams = array();
			foreach ( $f->listStreams() as $streamType => $streamIDs ) {
				foreach ( $streamIDs as $streamID ) {
					$stream = $f->getStream( $streamID );
					$streams[$streamID] = array(
						'serial' => $stream->getSerial(),
						'group' => $stream->getGroup(),
						'type' => $stream->getType(),
						'vendor' => $stream->getVendor(),
						'length' => $stream->getLength(),
						'size' => $stream->getSize(),
						'header' => $stream->getHeader(),
						'comments' => $stream->getComments()
					);
				}
			}
			$metadata['streams'] = $streams;
			$metadata['length'] = $f->getLength();
			// Get the offset of the file (in cases where the file is a segment copy)
			$metadata['offset'] = $f->getStartOffset();
		} catch ( PEAR_Exception $e ) {
			// File not found, invalid stream, etc.
			$metadata['error'] = array(
				'message' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}
		return serialize( $metadata );
	}

	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::OGG_METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}

	function getMetadataType( $image ) {
		return 'ogg';
	}

	function isMetadataValid( $image, $metadata ) {
		return $this->unpackMetadata( $metadata ) !== false;
	}

	function getThumbType( $ext, $mime, $params = null ) {
		return array( 'jpg', 'image/jpeg' );
	}

	function doTransform( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		global $wgFFmpegLocation, $wgEnableTemporalOggUrls, $wgEnabledDerivatives;

		$width = $params['width'];
		$srcWidth = $file->getWidth();
		$srcHeight = $file->getHeight();
		$height = $srcWidth == 0 ? $srcHeight : $width * $srcHeight / $srcWidth;
		$length = $this->getLength( $file );
		$offset = $this->getOffset( $file );
		$noPlayer = isset( $params['noplayer'] );
		$noIcon = isset( $params['noicon'] );

		// Set up the default targetUrl:
		$targetFileUrl = $file->getURL();

		// Check for $wgEnabledDerivatives
		if ( isset( $wgEnabledDerivatives ) &&
			 is_array( $wgEnabledDerivatives ) &&
			 count( $wgEnabledDerivatives ) != 0
		){
			// Get the derivative via embed width
			//( will return $file->getURL() if no derivative is found )
			$targetFileUrl = OggTranscode::getWidthDerivativeURL( $file, $width);
		}

		// Add temporal request parameter if $wgEnableTemporalOggUrls is on:
		if( $wgEnableTemporalOggUrls && isset( $params['start'] ) ){
			$targetFileUrl .= '?t=' . seconds2npt( $this->parseTimeString( $params['start'], $length ) );
			if(isset( $params[ 'end' ] ) && $params['end'] )
				$targetFileUrl.='/'. seconds2npt( $this->parseTimeString( $params['end'], $length) );
		}


		if ( !$noPlayer ) {
			// Hack for miscellaneous callers
			global $wgOut;
			$this->setHeaders( $wgOut );
		}

		if ( $srcHeight == 0 || $srcWidth == 0 ) {
			// Make audio player
			$height = empty( $params['height'] ) ? 20 : $params['height'];
			if ( $noPlayer ) {
				if ( $height > 100 ) {
					global $wgStylePath;
					$iconUrl = "$wgStylePath/common/images/icons/fileicon-ogg.png";
					return new ThumbnailImage( $file, $iconUrl, 120, 120 );
				} else {
					$scriptPath = self::getMyScriptPath();
					$iconUrl = "$scriptPath/info.png";
					return new ThumbnailImage( $file, $iconUrl, 22, 22 );
				}
			}
			if ( empty( $params['width'] ) ) {
				$width = 200;
			} else {
				$width = $params['width'];
			}
			return new OggAudioDisplay( $file, $targetFileUrl, $width, $height, $length, $dstPath, $noIcon, $offset );
		}

		// Video thumbnail only
		if ( $noPlayer ) {
			return new ThumbnailImage( $file, $dstUrl, $width, $height, $dstPath , $noIcon, $offset);
		}

		if ( $flags & self::TRANSFORM_LATER ) {
			return new OggVideoDisplay( $file, $targetFileUrl, $dstUrl, $width, $height, $length, $dstPath, $noIcon, $offset);
		}

		$thumbStatus = $this->gennerateThumb($file, $dstPath,$params, $width, $height);
		if( $thumbStatus !== true )
			return $thumbStatus;


		return new OggVideoDisplay( $file, $targetFileUrl, $dstUrl, $width, $height, $length, $dstPath );
	}
	function gennerateThumb($file, $dstPath, $params, $width, $height){
		global $wgFFmpegLocation, $wgOggThumbLocation;

		$length = $this->getLength( $file );
		$thumbtime = false;
		if ( isset( $params['thumbtime'] ) ) {
			$thumbtime = $this->parseTimeString( $params['thumbtime'], $length );
		}
		if ( $thumbtime === false ) {
			// If start time param isset use that for the thumb:
			if( isset( $params['start'] ) ){
				$thumbtime = $this->parseTimeString( $params['start'], $length );
			}else{
				# Seek to midpoint by default, it tends to be more interesting than the start
				$thumbtime = $length / 2;
			}
		}
		wfMkdirParents( dirname( $dstPath ) );

		wfDebug( "Creating video thumbnail at $dstPath\n" );

		// First check for oggThumb
		if( $wgOggThumbLocation && is_file( $wgOggThumbLocation ) ){
			$cmd = wfEscapeShellArg( $wgOggThumbLocation ) .
				' -t '. intval( $thumbtime ) . ' ' .
				' -n ' . wfEscapeShellArg( $dstPath ) . ' ' .
				' ' . wfEscapeShellArg( $file->getPath() ) . ' 2>&1';
			$returnText = wfShellExec( $cmd, $retval );
			//check if it was successful or if we should try ffmpeg:
			if ( !$this->removeBadFile( $dstPath, $retval ) ) {
				return true;
			}
		}

		$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -ss ' . intval( $thumbtime ) . ' ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			# MJPEG, that's the same as JPEG except it's supported by the windows build of ffmpeg
			# No audio, one frame
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';

		$retval = 0;
		$returnText = wfShellExec( $cmd, $retval );

		if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
			#re-attempt encode command on frame time 1 and with mapping (special case for chopped oggs)
			$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -map 0:1 '.
			' -ss 1 ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';
			$retval = 0;
			$returnText = wfShellExec( $cmd, $retval );
		}

		if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
			#No mapping, time zero. A last ditch attempt.
			$cmd = wfEscapeShellArg( $wgFFmpegLocation ) .
			' -ss 0 ' .
			' -i ' . wfEscapeShellArg( $file->getPath() ) .
			' -f mjpeg -an -vframes 1 ' .
			wfEscapeShellArg( $dstPath ) . ' 2>&1';

			$retval = 0;
			$returnText = wfShellExec( $cmd, $retval );
			// If still bad return error:
			if ( $this->removeBadFile( $dstPath, $retval ) || $retval ) {
				// Filter nonsense
				$lines = explode( "\n", str_replace( "\r\n", "\n", $returnText ) );
				if ( substr( $lines[0], 0, 6 ) == 'FFmpeg' ) {
					for ( $i = 1; $i < count( $lines ); $i++ ) {
						if ( substr( $lines[$i], 0, 2 ) != '  ' ) {
							break;
						}
					}
					$lines = array_slice( $lines, $i );
				}
				// Return error box
				return new MediaTransformError( 'thumbnail_error', $width, $height, implode( "\n", $lines ) );
			}
		}
		//if we did not return an error return true to continue media thum display
		return true;
	}
	function canRender( $file ) { return true; }
	function mustRender( $file ) { return true; }

	function getLength( $file ) {
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return 0;
		} else {
			return $metadata['length'];
		}
	}
	function getOffset( $file ){
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) || !isset( $metadata['offset']) ) {
			return 0;
		} else {
			return $metadata['offset'];
		}
	}

	function getStreamTypes( $file ) {
		$streamTypes = '';
		$metadata = $this->unpackMetadata( $file->getMetadata() );
		if ( !$metadata || isset( $metadata['error'] ) ) {
			return false;
		}
		foreach ( $metadata['streams'] as $stream ) {
			$streamTypes[$stream['type']] = true;
		}
		return array_keys( $streamTypes );
	}

	function getShortDesc( $file ) {
		global $wgLang, $wgOggAudioTypes, $wgOggVideoTypes;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			return parent::getShortDesc( $file );
		}
		if ( array_intersect( $streamTypes, $wgOggVideoTypes ) ) {
			// Count multiplexed audio/video as video for short descriptions
			$msg = 'timedmedia-short-video';
		} elseif ( array_intersect( $streamTypes, $wgOggAudioTypes ) ) {
			$msg = 'timedmedia-short-audio';
		} else {
			$msg = 'timedmedia-short-general';
		}
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $this->getLength( $file ) ) );
	}

	function getLongDesc( $file ) {
		global $wgLang, $wgOggVideoTypes, $wgOggAudioTypes;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		$streamTypes = $this->getStreamTypes( $file );
		if ( !$streamTypes ) {
			$unpacked = $this->unpackMetadata( $file->getMetadata() );
			return wfMsg( 'timedmedia-long-error', $unpacked['error']['message'] );
		}
		if ( array_intersect( $streamTypes,$wgOggVideoTypes  ) ) {
			if ( array_intersect( $streamTypes, $wgOggAudioTypes ) ) {
				$msg = 'timedmedia-long-multiplexed';
			} else {
				$msg = 'timedmedia-long-video';
			}
		} elseif ( array_intersect( $streamTypes, $wgOggAudioTypes ) ) {
			$msg = 'timedmedia-long-audio';
		} else {
			$msg = 'timedmedia-long-general';
		}
		$size = 0;
		$unpacked = $this->unpackMetadata( $file->getMetadata() );
		if ( !$unpacked || isset( $metadata['error'] ) ) {
			$length = 0;
		} else {
			$length = $this->getLength( $file );
			foreach ( $unpacked['streams'] as $stream ) {
				if( isset( $stream['size'] ) )
					$size += $stream['size'];
			}
		}
		$bitrate = $length == 0 ? 0 : $size / $length * 8;
		return wfMsg( $msg, implode( '/', $streamTypes ),
			$wgLang->formatTimePeriod( $length ),
			$wgLang->formatBitrate( $bitrate ),
			$wgLang->formatNum( $file->getWidth() ),
			$wgLang->formatNum( $file->getHeight() )
	   	);
	}

	function getDimensionsString( $file ) {
		global $wgLang;
		wfLoadExtensionMessages( 'TimedMediaHandler' );
		if ( $file->getWidth() ) {
			return wfMsg( 'video-dims', $wgLang->formatTimePeriod( $this->getLength( $file ) ),
				$wgLang->formatNum( $file->getWidth() ),
				$wgLang->formatNum( $file->getHeight() ) );
		} else {
			return $wgLang->formatTimePeriod( $this->getLength( $file ) );
		}
	}

	static function getMyScriptPath() {
		global $wgScriptPath;
		return "$wgScriptPath/extensions/TimedMediaHandler";
	}

	function setHeaders( $out ) {
		global $wgOggScriptVersion, $wgCortadoJarFile, $wgServer, $wgUser, $wgScriptPath,
				$wgEnablePlayTracking, $wgPlayTrackingRate,  $wgVideoTagOut;

		// We could add "video" tag javascript
		// If we wanted to block on mwEmbed player js, instead of loading the js onDomReady

		// embedPlayer classes include: $j.ui,mw.EmbedPlayer,nativeEmbed,ctrlBuilder,mvpcfConfig,kskinConfig,$j.fn.menu,$j.cookie,$j.ui.slider,mw.TimedText
		//<link rel="stylesheet" href="js/mwEmbed/skins/kskin/playerSkin.css" type="text/css" media="screen" />

		// Loading dynamically lets us avoid unnecessary code
		// ie firefox does not need "JSON.js" and IE ~maybe~ needs cortado embed etc.

		if( $wgEnablePlayTracking ) {
			$encPlayTracking = Xml::encodeJsVar( $wgPlayTrackingRate );
			// Should replace with a standard way to send configuration to mw core js
			$out->addHeadItem( 'TimedMediaHandler', <<<EOT
<script type="text/javascript">
mw.setConfig('playTracking', 'true');
mw.setConfig('playTrackingRate', $encPlayTracking );
</script>
EOT
);
		}

	}

	function parserTransformHook( $parser, $file ) {
		if ( isset( $parser->mOutput->hasOggTransform ) ) {
			return;
		}
		$parser->mOutput->hasOggTransform = true;
		$parser->mOutput->addOutputHook( 'TimedMediaHandler' );
	}

	static function outputHook( $outputPage, $parserOutput, $data ) {
		$instance = MediaHandler::getHandler( 'application/ogg' );
		if ( $instance ) {
			$instance->setHeaders( $outputPage );
		}
	}
	// Output an iframe version of the player for remote embedding)
	static function iframeOutputHook( &$title, &$article, $doOutput = true ) {
		global $wgTitle, $wgRequest, $wgOut, $wgEnableIframeEmbed;
		if( !$wgEnableIframeEmbed )
			return true; //continue normal if iframes are "off" (maybe throw a warning in the future)

		// Make sure we are in the right namespace and iframe=true was called:
		if(	is_object( $wgTitle ) && $wgTitle->getNamespace() == NS_FILE  &&
			$wgRequest->getVal('iframe') == 'true' &&
			$wgEnableIframeEmbed &&
			$doOutput ){
				output_iframe_page( $title );
				exit();
				return false;
		}
		return true;
	}
}

class OggTransformOutput extends MediaTransformOutput {
	static $serial = 0;

	function __construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, $isVideo,
		$path, $noIcon = false, $offset )
	{
		$this->file = $file;
		$this->videoUrl = $videoUrl;
		$this->url = $thumbUrl;
		$this->width = round( $width );
		$this->height = round( $height );
		$this->length = round( $length );
		$this->offset = round( $offset );
		$this->isVideo = $isVideo;
		$this->path = $path;
		$this->noIcon = $noIcon;
	}

	function toHtml( $options = array() ) {
		global $wgEnableTemporalOggUrls, $wgVideoTagOut,
			$wgScriptPath, $wgEnableTimedText;

		wfLoadExtensionMessages( 'TimedMediaHandler' );
		if ( count( func_get_args() ) == 2 ) {
			throw new MWException( __METHOD__ .' called in the old style' );
		}

		OggTransformOutput::$serial++;

		if ( substr( $this->videoUrl, 0, 4 ) != 'http' ) {
			global $wgServer;
			$url = $wgServer . $this->videoUrl;
		} else {
			$url = $this->videoUrl;
		}
		// Normalize values
		$length = floatval( $this->length );
		$offset = floatval( $this->offset );
		$width = intval( $this->width );
		$height = intval( $this->height );

		$alt = empty( $options['alt'] ) ? $this->file->getTitle()->getText() : $options['alt'];
		$scriptPath = TimedMediaHandler::getMyScriptPath();
		$thumbDivAttribs = array();
		$showDescIcon = false;

		// Output video tag
		return $this->outputVideoTag($url, $width, $height, $length, $offset, $alt);
	}
	/*
	 * Output the inline video tag output
	 */
	function outputVideoTag($url, $width, $height, $length, $offset, $alt){
		global $wgVideoPlayerSkin, $wgEnableTemporalOggUrls, $wgEnableTimedText;
		// Video tag output:
		if ( $this->isVideo ) {
			$playerHeight = $this->height;
			$thumb_url = $this->url;
		}else{
			// Sound file
			global $wgStylePath;
			$thumb_url = "$wgStylePath/common/images/icons/fileicon-ogg.png";
			if ( $height < 35 )
				$playerHeight = 35;
			else
				$playerHeight = $height;
		}
		$id = "ogg_player_" . OggTransformOutput::$serial;
		$linkAttribs = $this->getDescLinkAttribs( $alt );
		$videoAttr = array(
			'id' => $id,
			'src' => $url,
			'style' => "width:{$width}px;height:{$playerHeight}px",
			'poster' => $thumb_url,
			'controls'=> 'true',
			'durationHint' => $length,
			'startOffset' => $offset,
			'linkback' => $linkAttribs['href'],
			'apiTitleKey' => $this->file->getTitle()->getDBKey()
		);

		/*
		* Output inline metadata for video tag
		* this will eventually be phased out in favor of "ROE" type xml
		* representation of all media asset info.
		*/

		// Init $timedTextSources string
		$timedTextSources = '';
		if( $this->file->getRepoName() != 'local' ){

			//Set the api provider name to "commons" for shared
			// ( provider names should have identified the provider
			// instead of the provider type "shared" )
			$apiProviderName = ( $this->file->getRepoName() == 'shared' ) ? 'commons':  $this->file->getRepoName();

			$videoAttr[ 'apiProvider' ] = 'commons';
		} else if( $wgEnableTimedText ){
				// Get the list of subtitles available
			$params = new FauxRequest( array (
				'action' => 'query',
				'list' => 'allpages',
				'apnamespace' => NS_TIMEDTEXT,
				'aplimit' => 200,
				'apprefix' => $this->file->getTitle()->getDBKey()
			));
			$api = new ApiMain( $params );
			$api->execute();
			$data = & $api->getResultData();

			// Get the list of language Names
			$langNames = Language::getLanguageNames();


			if($data['query'] && $data['query']['allpages'] ){
				foreach( $data['query']['allpages'] as $na => $page ){
					$pageTitle = $page['title'];
					$tileParts = explode( '.', $pageTitle );
					if( count( $tileParts) >= 3 ){
						$subtitle_extension = array_pop( $tileParts );
						$languageKey = array_pop( $tileParts );
					}
					// If there is no valid language continue:
					if( !isset( $langNames[ $languageKey ] ) ){
						continue;
					}
					$textAttr = array(
						'src' => "{$wgServer}{$wgScriptPath}/api.php?" .
							'action=parse&format=json&page=' . $pageTitle,
						'lang' =>  $languageKey,
						'type' => 'text/mw-srt'
					);
					$timedTextSources.= Xml::tags( 'itext', $textAttr, '' );
				}
			}
		}

		if( $wgEnableTemporalOggUrls ){
			$videoAttr['URLTimeEncoding'] = 'true';
		}

		// Set player skin:
		if( $wgVideoPlayerSkin ){
			$videoAttr['class'] = htmlspecialchars ( $wgVideoPlayerSkin );
		}

		$s = Xml::tags( 'video', $videoAttr,
				Xml::tags('div', array(
						'class'=>'videonojs',
						'style'=>"overflow:hidden;".
							"width:{$width}px;height:{$playerHeight}px;".
							"border:solid thin black;padding:5px;"
					),
					wfMsg('timedmedia-no-player-js', $url)
				) .
				$timedTextSources
			);

		return $s;
	}
}

class OggVideoDisplay extends OggTransformOutput {
	function __construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, $path, $noIcon=false, $offset=0 ) {
		parent::__construct( $file, $videoUrl, $thumbUrl, $width, $height, $length, true, $path, false, $offset );
	}
}

class OggAudioDisplay extends OggTransformOutput {
	function __construct( $file, $videoUrl, $width, $height, $length, $path, $noIcon = false, $offset=0 ) {
		parent::__construct( $file, $videoUrl, false, $width, $height, $length, false, $path, $noIcon, $offset );
	}
}
/* Utility functions*/


/*
* Output a minimal iframe for remote embedding (with mv_embed loaded via the script-loader if enabled)
*/
function output_iframe_page( $title ) {
	global $wgEnableIframeEmbed, $wgEnableTemporalOggUrls, $wgOut, $wgUser,
		$wgEnableScriptLoader;

	if(!$wgEnableIframeEmbed){
		throw new MWException( __METHOD__ .' is not enabled' );
		return false;
	}

	// Check for start end if temporal urls are enabled:
	if( $wgEnableTemporalOggUrls ){
		$videoParam[ 'start' ] 	= ( isset( $_GET['starttime'] ) ) ? $_GET['starttime']: '';
		$videoParam[ 'end' ]	= ( isset( $_GET['endtime'] ) ) ? $_GET['endtime']: '';
	}

	$videoParam['width'] 	=  ( isset( $_GET['width'] )  ) ? intval( $_GET['width'] ) 	: '400';
	$videoParam['height'] 	=  ( isset( $_GET['height'] ) ) ? intval( $_GET['height'] ) : '300';

	// Build the html output:
	$file = wfFindFile( $title );
	$thumb = $file->transform( $videoParam );
	$out = new OutputPage();
	$file->getHandler()->setHeaders( $out );
	$out->addCoreScripts2Top();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title> iframe embed </title>
	<style type="text/css">
		body {
			margin-left: 0px;
			margin-top: 0px;
			margin-right: 0px;
			margin-bottom: 0px;
		}
	</style>
		<?php
			// Similar to $out->headElement (but without css)
			echo $out->getHeadScripts();
			echo $out->getHeadLinks();
			echo $out->getHeadItems();
		?>
	</head>
	<body>
		<?php echo $thumb->toHtml(); ?>
	</body>
	</html>
<?php
}
/*
* Converts seconds duration to npt format:
* hh:mm:ss.ms
*/
if(!function_exists('seconds2npt')){
	function seconds2npt( $seconds, $short = false ) {
		$dur = time_duration_2array( $seconds );
		if( ! $dur )
			return null;
		// Output leading zeros (for min,sec):
		if ( $dur['hours'] == 0 && $short == true ) {
			return sprintf( "%2d:%02d", $dur['minutes'], $dur['seconds'] );
		} else {
			return sprintf( "%d:%02d:%02d", $dur['hours'], $dur['minutes'], $dur['seconds'] );
		}
	}
}
/*
 * Convert seconds to time unit array
 */
if(!function_exists('time_duration_2array')){
	function time_duration_2array ( $seconds, $periods = null ) {
		// Define time periods
		if ( !is_array( $periods ) ) {
			$periods = array (
				'years'     => 31556926,
				'months'    => 2629743,
				'weeks'     => 604800,
				'days'      => 86400,
				'hours'     => 3600,
				'minutes'   => 60,
				'seconds'   => 1
				);
		}

		// Loop
		$seconds = (float) $seconds;
		foreach ( $periods as $period => $value ) {
			$count = floor( $seconds / $value );
			if ( $count == 0 ) {
				// Must include hours minutes and seconds even if they are 0
				if ( $period == 'hours' || $period == 'minutes' || $period == 'seconds' ) {
					$values[$period] = 0;
				}
				continue;
			}
			$values[$period] = sprintf( "%02d", $count );
			$seconds = $seconds % $value;
		}
		// Return
		if ( empty( $values ) ) {
			$values = null;
		}
		return $values;
	}
}
