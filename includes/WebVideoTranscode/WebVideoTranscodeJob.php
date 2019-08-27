<?php
/**
 * Job for transcode jobs
 *
 * @file
 * @ingroup JobQueue
 */

 use MediaWiki\Logger\LoggerFactory;

/**
 * Job for web video transcode
 *
 * Support two modes
 * 1) non-free media transcode ( delays the media file being inserted,
 *    adds note to talk page once ready)
 * 2) derivatives for video ( makes new sources for the asset )
 *
 * @ingroup JobQueue
 */

class WebVideoTranscodeJob extends Job {
	/** @var TempFSFile */
	public $targetEncodeFile = null;
	/** @var string */
	public $sourceFilePath = null;
	/** @var File */
	public $file;
	/** @var FSFile|null */
	public $source;

	public function __construct( $title, $params, $id = 0 ) {
		if ( isset( $params['prioritized'] ) && $params['prioritized'] ) {
			$command = 'webVideoTranscodePrioritized';
		} else {
			$command = 'webVideoTranscode';
		}
		parent::__construct( $command, $title, $params, $id );
		$this->removeDuplicates = true;
	}

	/**
	 * Wrapper around debug logger
	 * @param string $msg
	 */
	private function output( $msg ) {
		LoggerFactory::getInstance( 'WebVideoTranscodeJob' )->debug( $msg );
	}

	/**
	 * @return File
	 */
	private function getFile() {
		if ( !$this->file ) {
			$this->file = wfLocalFile( $this->title );
		}
		return $this->file;
	}

	/**
	 * @return string
	 */
	private function getTargetEncodePath() {
		if ( !$this->targetEncodeFile ) {
			$file = $this->getFile();
			$transcodeKey = $this->params[ 'transcodeKey' ];
			$this->targetEncodeFile = WebVideoTranscode::getTargetEncodeFile( $file, $transcodeKey );
			$this->targetEncodeFile->bind( $this );
		}
		return $this->targetEncodeFile->getPath();
	}

	/**
	 * purge temporary encode target
	 */
	private function purgeTargetEncodeFile() {
		if ( $this->targetEncodeFile ) {
			$this->targetEncodeFile->purge();
			$this->targetEncodeFile = null;
		}
	}

	/**
	 * @return string|bool
	 */
	private function getSourceFilePath() {
		if ( !$this->sourceFilePath ) {
			$file = $this->getFile();
			$this->source = $file->repo->getLocalReference( $file->getPath() );
			if ( !$this->source ) {
				$this->sourceFilePath = false;
			} else {
				$this->sourceFilePath = $this->source->getPath();
			}
		}
		return $this->sourceFilePath;
	}

	/**
	 * Update the transcode table with failure time and error
	 * @param string $transcodeKey
	 * @param string $error
	 *
	 */
	private function setTranscodeError( $transcodeKey, $error ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbw->update(
			'transcode',
			[
				'transcode_time_error' => $dbw->timestamp(),
				'transcode_error' => $error
			],
			[
					'transcode_image_name' => $this->getFile()->getName(),
					'transcode_key' => $transcodeKey
			],
			__METHOD__
		);
		$this->setLastError( $error );
	}

