<?php
/**
 * Job for transcode jobs
 *
 * @file
 * @ingroup JobQueue
 */

namespace MediaWiki\TimedMediaHandler\WebVideoTranscode;

use CdnCacheUpdate;
use Exception;
use File;
use FSFile;
use Job;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\TimedMediaHandler\HLS\Segmenter;
use MediaWiki\TimedMediaHandler\TimedMediaHandler;
use MediaWiki\Title\Title;
use TempFSFile;
use Wikimedia\AtEase\AtEase;

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

	/** @var TempFSFile|null */
	public $targetEncodeFile;

	/** @var TempFSFile|null */
	public $targetPlaylistFile;

	/** @var string|null|false */
	public $sourceFilePath;

	/** @var File */
	public $file;

	/** @var FSFile|null */
	public $source;

	/**
	 * @param Title $title
	 * @param array $params
	 * @param int $id
	 */
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
			$this->file = MediaWikiServices::getInstance()->getRepoGroup()->getLocalRepo()
				->newFile( $this->title );
		}
		return $this->file;
	}

	/**
	 * @return string
	 */
	private function getTargetEncodePath() {
		if ( !$this->targetEncodeFile ) {
			$this->targetEncodeFile = $this->fileTarget();
		}
		return $this->targetEncodeFile->getPath();
	}

	/**
	 * @return string
	 */
	private function getTargetPlaylistPath() {
		if ( !$this->targetPlaylistFile ) {
			$this->targetPlaylistFile = $this->fileTarget( '.m3u8' );
		}
		return $this->targetPlaylistFile->getPath();
	}

	/**
	 * @param string $suffix
	 * @return TempFSFile
	 */
	private function fileTarget( $suffix = '' ) {
		$base = $this->getFile();
		$transcodeKey = $this->params[ 'transcodeKey' ];
		$file = WebVideoTranscode::getTargetEncodeFile( $base, $transcodeKey, $suffix );
		if ( !$file ) {
			throw new Exception( 'Internal state error' );
		}
		$file->bind( $this );
		return $file;
	}

	/**
	 * purge temporary encode target
	 */
	private function purgeTargetEncodeFile() {
		if ( $this->targetEncodeFile ) {
			$this->targetEncodeFile->purge();
			$this->targetEncodeFile = null;
		}
		if ( $this->targetPlaylistFile ) {
			$this->targetPlaylistFile->purge();
			$this->targetPlaylistFile = null;
		}
	}

	/**
	 * @return string|false
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
		$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
		$dbw = $lbFactory->getPrimaryDatabase();
		$dbw->newUpdateQueryBuilder()
			->update( 'transcode' )
			->set( [
				'transcode_time_error' => $dbw->timestamp(),
				'transcode_error' => $error
			] )
			->where( [
					'transcode_image_name' => $this->getFile()->getName(),
					'transcode_key' => $transcodeKey
			] )
			->caller( __METHOD__ )
			->execute();
		$this->setLastError( $error );
	}

	/**
	 * Run the transcode request
	 * @return bool success
	 */
	public function run() {
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
			if ( !isset( $options['audioCodec'] ) ) {
				throw new Exception( 'Invalid audio track options' );
			}
			$this->output( "Encoding to audio codec: " . $options['audioCodec'] );
		} else {
			if ( !isset( $options['videoCodec'] ) ) {
				throw new Exception( 'Invalid video track options' );
			}
			$this->output( "Encoding to codec: " . $options['videoCodec'] );
		}
		$lbFactory = MediaWikiServices::getInstance()->getDBLoadBalancerFactory();
		$dbw = $lbFactory->getPrimaryDatabase();

		// Check if we have "already started" the transcode ( possible error )
		$dbStartTime = $dbw->newSelectQueryBuilder()
			->select( 'transcode_time_startwork' )
			->from( 'transcode' )
			->where( [
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			] )
			->caller( __METHOD__ )
			->fetchField();
		if ( $dbStartTime !== null ) {
			$error = 'Error, running transcode job, for job that has already started';
			$this->output( $error );
			return true;
		}

		// Update the transcode table letting it know we have "started work":
		$jobStartTimeCache = wfTimestamp( TS_UNIX );
		$dbw->newUpdateQueryBuilder()
			->update( 'transcode' )
			->set( [ 'transcode_time_startwork' => $dbw->timestamp( $jobStartTimeCache ) ] )
			->where( [
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			] )
			->caller( __METHOD__ )
			->execute();

		// Avoid contention and "server has gone away" errors as
		// the transcode will take a very long time in some cases
		$lbFactory->commitPrimaryChanges( __METHOD__ );
		$lbFactory->flushPrimarySessions( __METHOD__ );
		$lbFactory->flushReplicaSnapshots( __METHOD__ );
		// We can't just leave the connection open either or it will
		// eat up resources and block new connections, so make sure
		// everything is dead and gone.
		$lbFactory->closeAll();

		// Check the codec see which encode method to call;
		$streaming = $options['streaming'] ?? false;
		$videoCodec = $options['videoCodec'] ?? '';
		$codecs = [ 'vp8', 'vp9', 'h264', 'h263', 'mpeg4', 'mjpeg' ];
		if ( isset( $options[ 'novideo' ] ) ) {
			if ( $file->getMimeType() === 'audio/midi' ) {
				$status = $this->midiToAudioEncode( $options );
			} else {
				$status = $this->ffmpegEncode( $options );
			}
		} elseif ( in_array( $videoCodec, $codecs ) ) {
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
			wfDebug( 'Error unknown codec:' . $videoCodec );
			$status = 'Error unknown target encode codec:' . $videoCodec;
		}

		// Remove any log files,
		// all useful info should be in status and or we are done with 2 pass encoding
		$this->removeFfmpegLogFiles();

		// Reconnect to the database...
		$dbw = $lbFactory->getPrimaryDatabase();

		// Do a quick check to confirm the job was not restarted or removed while we were transcoding
		// Confirm that the in memory $jobStartTimeCache matches db start time
		$dbStartTime = $dbw->newSelectQueryBuilder()
			->select( 'transcode_time_startwork' )
			->from( 'transcode' )
			->where( [
				'transcode_image_name' => $this->getFile()->getName(),
				'transcode_key' => $transcodeKey
			] )
			->caller( __METHOD__ )
			->fetchField();

		// Check for ( hopefully rare ) issue of or job restarted while transcode in progress
		if ( $dbStartTime === null || $jobStartTimeCache !== wfTimestamp( TS_UNIX, $dbStartTime ) ) {
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

		// If status is ok and target does not exist, reset status
		if ( $status === true && !is_file( $this->getTargetEncodePath() ) ) {
			$status = 'Target does not exist: ' . $this->getTargetEncodePath();
		}

		// If status is ok and target is larger than 0 bytes
		if ( $status === true && filesize( $this->getTargetEncodePath() ) > 0 ) {

			$file = $this->getFile();
			$mediaFilename = WebVideoTranscode::getTranscodeFileBaseName( $file, $transcodeKey );
			$mediaPath = WebVideoTranscode::getDerivativeFilePath( $file, $transcodeKey );

			if ( $streaming === 'hls' ) {
				$playlistKey = $transcodeKey . '.m3u8';
				$playlistFilename = WebVideoTranscode::getTranscodeFileBaseName( $file, $playlistKey );
				$playlistPath = WebVideoTranscode::getDerivativeFilePath( $file, $playlistKey );
				$playlistTemp = $this->getTargetPlaylistPath();

				$segmenter = Segmenter::segment( $this->getTargetEncodePath() );
				// @fixme put the 10-second segment target in a constant somewhere
				$segmenter->consolidate( 10 );
				$segmenter->rewrite();
				$playlist = $segmenter->playlist( 10, $mediaFilename );

				file_put_contents( $playlistTemp, $playlist );
			} else {
				$playlistTemp = null;
				$playlistPath = null;
			}

			$storeOptions = null;
			if (
				strpos( $options['type'], '/ogg' ) !== false &&
				$file->getLength()
			) {
				$storeOptions = [];
				// Ogg files need a duration header for firefox
				$storeOptions['headers']['X-Content-Duration'] = (float)$file->getLength();
			}

			// Avoid "server has gone away" errors as copying can be slow
			$lbFactory->commitPrimaryChanges( __METHOD__ );
			$lbFactory->flushPrimarySessions( __METHOD__ );
			$lbFactory->flushReplicaSnapshots( __METHOD__ );
			$lbFactory->closeAll();

			// Copy derivative from the FS into storage at $finalDerivativeFilePath
			$result = $file->getRepo()->quickImport(
				// temp file
				$this->getTargetEncodePath(),
				// storage
				$mediaPath,
				$storeOptions
			);
			if ( $result->isOK() && $streaming === 'hls' && $playlistTemp && $playlistPath ) {
				$result = $file->getRepo()->quickImport(
					// temp file
					$playlistTemp,
					// storage
					$playlistPath
				);
				if ( $result->isOK() ) {
					WebVideoTranscode::updateStreamingManifests( $file );
				}
			}

			if ( !$result->isOK() ) {
				// no need to invalidate all pages with video.
				// Because all pages remain valid ( no $transcodeKey derivative )
				// just clear the file page ( so that the transcode table shows the error )
				$this->title->invalidateCache();
				$this->setTranscodeError( $transcodeKey, $result->getWikiText() );
				$status = false;
			} else {
				$bitrate = round(
					(int)( filesize( $this->getTargetEncodePath() ) / $file->getLength() ) * 8
				);
				// Wikimedia\restoreWarnings();
				// Reconnect to the database...
				$dbw = $lbFactory->getPrimaryDatabase();
				// Update the transcode table with success time:
				$dbw->newUpdateQueryBuilder()
					->update( 'transcode' )
					->set( [
						'transcode_error' => '',
						'transcode_time_error' => null,
						'transcode_time_success' => $dbw->timestamp(),
						'transcode_final_bitrate' => $bitrate
					] )
					->where( [
						'transcode_image_name' => $this->getFile()->getName(),
						'transcode_key' => $transcodeKey,
					] )
					->caller( __METHOD__ )
					->execute();
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
		$urls = [ $url ];
		if ( $streaming === 'hls' ) {
			$urls[] = "$url.m3u8";
		}
		$update = new CdnCacheUpdate( $urls );
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
					if ( $ext === 'log' && strpos( $log_path, $path ) === 0 ) {
						AtEase::suppressWarnings();
						unlink( $log_path );
						AtEase::restoreWarnings();
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
	 * @return true|string
	 */
	private function ffmpegEncode( $options, $pass = 0 ) {
		global $wgFFmpegLocation, $wgTranscodeBackgroundMemoryLimit;
		global $wgTranscodeBackgroundSizeLimit, $wgTranscodeSoftSizeLimit;

		if ( !is_file( $this->getSourceFilePath() ) ) {
			return "source file is missing, " . $this->getSourceFilePath() . ". Encoding failed.";
		}

		// Set up the base command
		$cmd = wfEscapeShellArg(
			$wgFFmpegLocation
		) . ' -nostdin -y -i ' . wfEscapeShellArg( $this->getSourceFilePath() );

		if ( isset( $options['vpre'] ) ) {
			$cmd .= ' -vpre ' . wfEscapeShellArg( $options['vpre'] );
		}
		$interval = 10;
		$fps = 0;
		if ( isset( $options['novideo'] ) ) {
			$cmd .= " -vn ";
		} else {
			$fps = $this->effectiveFrameRate( $options );
			if ( isset( $options['framerate'] ) ) {
				$cmd .= " -r " . wfEscapeShellArg( $options['framerate'] );
			} else {
				// Note -fpsmax is not available on Wikimedia's Debian as of 2023-02-02
				//
				//   $cmd .= " -fpsmax " . wfEscapeShellArg( $options['fpsmax'] );
				//   $cmd .= " -fpsmax " . self::MAX_FPS;
				//
				// Instead, manually check the detected framerate.
				// Note some files report incorrectly via GetID3, and may
				// end up actually increasing in frame rate because of this!
				$orig = $this->frameRate();
				if ( $this->isInterlaced() ) {
					$orig *= 2;
				}
				if ( $orig > $fps ) {
					$cmd .= " -r " . wfEscapeShellArg( strval( $fps ) );
				}
			}

			switch ( $options['videoCodec'] ) {
			case 'vp8':
			case 'vp9':
				$cmd .= $this->ffmpegAddWebmVideoOptions( $options, $pass );
				break;
			case 'h264':
				$cmd .= $this->ffmpegAddH264VideoOptions( $options, $pass );
				break;
			case 'mpeg4':
				$cmd .= $this->ffmpegAddMPEG4VideoOptions( $options, $pass );
				break;
			default:
				$cmd .= $this->ffmpegAddGenericVideoOptions( $options );
			}

			// Check for keyframeInterval
			$defaultKeyframeInterval = round( $fps * $interval );
			$keyframeInterval = $options['keyframeInterval'] ?? $defaultKeyframeInterval;
			$cmd .= ' -g ' . wfEscapeShellArg( $keyframeInterval );
			if ( isset( $options['keyframeIntervalMin'] ) ) {
				$cmd .= ' -keyint_min ' . wfEscapeShellArg( $options['keyframeIntervalMin'] );
			}

			if ( isset( $options['videoBitrate'] ) ) {
				$base = $this->expandRate( $options['videoBitrate'] );
				$bitrate = $this->scaleRate( $options, $base );
				$cmd .= " -b:v $bitrate";

				// Estimate the output file size in KiB and bail out early
				// if it's potentially very large. Could be a denial of
				// service, or just a large file that probably is poorly
				// compressed.
				$duration = (float)$this->file->getLength();
				$estimatedSize = round( ( $bitrate / 8 ) * $duration / 1024 );
				if ( $wgTranscodeBackgroundSizeLimit > 0 && $estimatedSize > $wgTranscodeBackgroundSizeLimit ) {
					// This hard limit cannot be overridden by admins, except by raising the limit in config.
					// @todo return an error code that can be localized later
					return "estimated file size $estimatedSize KiB over hard limit $wgTranscodeBackgroundSizeLimit KiB";
				}

				if ( $wgTranscodeSoftSizeLimit > 0 && $estimatedSize > $wgTranscodeSoftSizeLimit ) {
					// This soft limit can be overridden when a transcode is reset by hand via the web UI
					// or API, or requeueTranscodes.php with --manual-override option.
					$manualOverride = $this->params['manualOverride'] ?? false;
					if ( !$manualOverride ) {
						// @todo return an error code that can be localized later
						return "estimated file size $estimatedSize KiB over soft limit $wgTranscodeSoftSizeLimit KiB";
					}
				}

				if ( isset( $options['minrate'] ) ) {
					$minrate = $this->scaleRate( $options, $options['minrate'] );
					$cmd .= " -minrate $minrate";
				}
				if ( isset( $options['maxrate'] ) ) {
					$maxrate = $this->scaleRate( $options, $options['maxrate'] );
					$cmd .= " -maxrate $maxrate";
				}
				if ( isset( $options['bufsize'] ) ) {
					$bufsize = $this->scaleRate( $options, $options['bufsize'] );
					$cmd .= " -bufsize $bufsize";
				}
			}

			// If necessary, add deinterlacing options
			$cmd .= $this->ffmpegAddDeinterlaceOptions( $options );
			// Add size options:
			$cmd .= $this->ffmpegAddVideoSizeOptions( $options );
		}

		if ( !MediaWikiServices::getInstance()->getMainConfig()->get( 'UseFFmpeg2' ) ) {
			// Work around https://trac.ffmpeg.org/ticket/6375 in ffmpeg 3.4/4.0
			// Sometimes caused transcode failures saying things like:
			// "1 frames left in the queue on closing"
			$cmd .= ' -max_muxing_queue_size 1024';
		}

		// Check for start time
		if ( isset( $options['starttime'] ) ) {
			$cmd .= ' -ss ' . wfEscapeShellArg( $options['starttime'] );
		} else {
			$options['starttime'] = 0;
		}
		// Check for end time:
		if ( isset( $options['endtime'] ) ) {
			$duration = (int)$options['endtime'] - (int)$options['starttime'];
			$cmd .= ' -t ' . $duration;
		}

		if ( $pass === 1 || isset( $options['noaudio'] ) ) {
			$cmd .= ' -an';
		} else {
			$cmd .= $this->ffmpegAddAudioOptions( $options, $pass );
		}

		if ( $pass !== 0 ) {
			$cmd .= " -pass " . wfEscapeShellArg( (string)$pass );
			$cmd .= " -passlogfile " . wfEscapeShellArg( $this->getTargetEncodePath() . '.log' );
		}

		$streaming = $options['streaming'] ?? false;
		$target = $this->getTargetEncodePath();
		$playlist = false;

		$transcodeKey = $this->params[ 'transcodeKey' ];
		$extension = substr( $transcodeKey, strrpos( $transcodeKey, '.' ) + 1 );

		if ( WebVideoTranscode::isBaseMediaFormat( $extension ) ) {
			$cmd .= " -movflags +faststart";
		}

		if ( $streaming === 'hls' ) {
			$playlist = $target . ".m3u8";

			if ( WebVideoTranscode::isBaseMediaFormat( $extension ) ) {
				// Don't use the HLS muxer, as it'll want to manage
				// filenames and we have to rewrite everything anyway.
				// We'll generate an .m3u8 from the file structure after.

				if ( isset( $options['novideo'] ) || isset( $options['intraframe'] ) ) {
					// Audio-only tracks should be fragmented around the standard interval.
					// Intraframe-only codecs like Motion-JPEG should also be treated this way.
					$cmd .= " -movflags +empty_moov+default_base_moof";
					$cmd .= " -frag_duration {$interval}000000";
				} else {
					// Video keyframe interval is set to approximate the desired interval, but
					// they may occur whenever the encoder thinks they would be desirable such
					// as a visible scene change.
					$cmd .= " -movflags +frag_keyframe+empty_moov+default_base_moof";
				}

				// This is needed for opus on debian bullseye
				$cmd .= " -strict experimental";
			} elseif ( $extension === 'mp3' ) {
				// No additional options needed at present.
			} else {
				return "Invalid HLS track media type, expected .mp4, .m4v, .m4a, .mov, .3gp, or .mp3";
			}
		}

		// And the output target:
		if ( $pass === 1 ) {
			$cmd .= ' /dev/null';
		} else {
			$cmd .= " " . wfEscapeShellArg( $target );
		}

		$this->output( "Running cmd: \n\n" . $cmd . "\n" );

		// Right before we output remove the old file
		$shellOutput = $this->runShellExec( $cmd, $retval );

		if ( $retval !== 0 ) {
			return $cmd .
				"\n\nExitcode: $retval\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
				$shellOutput;
		}

		return true;
	}

	// Bitrates and keyframe distances are specified for this
	// common frame rate (30), and scaled accordingly to accomodate
	// higher frame rates.
	private const DEFAULT_FPS = 30;
	private const MAX_FPS = 60;
	private const MIN_FPS = 24;

	/**
	 * Scale a bitrate or frame count according to the frame rate
	 * of the file versus the default frame rate. This is not a
	 * straight linear multiplication; it's biased to reduce impact
	 * beyond 30 fps, to 1.5x base at 60 fps.
	 *
	 * @param array $options
	 * @param string|int $rate
	 * @return int
	 */
	private function scaleRate( $options, $rate ) {
		$fps = $this->effectiveFrameRate( $options );
		$base = $this->expandRate( $rate );

		$lofps = min( $fps, self::DEFAULT_FPS );
		$hifps = $fps - $lofps;
		$scaled = $base * $lofps / self::DEFAULT_FPS +
			0.5 * $base * $hifps / self::DEFAULT_FPS;
		return (int)$scaled;
	}

	/**
	 * Expand a bitrate that may have a k/m/g suffix
	 *
	 * @param string|int $rate
	 * @return int
	 */
	private function expandRate( $rate ) {
		return WebVideoTranscode::expandRate( $rate );
	}

	/**
	 * Grab the frame rate from the file, bounded by
	 * format-specific or generic limitations.
	 * Suitable for scaling linear parameters like the
	 * target bit rate.
	 *
	 * @param array $options
	 * @return float
	 */
	private function effectiveFrameRate( $options ) {
		if ( isset( $options['framerate'] ) ) {
			// fixed framerate
			$fps = $this->fractionToFloat( $options['framerate'] );
		} else {
			// @todo getid3 gets this wrong on some WebM input files
			// consider reading from ffmpeg or ffprobe...
			// We cap it, but this can cause a 29.97fps file to use
			// the 60fps bitrate. Worst case it's a bloated file.
			$fps = $this->frameRate();
		}
		if ( $this->shouldFrameDouble( $options ) ) {
			$fps *= 2;
		}

		if ( $fps < self::MIN_FPS ) {
			return self::MIN_FPS;
		}
		if ( isset( $options['fpsmax'] ) ) {
			$max = $this->fractionToFloat( $options['fpsmax'] );
		} else {
			$max = self::MAX_FPS;
		}
		if ( $fps > $max ) {
			return $max;
		}
		return $fps;
	}

	/**
	 * @param string $str
	 * @return float
	 */
	private function fractionToFloat( $str ) {
		$fraction = explode( '/', $str, 2 );
		if ( count( $fraction ) > 1 ) {
			return (float)$fraction[0] / (float)$fraction[1];
		}
		return (float)$str;
	}

	/**
	 * Return the actual frame rate of the file, or the default
	 * if can't retrieve it.
	 *
	 * @return float
	 */
	private function frameRate() {
		$file = $this->getFile();
		$handler = $file->getHandler();
		if ( $handler instanceof TimedMediaHandler ) {
			$fps = $handler->getFrameRate( $file );
			if ( $fps ) {
				return $fps;
			}
		}
		return self::DEFAULT_FPS;
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
		$cmd = " -threads " . (int)$wgFFmpegThreads . " -vcodec libx264";
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

		$cmd .= ' -pix_fmt yuv420p';
		$cmd .= ' -rc-lookahead 16';

		// Output mp4
		$cmd .= " -f mp4";
		return $cmd;
	}

	/**
	 * Adds ffmpeg shell options for h264
	 *
	 * @param array $options
	 * @param int $pass
	 * @return string
	 */
	public function ffmpegAddMPEG4VideoOptions( $options, $pass ) {
		$cmd = " -vcodec mpeg4";

		// Force to 4:2:0 chroma subsampling.
		$cmd .= ' -pix_fmt yuv420p';

		// needed for 2-pass to override file type detection
		$cmd .= " -f mp4";

		return $cmd;
	}

	/**
	 * @param array $options
	 * @return string
	 */
	private function ffmpegAddGenericVideoOptions( $options ) {
		$cmd = ' -vcodec ' . wfEscapeShellArg( $options['videoCodec'] );

		// Force to 4:2:0 chroma subsampling.
		$cmd .= ' -pix_fmt yuv420p';

		return $cmd;
	}

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	private function ffmpegAddVideoSizeOptions( $options ) {
		$cmd = '';
		// Get a local pointer to the file object
		$file = $this->getFile();

		// Check for aspect ratio
		$aspectRatio = $options['aspect'] ?? $file->getWidth() . ':' . $file->getHeight();
		if ( ( isset( $options['width'] ) && $options['width'] > 0 )
			&&
			( isset( $options['height'] ) && $options['height'] > 0 )
		) {
			$cmd .= ' -s ' . (int)$options['width'] . 'x' . (int)$options['height'];
			$cmd .= ' -aspect ' . wfEscapeShellArg( $aspectRatio );
		} elseif ( isset( $options['maxSize'] ) ) {
			// Get size transform ( if maxSize is > file, file size is used:

			[ $width, $height ] = WebVideoTranscode::getMaxSizeTransform( $file, $options['maxSize'] );
			$cmd .= ' -s ' . (int)$width . 'x' . (int)$height;
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

		$cmd = ' -threads ' . (int)$wgFFmpegThreads;
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

		// Force to 4:2:0 chroma subsampling. Others are supported in Theora
		// and in VP9 profile 1, but Chrome and Edge don't grok them.
		$cmd .= ' -pix_fmt yuv420p';

		// Check for video quality:
		if ( isset( $options['videoQuality'] ) && $options['videoQuality'] >= 0 ) {
			// Map 0-10 to 63-0, higher values worse quality
			$quality = 63 - (int)( (int)$options['videoQuality'] / 10 * 63 );
			$options['qmax'] = (string)$quality;
			$options['qmin'] = (string)$quality;
		}
		if ( isset( $options['qmin'] ) ) {
			$cmd .= " -qmin " . wfEscapeShellArg( $options['qmin'] );
		}
		if ( isset( $options['qmax'] ) ) {
			$cmd .= " -qmax " . wfEscapeShellArg( $options['qmax'] );
		}
		// libvpx-specific constant quality or constrained quality
		// note the range is different between VP8 and VP9
		if ( isset( $options['crf'] ) ) {
			$cmd .= " -crf " . wfEscapeShellArg( $options['crf'] );
		}

		// Set the codec:
		if ( $options['videoCodec'] === 'vp9' ) {
			$cmd .= " -vcodec libvpx-vp9";
			if ( isset( $options['tileColumns'] ) ) {
				$cmd .= ' -tile-columns ' . wfEscapeShellArg( $options['tileColumns'] );
			}
			if ( isset( $options['tileRows'] ) ) {
				$cmd .= ' -tile-rows ' . wfEscapeShellArg( $options['tileRows'] );
			}
		} else {
			$cmd .= " -vcodec libvpx";
			if ( isset( $options['slices'] ) ) {
				$cmd .= ' -slices ' . wfEscapeShellArg( $options['slices'] );
			}
		}
		if ( isset( $options['altref'] ) ) {
			if ( $options['altref'] === '0' ) {
				$cmd .= ' -auto-alt-ref 0';
			} else {
				$cmd .= ' -auto-alt-ref 1';
			}
		}
		if ( isset( $options['lagInFrames'] ) ) {
			$cmd .= ' -lag-in-frames ' . wfEscapeShellArg( $options['lagInFrames'] );
		}

		if ( isset( $options['quality'] ) ) {
			$cmd .= ' -quality ' . wfEscapeShellArg( $options['quality'] );
		} else {
			$cmd .= ' -quality good';
		}

		if ( $pass === 1 ) {
			// Make first pass faster...
			$cmd .= ' -speed 4';
		} elseif ( isset( $options['speed'] ) ) {
			$cmd .= ' -speed ' . wfEscapeShellArg( $options['speed'] );
		}

		// Output WebM
		$streaming = $options['streaming'] ?? false;
		if ( $streaming === 'hls' ) {
			$cmd .= " -f mp4";
		} else {
			$cmd .= " -f webm";
		}

		return $cmd;
	}

	/**
	 * @return bool
	 */
	private function isInterlaced() {
		$handler = $this->file->getHandler();
		return ( $handler instanceof TimedMediaHandler && $handler->isInterlaced( $this->file ) );
	}

	/**
	 * Whether to produce one frame per field when deinterlacing.
	 * This will double the output frame rate.
	 *
	 * @param array $options
	 * @return bool
	 */
	private function shouldFrameDouble( $options ) {
		if ( $this->isInterlaced() ) {
			if ( isset( $options['framerate'] ) ) {
				// Fixed framerate, don't mess with it.
				return false;
			}
			if ( isset( $options['fpsmax'] ) && $this->fractionToFloat( $options['fpsmax'] ) < 60 ) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * @param array $options
	 * @return string
	 */
	private function ffmpegAddDeinterlaceOptions( $options ) {
		if ( $this->isInterlaced() ) {
			if ( $this->shouldFrameDouble( $options ) ) {
				// Send one frame per field for full motion smoothness.
				return ' -vf yadif=1';
			}
			// Send one frame per field
			return ' -vf yadif=0';
		}
		return '';
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
			$cmd .= " -ab " . $this->expandRate( $options['audioBitrate'] );
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
			$codec = $encoders[$options['audioCodec']] ?? $options['audioCodec'];
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
	 * @return true|string
	 */
	private function midiToAudioEncode( $options ) {
		global $wgTmhFluidsynthLocation, $wgFFmpegLocation, $wgTmhSoundfontLocation,
			$wgTranscodeBackgroundMemoryLimit;

		if ( !is_file( $this->getSourceFilePath() ) ) {
			return "source file is missing, " . $this->getSourceFilePath() . ". Encoding failed.";
		}

		$outputFileExt = $options['audioCodec'] === 'vorbis' ? '' : '.wav';

		// Set up the base command
		$cmdArgs = [
			wfEscapeShellArg( $wgTmhFluidsynthLocation ),
			'-T',
			// wav for mp3
			$options['audioCodec'] === 'vorbis' ? 'oga' : 'wav',
			wfEscapeShellArg( $wgTmhSoundfontLocation ),
			wfEscapeShellArg( $this->getSourceFilePath() ),
			'-F',
			wfEscapeShellArg( $this->getTargetEncodePath() . $outputFileExt )
		];

		$cmdString = implode( " ", $cmdArgs );

		$shellOutput = $this->runShellExec( $cmdString, $retval );
		'@phan-var int $retval';

		// Fluidsynth doesn't give error codes - $retval always stays 0
		if ( strpos( $shellOutput, "fluidsynth: error:" ) !== false ) {
			return $cmdString .
				"\n\nExitcode: " . $retval . "\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
				$shellOutput;
		}

		if ( $options['audioCodec'] === 'vorbis' ) {
			return true;
		}

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
			array_push( $lameCmdArgs, "-ab", $this->expandRate( $options['audioBitrate'] ) );
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

		// Retval from fluidsynth command
		if ( $retval !== 0 ) {
			return $lameCmdString .
				"\n\nExitcode: $retval\nMemory: $wgTranscodeBackgroundMemoryLimit\n\n" .
				$shellOutput;
		}
		return true;
	}

	/**
	 * Runs the shell exec command.
	 * if $wgEnableBackgroundTranscodeJobs is enabled will mannage a background transcode task
	 * else it just directly passes off to wfShellExec
	 *
	 * @param string $cmd Command to be run
	 * @param int &$retval reference variable to return the exit code
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
		AtEase::suppressWarnings();
		if ( !touch( $encodingLog ) || !touch( $retvalLog ) ) {
			AtEase::restoreWarnings();
			$retval = 1;
			return "Error could not write to target location";
		}
		AtEase::restoreWarnings();

		// Fork out a process for running the transcode
		$pid = pcntl_fork();
		if ( $pid === -1 ) {
			$errorMsg = '$wgEnableNiceBackgroundTranscodeJobs enabled but failed pcntl_fork';
			$retval = 1;
			$this->output( $errorMsg );
			return $errorMsg;
		}

		if ( $pid === 0 ) {
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
		}

		// we are the parent monitor and return status
		return $this->monitorTranscode( $pid, $retval, $encodingLog, $retvalLog );
	}

	/**
	 * @param string $cmd
	 * @param int &$retval
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
		AtEase::suppressWarnings();
		file_put_contents( $encodingLog, $status );
		// Output the retVal to the $retvalLog
		file_put_contents( $retvalLog, $retval );
		AtEase::restoreWarnings();
	}

	/**
	 * @param int $pid
	 * @param int &$retval
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
			"\nisProcessRunning: " . ( self::isProcessRunningKillZombie( $pid ) ? 'true' : 'false' ) .
			"\n" );

		// Check that the child process is still running
		// ( note this does not work well with  pcntl_waitpid for some reason :( )
		while ( self::isProcessRunningKillZombie( $pid ) ) {
			// $this->output( "$pid is running" );

			// Check that the target file is growing ( every 5 seconds )
			if ( $loopCount === 10 ) {
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
					if ( $newFileSize === $oldFileSize ) {
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
		AtEase::suppressWarnings();
		$returnCodeFile = file_get_contents( $retvalLog );
		AtEase::restoreWarnings();

		// File based exit code seems more reliable than pcntl_wexitstatus
		$retval = (int)$returnCodeFile;

		// return the encoding log contents ( will be inserted into error table if an error )
		// ( will be ignored and removed if success )
		if ( $errorMsg !== '' ) {
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

class_alias( WebVideoTranscodeJob::class, 'WebVideoTranscodeJob' );
