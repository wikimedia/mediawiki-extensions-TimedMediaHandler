-- transcodeTable.sql
-- 2011-04-05  <mdale@wikimedia.org>
--
CREATE TABLE /*$wgDBprefix*/transcode (
	transcode_id BIGINT unsigned NOT NULL AUTO_INCREMENT,
	transcode_image_name varchar(255) binary NOT NULL,
	transcode_key varchar( 48 ) NOT NULL ,
	transcode_error BLOB NULL,
	transcode_time_addjob VARBINARY( 14 ) NULL,
	transcode_time_startwork VARBINARY( 14 ) NULL,
	transcode_time_success VARBINARY( 14 ) NULL,
	transcode_time_error VARBINARY( 14 ) NULL,
	PRIMARY KEY ( transcode_id ),
	INDEX ( transcode_image_name , transcode_state , transcode_key ),
	INDEX ( transcode_time_addjob ,transcode_time_startwork , transcode_time_success, transcode_time_error ),
) /*$wgDBTableOptions*/;