	/**
	 * Run the transcode request
	 * @return bool success
	 */
	public function run() {
		global $wgVersion;
		// get a local pointer to the file
		$file = $this->getFile();

		// Validate the file exists:
		if ( !$file ) {
			$this->output( $this->title . ': File not found ' );
			return false;
		}

		// Validate the transcode key param:
		$transcodeKey = $this->params['transcodeKey'];
		// Build the destination target
		if ( !isset( WebVideoTranscode::$derivativeSettings[ $transcodeKey ] ) ) {
			$error = "Transcode key $transcodeKey not found, skipping";
			$this->output( $error );
			$this->setLastError( $error );
			return false;
		}

		// Validate the source exists:
		if ( !$this->getSourceFilePath() || !is_file( $this->getSourceFilePath() ) ) {
			$status = $this->title . ': Source not found ' . $this->getSourceFilePath();
			$this->output( $status );
			$this->setTranscodeError( $transcodeKey, $status );
			return false;
		}

		$options = WebVideoTranscode::$derivativeSettings[ $transcodeKey ];

		if ( isset( $options[ 'novideo' ] ) ) {
			$this->output( "Encoding to audio codec: " . $options['audioCodec'] );
		} else {
			$this->output( "Encoding to codec: " . $options['videoCodec'] );
		}

		$dbw = wfGetDB( DB_MASTER );

		// Check if we have "already started" the transcode ( possible error )
		$dbStartTime = $dbw->selectField( 'transcode', 'transcode_time_startwork',
			[
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			],
			__METHOD__
		);
		if ( !is_null( $dbStartTime ) ) {
			$error = 'Error, running transcode job, for job that has already started';
			$this->output( $error );
			return true;
		}

		// Update the transcode table letting it know we have "started work":
		$jobStartTimeCache = $dbw->timestamp();
		$dbw->update(
			'transcode',
			[ 'transcode_time_startwork' => $jobStartTimeCache ],
			[
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			],
			__METHOD__
		);

		$lbFactory = MediaWiki\MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
		// Avoid contention and "server has gone away" errors as
		// the transcode will take a very long time in some cases
		$lbFactory->commitAll( __METHOD__ );
		// We can't just leave the connection open either or it will
		// eat up resources and block new connections, so make sure
		// everything is dead and gone.
		$lbFactory->closeAll();

		// Check the codec see which encode method to call;
		if ( isset( $options[ 'novideo' ] ) ) {
			if ( $file->getMimeType() == 'audio/midi' ) {
				$status = $this->midiToAudioEncode( $options );
			} else {
				$status = $this->ffmpegEncode( $options );
			}
		} elseif ( $options['videoCodec'] == 'vp8' || $options['videoCodec'] == 'vp9' ||
			$options['videoCodec'] == 'h264'
		) {
			// Check for twopass:
			if ( isset( $options['twopass'] ) ) {
				// ffmpeg requires manual two pass
				$status = $this->ffmpegEncode( $options, 1 );
				if ( $status && !is_string( $status ) ) {
					$status = $this->ffmpegEncode( $options, 2 );
				}
			} else {
				$status = $this->ffmpegEncode( $options );
			}
		} else {
			wfDebug( 'Error unknown codec:' . $options['videoCodec'] );
			$status = 'Error unknown target encode codec:' . $options['videoCodec'];
		}

		// Remove any log files,
		// all useful info should be in status and or we are done with 2 pass encoding
		$this->removeFfmpegLogFiles();

		// Reconnect to the database...
		$dbw = wfGetDB( DB_MASTER );

		// Do a quick check to confirm the job was not restarted or removed while we were transcoding
		// Confirm that the in memory $jobStartTimeCache matches db start time
		$dbStartTime = $dbw->selectField( 'transcode', 'transcode_time_startwork',
			[
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			]
		);

		// Check for ( hopefully rare ) issue of or job restarted while transcode in progress
		if ( $dbw->timestamp( $jobStartTimeCache ) != $dbw->timestamp( $dbStartTime ) ) {
			$this->output(
				'Possible Error,
					transcode task restarted, removed, or completed while transcode was in progress'
			);
			// if an error; just error out,
			// we can't remove temp files or update states, because the new job may be doing stuff.
			if ( $status !== true ) {
				$this->setTranscodeError( $transcodeKey, $status );
				return false;
			}
			// else just continue with db updates,
			// and when the new job comes around it won't start because it will see
			// that the job has already been started.
		}

		// If status is oky and target does not exist, reset status
		if ( $status === true && !is_file( $this->getTargetEncodePath() ) ) {
			$status = 'Target does not exist: ' . $this->getTargetEncodePath();
		}
		// If status is ok and target is larger than 0 bytes
		if ( $status === true && filesize( $this->getTargetEncodePath() ) > 0 ) {
			$file = $this->getFile();
			$storeOptions = null;
			if ( version_compare( $wgVersion, '1.23c', '>' ) &&
				strpos( $options['type'], '/ogg' ) !== false &&
				$file->getLength()
			) {
				$storeOptions = [];
				// Ogg files need a duration header for firefox
				$storeOptions['headers']['X-Content-Duration'] = floatval( $file->getLength() );
			}

			// Avoid "server has gone away" errors as copying can be slow
			$lbFactory->commitAll( __METHOD__ );
			$lbFactory->closeAll();

			// Copy derivative from the FS into storage at $finalDerivativeFilePath
			$result = $file->getRepo()->quickImport(
				$this->getTargetEncodePath(), // temp file
				WebVideoTranscode::getDerivativeFilePath( $file, $transcodeKey ), // storage
				$storeOptions
			);

			if ( !$result->isOK() ) {
				// no need to invalidate all pages with video.
				// Because all pages remain valid ( no $transcodeKey derivative )
				// just clear the file page ( so that the transcode table shows the error )
				$this->title->invalidateCache();
				$this->setTranscodeError( $transcodeKey, $result->getWikiText() );
				$status = false;
			} else {
				$bitrate = round(
					intval( filesize( $this->getTargetEncodePath() ) / $file->getLength() ) * 8
				);
				// Wikimedia\restoreWarnings();
				// Reconnect to the database...
				$dbw = wfGetDB( DB_MASTER );
				// Update the transcode table with success time:
				$dbw->update(
					'transcode',
					[
						'transcode_error' => '',
						'transcode_time_success' => $dbw->timestamp(),
						'transcode_final_bitrate' => $bitrate
					],
					[
						'transcode_image_name' => $this->getFile()->getName(),
						'transcode_key' => $transcodeKey,
					],
					__METHOD__
				);
				// Commit to reduce contention
				$dbw->commit( __METHOD__, 'flush' );
				WebVideoTranscode::invalidatePagesWithFile( $this->title );
			}
		} else {
			// Update the transcode table with failure time and error
			$this->setTranscodeError( $transcodeKey, $status );
			// no need to invalidate all pages with video.
			// Because all pages remain valid ( no $transcodeKey derivative )
			// just clear the file page ( so that the transcode table shows the error )
			$this->title->invalidateCache();
		}
		// done with encoding target, clean up
		$this->purgeTargetEncodeFile();

		// Clear the webVideoTranscode cache ( so we don't keep out dated table cache around )
		WebVideoTranscode::clearTranscodeCache( $this->title->getDBkey() );

		$url = WebVideoTranscode::getTranscodedUrlForFile( $file, $transcodeKey );
		$update = new CdnCacheUpdate( [ $url ] );
		$update->doUpdate();

		if ( $status !== true ) {
			$this->setLastError( $status );
		}
		return $status === true;
	}

