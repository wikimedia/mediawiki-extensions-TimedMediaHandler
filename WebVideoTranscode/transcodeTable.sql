-- transcodeTable.sql
-- 2011-04-05  <mdale@wikimedia.org>
--
CREATE TABLE /*$wgDBprefix*/transcode (
	transcode_id INT NOT NULL  PRIMARY KEY,
	transcode_image_name varchar(255) binary NOT NULL,
	transcode_key varchar( 48 ) NOT NULL ,
	transcode_error BLOB NULL,
	transcode_time_addjob VARBINARY( 14 ) NULL,
	transcode_time_startwork VARBINARY( 14 ) NULL,
	transcode_time_success VARBINARY( 14 ) NULL,
	transcode_time_error VARBINARY( 14 ) NULL,
) /*$wgDBTableOptions*/;
CREATE INDEX ( transcode_image_name , transcode_state , transcode_key ),
CREATE INDEX ( transcode_time_addjob ,transcode_time_startwork , transcode_time_success, transcode_time_error ),
