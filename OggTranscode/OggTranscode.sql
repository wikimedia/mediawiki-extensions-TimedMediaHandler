--
-- Table structure for table `transcode_job`
--

CREATE TABLE IF NOT EXISTS /*_*/transcode_job (

	--File name key ( called image to match table name ) 
	tjob_name varbinary(255) NOT NULL,

	--Derivative key of the transcode
	tjob_derivative_key varbinary(40) NOT NULL,

	-- Timestamp
	`tjob_start_timestamp` char(14) NOT NULL,

	-- Transcoding state 
	tjob_state enum('OK','ASSIGNED','FAILED') default NULL
	
) /*$wgDBTableOptions*/;

--
-- Index for transcode_job_name 
--
CREATE INDEX /*i*/transcode_job_name ON /*_*/transcode_job ( tjob_name );

--
-- Index for tjob_state 
--
CREATE INDEX /*i*/transcode_job_state ON /*_*/transcode_job ( tjob_state );