	private function removeFfmpegLogFiles() {
		$path = $this->getTargetEncodePath();
		$dir = dirname( $path );
		if ( is_dir( $dir ) ) {
			$dh = opendir( $dir );
			if ( $dh ) {
				$file = readdir( $dh );
				while ( $file !== false ) {
					$log_path = "$dir/$file";
					$ext = strtolower( pathinfo( $log_path, PATHINFO_EXTENSION ) );
					if ( $ext == 'log' && substr( $log_path, 0, strlen( $path ) ) == $path ) {
						Wikimedia\suppressWarnings();
						unlink( $log_path );
						Wikimedia\restoreWarnings();
					}
					$file = readdir( $dh );
				}
				closedir( $dh );
			}
		}
	}

	/**
	 * Utility helper for ffmpeg mapping
	 * @param array $options
	 * @param int $pass
	 * @return bool|string
	 */
	private function ffmpegEncode( $options, $pass = 0 ) {
		global $wgFFmpegLocation, $wgTranscodeBackgroundMemoryLimit;

		if ( !is_file( $this->getSourceFilePath() ) ) {
			return "source file is missing, " . $this->getSourceFilePath() . ". Encoding failed.";
		}

		// Set up the base command
		$cmd = wfEscapeShellArg(
			$wgFFmpegLocation
		) . ' -y -i ' . wfEscapeShellArg( $this->getSourceFilePath() );

		if ( isset( $options['vpre'] ) ) {
			$cmd .= ' -vpre ' . wfEscapeShellArg( $options['vpre'] );
		}

		if ( isset( $options['novideo'] ) ) {
			$cmd .= " -vn ";
		} elseif ( $options['videoCodec'] == 'vp8' || $options['videoCodec'] == 'vp9' ) {
			$cmd .= $this->ffmpegAddWebmVideoOptions( $options, $pass );
		} elseif ( $options['videoCodec'] == 'h264' ) {
			$cmd .= $this->ffmpegAddH264VideoOptions( $options, $pass );
		}
		// Add size options:
		$cmd .= $this->ffmpegAddVideoSizeOptions( $options );

		// Work around https://trac.ffmpeg.org/ticket/6375 in ffmpeg 3.4/4.0
		// Sometimes caused transcode failures saying things like:
		// "1 frames left in the queue on closing"
		$cmd .= ' -max_muxing_queue_size 1024';

		// Check for start time
		if ( isset( $options['starttime'] ) ) {
			$cmd .= ' -ss ' . wfEscapeShellArg( $options['starttime'] );
		} else {
			$options['starttime'] = 0;
		}
		// Check for end time:
		if ( isset( $options['endtime'] ) ) {
			$cmd .= ' -t ' . intval( $options['endtime'] ) - intval( $options['starttime'] );
		}

		if ( $pass == 1 || isset( $options['noaudio'] ) ) {
			$cmd .= ' -an';
		} else {
			$cmd .= $this->ffmpegAddAudioOptions( $options, $pass );
		}

		if ( $pass != 0 ) {
			$cmd .= " -pass " . wfEscapeShellArg( $pass );
			$cmd .= " -passlogfile " . wfEscapeShellArg( $this->getTargetEncodePath() . '.log' );
		}
		// And the output target:
		if ( $pass == 1 ) {
			$cmd .= ' /dev/null';
		} else {
			$cmd .= " " . wfEscapeShellArg( $this->getTargetEncodePath() );
		}

		$this->output( "Running cmd: \n\n" . $cmd . "\n" );

		// Right before we output remove the old file
		$retval = 0;
		$shellOutput = $this->runShellExec( $cmd, $retval );

		if ( $retval != 0 ) {
			return $cmd .
				"\n\nExitcode: $retval\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
				$shellOutput;
		}
		return true;
	}

