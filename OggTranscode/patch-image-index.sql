-- 
-- Add an index on img_media_type 
--
CREATE INDEX /*i*/image_media_type ON /*_*/image ( img_media_type );

--
-- Index for transcode_job_name 
--
CREATE INDEX /*i*/transcode_job_name ON /*_*/transcode_job ( tjob_name );

--
-- Index for tjob_state 
--
CREATE INDEX /*i*/transcode_job_state ON /*_*/transcode_job ( tjob_state );