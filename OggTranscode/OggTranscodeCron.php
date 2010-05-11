<?php

/*
* OggTranscodeCron.php
*
* Maintenance script overview:
*
* Designed to be run every 10 seconds or so. Will spawn threads
* until max thread count reached
*
* 1) Check if number of instances is < $wgMaxTranscodeThreads
*        the script exits immediately.
*
* 2) Query the db for jobSets ( find a job that is not processed or > wgTranscodeJobTimeOut )
*
* 3) Does a incomplete transcode to temporary location
* 	 	once done moves file into place.
*
*/
require_once(  dirname(__FILE__) . '/../../../maintenance/Maintenance.php' );

class OggTranscodeCron extends Maintenance {

	// The max number of threads ( can also be set when called via the command line )
	private $maxThreads = 2;

	/**
	 * @constructor
	 *
	 */
	public function __construct() {
		global $wgUseNormalUser;
		parent::__construct();
		$this->mDescription = "Transcode video files";
		$this->addOption( 'maxThreads', 'Maximum number of threads to run', false, true );
		$wgUseNormalUser = true;
	}

	/**
	 * Main Execute method from Maintenance class
	 */
	public function execute() {
		global $wgEnabledDerivatives;
		// Set command line options:
		if ( $this->hasOption( 'maxThreads' ) ) {
			$this->maxThreads = intval( $this->hasOption( 'maxThreads' ) );
		}

		// Check if number of instances is < $wgMaxTranscodeThreads
		if( $this->checkMaxThreads() ){
			exit( 0 );
		}

		// Check if we have $wgEnabledDerivatives to transcode to.
		if( !isset( $wgEnabledDerivatives ) || count( $wgEnabledDerivatives ) == 0 ){
			print "\$wgEnabledDerivatives is not set or empty \n";
			exit( 0 );
		}

		// Check if there are any jobs to be done
		$job = $this->getTranscodeJob();
		if( $job ){
			if( ! $this->processTranscodeJob( $job ) ){
				// Mark the job as failed if it did not complete
				$this->markJobState( $job, 'FAILED' );
				exit( 1 );
			}
			// Else job was oky mark state and exit:
			$this->markJobState( $job,  'OK' );
			exit( 0 );
		}
		// No jobs found.

		// Update Transcode Jobs table with images that are missing transcode jobs
		// ( the updated transcode job set will be accessed the next time the script is run)
		$this->updateTranscodeJobs();
		exit( 0 );
	}

	/**
	 * Get a Transcode Job from the transcode_job table
	 */
	function getTranscodeJob() {
		$dbr = & wfGetDB( DB_SLAVE );

		// Don't hit the slaves under high load:
		wfWaitForSlaves( 5 );

		// Grab transcode job
		$res = $dbr->select(
			'transcode_job',
			'*',
			'tjob_state IS NULL',
			__METHOD__,
			array(
				'USE INDEX' => array(
					'transcode_job' => 'transcode_job_state'
				),
				'LIMIT' => 20
			)
		);
		return $dbr->fetchObject( $res );
	}

	/**
	 * Grab a batch of up-to 100 jobs and insert them into the jobs table.
	 *
	 * NOTE: this presently does not test for missing derivative keys /
	 * keep derivative keys "in-sync" with transcode_job table.
	 */
	function updateTranscodeJobs( ){
		global $wgEnabledDerivatives;
		// Find new jobs and insert them into the db
		$dbr = & wfGetDB( DB_SLAVE );

		// Don't hit the slaves under high load:
		wfWaitForSlaves( 5 );

		// Get image files that don't have a transcode job
		$res = $dbr->select(
			array( 'image', 'transcode_job'),
			array( 'img_name' ),
			array( 'tjob_name IS NULL',
					'img_media_type' => 'VIDEO'),
			__METHOD__,
			array( 'LIMIT'  => 100 ),
			array(
				'transcode_job' => array(
					'LEFT JOIN',
					array( 'img_name = tjob_name' )
				)
			)
		);
		$imageNames = array();
		while( $row = $dbr->fetchObject( $res ) ) {
			$imageNames[] = $row->img_name;
		}
		if( count( $imageNames ) == 0){
			return false;
		}

		// Add the derivative set[s] to the transcode table
		$dbw = & wfGetDB( DB_MASTER );
		$dbw->begin();
		foreach( $imageNames as $name){
			foreach( $wgEnabledDerivatives as $derivativeKey ){

				// Do not to add a derivative job that is > than the source asset
				$fileTitle = Title::newFromText( $name, NS_FILE);

				$file = wfFindFile( $fileTitle );
				if( !$file ){
					$this->output( "File not found: {$name} (not adding to jobQueue\n" );
					continue;
				}
				$targetSize =  OggTranscode::$derivativeSettings[ $derivativeKey ]['maxSize'];
				$sourceSize = $file->getWidth();
				if( $targetSize > $sourceSize ){
					$this->output( "File:{$name} is too small for {$derivativeKey} ::\n" .
					 "target: {$targetSize} > source: {$sourceSize} \n\n" );
					continue;
				}
				// Else $derivative size is < source size, do insert:
				$dbw->insert(
					'transcode_job',
					array(
						'tjob_name' => $name,
						'tjob_derivative_key' => $derivativeKey,
						'tjob_start_timestamp' => time()
					),
					__METHOD__
				);
			}
		}
		$dbw->commit();
	}