	/**
	 * Adds ffmpeg shell options for h264
	 *
	 * @param array $options
	 * @param int $pass
	 * @return string
	 */
	public function ffmpegAddH264VideoOptions( $options, $pass ) {
		global $wgFFmpegThreads;
		// Set the codec:
		$cmd = " -threads " . intval( $wgFFmpegThreads ) . " -vcodec libx264";
		// Check for presets:
		if ( isset( $options['preset'] ) ) {
			// Add the two vpre types:
			switch ( $options['preset'] ) {
				case 'ipod320':
					// phpcs:ignore Generic.Files.LineLength.TooLong
					$cmd .= " -profile:v baseline -preset slow -coder 0 -bf 0 -weightb 1 -level 13 -maxrate 768k -bufsize 3M";
				break;
				case '720p':
				case 'ipod640':
					// phpcs:ignore Generic.Files.LineLength.TooLong
					$cmd .= " -profile:v baseline -preset slow -coder 0 -bf 0 -refs 1 -weightb 1 -level 31 -maxrate 10M -bufsize 10M";
				break;
				default:
					// in the default case just pass along the preset to ffmpeg
					$cmd .= " -vpre " . wfEscapeShellArg( $options['preset'] );
				break;
			}
		}
		if ( isset( $options['videoBitrate'] ) ) {
			$cmd .= " -b " . wfEscapeShellArg( $options['videoBitrate'] );
		}
		// Output mp4
		$cmd .= " -f mp4";
		return $cmd;
	}

	private function ffmpegAddVideoSizeOptions( $options ) {
		$cmd = '';
		// Get a local pointer to the file object
		$file = $this->getFile();

		// Check for aspect ratio ( we don't do anything with this right now)
		if ( isset( $options['aspect'] ) ) {
			$aspectRatio = $options['aspect'];
		} else {
			$aspectRatio = $file->getWidth() . ':' . $file->getHeight();
		}
		if ( isset( $options['maxSize'] ) ) {
			// Get size transform ( if maxSize is > file, file size is used:

			list( $width, $height ) = WebVideoTranscode::getMaxSizeTransform( $file, $options['maxSize'] );
			$cmd .= ' -s ' . intval( $width ) . 'x' . intval( $height );
		} elseif (
			( isset( $options['width'] ) && $options['width'] > 0 )
			&&
			( isset( $options['height'] ) && $options['height'] > 0 )
		) {
			$cmd .= ' -s ' . intval( $options['width'] ) . 'x' . intval( $options['height'] );
		}

		// Handle crop:
		$optionMap = [
			'cropTop' => '-croptop',
			'cropBottom' => '-cropbottom',
			'cropLeft' => '-cropleft',
			'cropRight' => '-cropright'
		];
		foreach ( $optionMap as $name => $cmdArg ) {
			if ( isset( $options[$name] ) ) {
				$cmd .= " $cmdArg " . wfEscapeShellArg( $options[$name] );
			}
		}
		return $cmd;
	}

