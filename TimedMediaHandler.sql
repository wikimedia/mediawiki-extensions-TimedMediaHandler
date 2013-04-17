-- TimedMediaHandler.sql
-- 2011-04-05  <mdale@wikimedia.org>
--
CREATE TABLE /*_*/transcode (
	transcode_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	transcode_image_name VARCHAR(255) NOT NULL,
	transcode_key VARCHAR(48) NOT NULL,
	transcode_error longtext NOT NULL,
	transcode_time_addjob VARCHAR(14) NULL,
	transcode_time_startwork VARCHAR(14) NULL,
	transcode_time_success VARCHAR(14) NULL,
	transcode_time_error VARCHAR(14) NULL,
	transcode_final_bitrate INT NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/transcode_time_inx ON /*_*/transcode( transcode_time_addjob ,transcode_time_startwork , transcode_time_success, transcode_time_error );
CREATE INDEX /*i*/transcode_key_idx ON /*_*/transcode( transcode_key );

CREATE UNIQUE INDEX /*i*/transcode_name_key ON /*_*/transcode (transcode_image_name,transcode_key);