	/**
	 * Process a transcode job
	 * @param {Object} $job Transcode job db row
	 * @return {Bollean}
	 * 	true if job was processed
	 * 	false if there was an error in the job
	 */
	function processTranscodeJob( $job ){
		$dbw = & wfGetDB( DB_MASTER );

		// Update job state to "ASSIGNED"
		$this->markJobState( $job, 'ASSIGNED');

		// Get the source title from tjob_name
		$fileTitle = Title::newFromText( $job->tjob_name, NS_FILE);
		if( ! $fileTitle->exists() ){
			$this->output( "Title not found: {$job->tjob_name}\n" );
			return false;
		}

		// Get the file source:
		$file = wfFindFile( $fileTitle );
		if( !$file ){
			$this->output( "File not found: {$job->tjob_name}\n" );
			return false;
		}

		// Setup a temporary target location for encode output
		$tempTarget = wfTempDir() .'/'. $fileTitle->getDbKey() . $job->tjob_derivative_key . '.ogv';

		// Get encoding settings from encode key
		if( !isset( OggTranscode::$derivativeSettings[ $job->tjob_derivative_key ] )){
			$this->output( "Derivative key not found: {$job->tjob_name}\n" );
			return false;
		}
		$encodeSettings = OggTranscode::$derivativeSettings[ $job->tjob_derivative_key ];

		// Shell out the transcode command.
		$this->output( "Starting transcode:\n" );
		$result = $this->doEncode( $file->getPath(), $tempTarget, $encodeSettings  );

		// Check the result
		if( !$result ){
			$this->output( "Transcode failed: {$job->tjob_name}\n" );
			return false;
		}

		// If result is "ok"
		$this->output( "Transcode OK, moving thumb to public location\n" );
		$derivativeTarget = $file->getThumbPath( $job->tjob_derivative_key ) . '.ogv';

		if( !rename( $tempTarget, $derivativeTarget) ){
			$this->output( "Renamed failed: {$tempTarget} to {$derivativeTarget}\n" );
			return false;
		}
		// Return true to update the job state:
		return true;
	}
	/**
	 * Mark a job as state
	 * @param {Object} $job Job db row object.
	 * @param {String} $state String to update state to can be:
	 * 	'OK','ASSIGNED','FAILED'
	 */
	function markJobState( $job , $state){
		$dbw = & wfGetDB( DB_MASTER );
		$dbw->update(
			'transcode_job',
			array(
				'tjob_state' => $state
			),
			array(
				'tjob_name' => $job->tjob_name,
				'tjob_derivative_key' => $job->tjob_derivative_key
			)
		);
	}

	/**
	 * Check the number if the instances of this script that are running
	 * are over the max allowed threads $this->maxThreads
	 *
	 * @return {Boolean}
	 *	true if max Threads count has been reached
	 * 	false if less than max Threads
	 */
	public function checkMaxThreads(){

		// Get the list of processes:
		$pslist = wfShellExec( "ps aux", $out);

		// Return the count of php OggTranscodeCron.php found
		$threadCount = preg_match_all( '/php\sOggTranscodeCron\.php/', $pslist, $matches);
		if( $threadCount === false ){
			print "Error in preg_match";
			exit( 1 );
		}
		return ( $threadCount > $this->maxThreads );
	}

	/**
	 * Issues an encode command to ffmpeg2theora
	 *
	 * @param {String} $source Source file asset
	 * @param {String} $target Target output encode
	 * @param {Array} $encodeSettings Settings to encode the file with
	 */
	function doEncode( $source, $target, $encodeSettings ){
		global $wgffmpeg2theoraPath;

		// Set up the base command
		$cmd = wfEscapeShellArg( $wgffmpeg2theoraPath ) . ' ' . wfEscapeShellArg( $source );

		// Add in the encode settings
		foreach( $encodeSettings as $key => $val){
			if( isset( OggTranscode::$foggMap[$key] ) ){
				if( is_array(  OggTranscode::$foggMap[$key] ) ){
					$cmd.= ' '. implode(' ', OggTranscode::$foggMap[$key] );
				}else if($val == 'true' || $val === true){
			 		$cmd.= ' '. OggTranscode::$foggMap[$key];
				}else if( $val === false){
					//ignore "false" flags
				}else{
					//normal get/set value
					$cmd.= ' '. OggTranscode::$foggMap[$key] . ' ' . wfEscapeShellArg( $val );
				}
			}
		}
		// Add the output target:
		$cmd.= ' -o ' . wfEscapeShellArg ( $target );
		$this->output( "Running cmd: \n\n" .$cmd . "\n\n" );
		wfProfileIn( 'ffmpeg2theora_encode' );
		wfShellExec( $cmd, $retval );
		wfProfileOut( 'ffmpeg2theora_encode' );

		if( $retval ){
			return false;
		}
		return true;
	}
}

$maintClass = "OggTranscodeCron";
require_once( DO_MAINTENANCE );
?>