	/**
	 * Adds ffmpeg shell options for webm
	 *
	 * @param array $options
	 * @param int $pass
	 * @return string
	 */
	private function ffmpegAddWebmVideoOptions( $options, $pass ) {
		global $wgFFmpegThreads, $wgFFmpegVP9RowMT;

		// Get a local pointer to the file object
		$file = $this->getFile();

		$cmd = ' -threads ' . intval( $wgFFmpegThreads );
		if ( $wgFFmpegVP9RowMT && $options['videoCodec'] === 'vp9' ) {
			// Macroblock row multithreading allows using more CPU cores
			// for VP9 encoding. This is not yet the default, and the option
			// will fail on a version of ffmpeg that is too old or is built
			// against a libvpx that is too old, so we have to enable it
			// conditionally for now.
			//
			// Requires libvpx 1.7 and ffmpeg 3.3.
			$cmd .= ' -row-mt 1';
		}

		// check for presets:
		if ( isset( $options['preset'] ) ) {
			if ( $options['preset'] == "360p" ) {
				$cmd .= " -vpre libvpx-360p";
			} elseif ( $options['preset'] == "720p" ) {
				$cmd .= " -vpre libvpx-720p";
			} elseif ( $options['preset'] == "1080p" ) {
				$cmd .= " -vpre libvpx-1080p";
			}
		}

		// Force to 4:2:0 chroma subsampling. Others are supported in Theora
		// and in VP9 profile 1, but Chrome and Edge don't grok them.
		$cmd .= ' -pix_fmt yuv420p';

		// Check for video quality:
		if ( isset( $options['videoQuality'] ) && $options['videoQuality'] >= 0 ) {
			// Map 0-10 to 63-0, higher values worse quality
			$quality = 63 - intval( intval( $options['videoQuality'] ) / 10 * 63 );
			$cmd .= " -qmin " . wfEscapeShellArg( $quality );
			$cmd .= " -qmax " . wfEscapeShellArg( $quality );
		}
		// libvpx-specific constant quality or constrained quality
		// note the range is different between VP8 and VP9
		if ( isset( $options['crf'] ) ) {
			$cmd .= " -crf " . wfEscapeShellArg( $options['crf'] );
		}

		// Check for video bitrate:
		if ( isset( $options['videoBitrate'] ) ) {
			$qmin = $options['qmin'] ?? 1;
			$qmax = $options['qmax'] ?? 51;
			$cmd .= " -qmin " . wfEscapeShellArg( $qmin );
			$cmd .= " -qmax " . wfEscapeShellArg( $qmax );

			$cmd .= " -vb " . wfEscapeShellArg( $options['videoBitrate'] * 1000 );
			if ( isset( $options['minrate'] ) ) {
				$cmd .= " -minrate " . wfEscapeShellArg( $options['minrate'] * 1000 );
			}
			if ( isset( $options['maxrate'] ) ) {
				$cmd .= " -maxrate " . wfEscapeShellArg( $options['maxrate'] * 1000 );
			}
		}
		// Set the codec:
		if ( $options['videoCodec'] === 'vp9' ) {
			$cmd .= " -vcodec libvpx-vp9";
			if ( isset( $options['tileColumns'] ) ) {
				$cmd .= ' -tile-columns ' . wfEscapeShellArg( $options['tileColumns'] );
			}
		} else {
			$cmd .= " -vcodec libvpx";
			if ( isset( $options['slices'] ) ) {
				$cmd .= ' -slices ' . wfEscapeShellArg( $options['slices'] );
			}
		}
		if ( isset( $options['altref'] ) ) {
			$cmd .= ' -auto-alt-ref 1';
			$cmd .= ' -lag-in-frames 25';
		}

		// Check for keyframeInterval
		if ( isset( $options['keyframeInterval'] ) ) {
			$cmd .= ' -g ' . wfEscapeShellArg( $options['keyframeInterval'] );
		}
		if ( isset( $options['keyframeIntervalMin'] ) ) {
			$cmd .= ' -keyint_min ' . wfEscapeShellArg( $options['keyframeIntervalMin'] );
		}
		if ( isset( $options['deinterlace'] ) ) {
			$cmd .= ' -deinterlace';
		}
		if ( $pass == 1 ) {
			// Make first pass faster...
			$cmd .= ' -speed 4';
		} elseif ( isset( $options['speed'] ) ) {
			$cmd .= ' -speed ' . wfEscapeShellArg( $options['speed'] );
		}

		// Output WebM
		$cmd .= " -f webm";

		return $cmd;
	}

	/**
	 * @param array $options
	 * @param int $pass
	 * @return string
	 */
	private function ffmpegAddAudioOptions( $options, $pass ) {
		$cmd = '';
		if ( isset( $options['audioQuality'] ) ) {
			$cmd .= " -aq " . wfEscapeShellArg( $options['audioQuality'] );
		}
		if ( isset( $options['audioBitrate'] ) ) {
			$cmd .= ' -ab ' . intval( $options['audioBitrate'] ) * 1000;
		}
		if ( isset( $options['samplerate'] ) ) {
			$cmd .= " -ar " . wfEscapeShellArg( $options['samplerate'] );
		}
		if ( isset( $options['channels'] ) ) {
			$cmd .= " -ac " . wfEscapeShellArg( $options['channels'] );
		}

		if ( isset( $options['audioCodec'] ) ) {
			$encoders = [
				'vorbis'	=> 'libvorbis',
				'opus'		=> 'libopus',
				'mp3'		=> 'libmp3lame',
			];
			if ( isset( $encoders[ $options['audioCodec'] ] ) ) {
				$codec = $encoders[ $options['audioCodec'] ];
			} else {
				$codec = $options['audioCodec'];
			}
			$cmd .= " -acodec " . wfEscapeShellArg( $codec );
			if ( $codec === 'aac' ) {
				// the aac encoder is currently "experimental" in libav 9? :P
				$cmd .= ' -strict experimental';
			}
		} else {
			// if no audio codec set use vorbis :
			$cmd .= " -acodec libvorbis ";
		}
		return $cmd;
	}

	/**
	 * Utility helper for midi to an audio format conversion
	 * @param array $options
	 * @return bool|string
	 */
	private function midiToAudioEncode( $options ) {
		global $wgTmhFluidsynthLocation, $wgFFmpegLocation, $wgTmhSoundfontLocation,
			$wgTranscodeBackgroundMemoryLimit;

		if ( !is_file( $this->getSourceFilePath() ) ) {
			return "source file is missing, " . $this->getSourceFilePath() . ". Encoding failed.";
		}

		$outputFileExt = $options['audioCodec'] == 'vorbis' ? '' : '.wav';

		// Set up the base command
		$cmdArgs = [
			wfEscapeShellArg( $wgTmhFluidsynthLocation ),
			'-T',
			$options['audioCodec'] == 'vorbis' ? 'oga' : 'wav', // wav for mp3
			wfEscapeShellArg( $wgTmhSoundfontLocation ),
			wfEscapeShellArg( $this->getSourceFilePath() ),
			'-F',
			wfEscapeShellArg( $this->getTargetEncodePath() . $outputFileExt )
		];

		$cmdString = implode( " ", $cmdArgs );

		$retval = 0;
		$shellOutput = $this->runShellExec( $cmdString, $retval );

		// Fluidsynth doesn't give error codes - $retval always stays 0
		if ( strpos( $shellOutput, "fluidsynth: error:" ) !== false ) {
			return $cmdString .
				"\n\nExitcode: $retval\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
				$shellOutput;
		}

		if ( $options['audioCodec'] == 'vorbis' ) {
			return true;
		} else {
			// For mp3, convert wav (previous command) to mp3 with ffmpeg
			$lameCmdArgs = [
				wfEscapeShellArg( $wgFFmpegLocation ),
				'-y',
				'-i',
				wfEscapeShellArg( $this->getTargetEncodePath() . $outputFileExt ),
				'-ss',
				wfEscapeShellArg( $options['starttime'] ?? '0' ),
			];

			if ( isset( $options['audioQuality'] ) ) {
				array_push( $lameCmdArgs, "-aq", wfEscapeShellArg( $options['audioQuality'] ) );
			}
			if ( isset( $options['audioBitrate'] ) ) {
				array_push( $lameCmdArgs, "-ab", intval( $options['audioBitrate'] ) * 1000 );
			}
			if ( isset( $options['samplerate'] ) ) {
				array_push( $lameCmdArgs, "-ar", wfEscapeShellArg( $options['samplerate'] ) );
			}
			if ( isset( $options['channels'] ) ) {
				array_push( $lameCmdArgs, "-ac", wfEscapeShellArg( $options['channels'] ) );
			}

			array_push(
				$lameCmdArgs,
				"-acodec",
				"libmp3lame",
				wfEscapeShellArg( $this->getTargetEncodePath() )
			);

			$lameCmdString = implode( " ", $lameCmdArgs );

			$shellOutput = $this->runShellExec( $lameCmdString, $retval );

			if ( $retval != 0 ) { // Retval from fluidsynth command
				return $lameCmdString .
					"\n\nExitcode: $retval\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
					$shellOutput;
			}
			return true;
		}
	}

	/**
	 * Runs the shell exec command.
	 * if $wgEnableBackgroundTranscodeJobs is enabled will mannage a background transcode task
	 * else it just directly passes off to wfShellExec
	 *
	 * @param string $cmd Command to be run
	 * @param string &$retval reference variable to return the exit code
	 * @return string
	 */
	public function runShellExec( $cmd, &$retval ) {
		global $wgTranscodeBackgroundTimeLimit,
			$wgTranscodeBackgroundMemoryLimit,
			$wgTranscodeBackgroundSizeLimit,
			$wgEnableNiceBackgroundTranscodeJobs;

		// For profiling
		$caller = wfGetCaller();

		// Check if background tasks are enabled
		if ( $wgEnableNiceBackgroundTranscodeJobs === false ) {
			// Directly execute the shell command:
			$limits = [
				"filesize" => $wgTranscodeBackgroundSizeLimit,
				"memory" => $wgTranscodeBackgroundMemoryLimit,
				"time" => $wgTranscodeBackgroundTimeLimit
			];
			return wfShellExec( $cmd . ' 2>&1', $retval, [], $limits,
				[ 'profileMethod' => $caller ] );
		}

		$encodingLog = $this->getTargetEncodePath() . '.stdout.log';
		$retvalLog = $this->getTargetEncodePath() . '.retval.log';
		// Check that we can actually write to these files
		// ( no point in running the encode if we can't write )
		Wikimedia\suppressWarnings();
		if ( !touch( $encodingLog ) || !touch( $retvalLog ) ) {
			Wikimedia\restoreWarnings();
			$retval = 1;
			return "Error could not write to target location";
		}
		Wikimedia\restoreWarnings();

		// Fork out a process for running the transcode
		$pid = pcntl_fork();
		if ( $pid == -1 ) {
			$errorMsg = '$wgEnableNiceBackgroundTranscodeJobs enabled but failed pcntl_fork';
			$retval = 1;
			$this->output( $errorMsg );
			return $errorMsg;
		} elseif ( $pid == 0 ) {
			// we are the child
			$this->runChildCmd( $cmd, $retval, $encodingLog, $retvalLog, $caller );
			// dont remove any temp files in the child process, this is done
			// once the parent is finished
			$this->targetEncodeFile->preserve();
			if ( $this->source instanceof TempFSFile ) {
				$this->source->preserve();
			}
			// exit with the same code as the transcode:
			exit( $retval );
		} else {
			// we are the parent monitor and return status
			return $this->monitorTranscode( $pid, $retval, $encodingLog, $retvalLog );
		}
	}

	/**
	 * @param string $cmd
	 * @param string &$retval
	 * @param string $encodingLog
	 * @param string $retvalLog
	 * @param string $caller The calling method
	 */
	public function runChildCmd( $cmd, &$retval, $encodingLog, $retvalLog, $caller ) {
		global $wgTranscodeBackgroundTimeLimit, $wgTranscodeBackgroundMemoryLimit,
		$wgTranscodeBackgroundSizeLimit;

		// In theory we should use pcntl_exec but not sure how to get the stdout, ensure
		// we don't max php memory with the same protections provided by wfShellExec.

		// pcntl_exec requires a direct path to the exe and arguments as an array:
		// $cmd = explode(' ', $cmd );
		// $baseCmd = array_shift( $cmd );
		// print "run:" . $baseCmd . " args: " . print_r( $cmd, true );
		// $status  = pcntl_exec($baseCmd , $cmd );

		// Directly execute the shell command:
		// global $wgTranscodeBackgroundPriority;
		// $status =
		// wfShellExec( 'nice -n ' . $wgTranscodeBackgroundPriority . ' '. $cmd . ' 2>&1', $retval );
		$limits = [
			"filesize" => $wgTranscodeBackgroundSizeLimit,
			"memory" => $wgTranscodeBackgroundMemoryLimit,
			"time" => $wgTranscodeBackgroundTimeLimit
		];
		$status = wfShellExec( $cmd . ' 2>&1', $retval, [], $limits,
			[ 'profileMethod' => $caller ] );

		// Output the status:
		Wikimedia\suppressWarnings();
		file_put_contents( $encodingLog, $status );
		// Output the retVal to the $retvalLog
		file_put_contents( $retvalLog, $retval );
		Wikimedia\restoreWarnings();
	}

	/**
	 * @param int $pid
	 * @param string &$retval
	 * @param string $encodingLog
	 * @param string $retvalLog
	 * @return string
	 */
	public function monitorTranscode( $pid, &$retval, $encodingLog, $retvalLog ) {
		global $wgTranscodeBackgroundTimeLimit, $wgLang;
		$errorMsg = '';
		$loopCount = 0;
		$oldFileSize = 0;
		$startTime = time();
		$fileIsNotGrowing = false;

		$this->output( "Encoding with pid: $pid \npcntl_waitpid: " .
			pcntl_waitpid( $pid, $status, WNOHANG | WUNTRACED ) .
			"\nisProcessRunning: " . self::isProcessRunningKillZombie( $pid ) . "\n" );

		// Check that the child process is still running
		// ( note this does not work well with  pcntl_waitpid for some reason :( )
		while ( self::isProcessRunningKillZombie( $pid ) ) {
			// $this->output( "$pid is running" );

			// Check that the target file is growing ( every 5 seconds )
			if ( $loopCount == 10 ) {
				// only run check if we are outputing to target file
				// ( two pass encoding does not output to target on first pass )
				clearstatcache();
				$newFileSize = is_file(
					$this->getTargetEncodePath()
				) ? filesize( $this->getTargetEncodePath() ) : 0;
				// Don't start checking for file growth until we have an initial positive file size:
				if ( $newFileSize > 0 ) {
					$this->output( $wgLang->formatSize( $newFileSize ) . ' Total size, encoding ' .
						$wgLang->formatSize( ( $newFileSize - $oldFileSize ) / 5 ) . ' per second' );
					if ( $newFileSize == $oldFileSize ) {
						if ( $fileIsNotGrowing ) {
							$errorMsg = "Target File is not increasing in size, kill process.";
							$this->output( $errorMsg );
							// file is not growing in size, kill proccess
							$retval = 1;

							// posix_kill( $pid, 9);
							self::killProcess( $pid );
							break;
						}
						// Wait an additional 5 seconds of the file not growing to confirm
						// the transcode is frozen.
						$fileIsNotGrowing = true;
					} else {
						$fileIsNotGrowing = false;
					}
					$oldFileSize = $newFileSize;
				}
				// reset the loop counter
				$loopCount = 0;
			}

			// Check if we have global job run-time has been exceeded:
			if (
				$wgTranscodeBackgroundTimeLimit && time() - $startTime > $wgTranscodeBackgroundTimeLimit
			) {
				$errorMsg = "Encoding exceeded max job run time ( "
					. TimedMediaHandler::seconds2npt( $wgTranscodeBackgroundTimeLimit ) . " ), kill process.";
				$this->output( $errorMsg );
				// File is not growing in size, kill proccess
				$retval = 1;
				// posix_kill( $pid, 9);
				self::killProcess( $pid );
				break;
			}

			// Sleep for one second before repeating loop
			$loopCount++;
			sleep( 1 );
		}

		$returnPcntl = pcntl_wexitstatus( $status );
		// check status
		Wikimedia\suppressWarnings();
		$returnCodeFile = file_get_contents( $retvalLog );
		Wikimedia\restoreWarnings();

		// File based exit code seems more reliable than pcntl_wexitstatus
		$retval = $returnCodeFile;

		// return the encoding log contents ( will be inserted into error table if an error )
		// ( will be ignored and removed if success )
		if ( $errorMsg != '' ) {
			$errorMsg .= "\n\n";
		}
		return $errorMsg . file_get_contents( $encodingLog );
	}

	/**
	 * check if proccess is running and not a zombie
	 * @param int $pid
	 * @return bool
	 */
	public static function isProcessRunningKillZombie( $pid ) {
		exec( "ps $pid", $processState );
		if ( !isset( $processState[1] ) ) {
			return false;
		}
		if ( strpos( $processState[1], '<defunct>' ) !== false ) {
			// posix_kill( $pid, 9);
			self::killProcess( $pid );
			return false;
		}
		return true;
	}

	/**
	 * Kill Application PID
	 *
	 * @param int $pid
	 * @return bool
	 */
	public static function killProcess( $pid ) {
		exec( "kill -9 $pid" );
		exec( "ps $pid", $processState );
		if ( isset( $processState[1] ) ) {
			return false;
		}
		return true;
	}

}
