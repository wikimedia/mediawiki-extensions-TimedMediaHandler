-- This file is automatically generated using maintenance/generateSchemaSql.php.
-- Source: ./tables.json
-- Do not modify this file directly.
-- See https://www.mediawiki.org/wiki/Manual:Schema_changes
CREATE TABLE /*_*/transcode (
  transcode_id INT AUTO_INCREMENT NOT NULL,
  transcode_image_name VARCHAR(255) NOT NULL,
  transcode_key VARCHAR(48) NOT NULL,
  transcode_error LONGTEXT NOT NULL,
  transcode_time_addjob BINARY(14) DEFAULT NULL,
  transcode_time_startwork BINARY(14) DEFAULT NULL,
  transcode_time_success BINARY(14) DEFAULT NULL,
  transcode_time_error BINARY(14) DEFAULT NULL,
  transcode_final_bitrate INT NOT NULL,
  INDEX transcode_time_inx (
    transcode_time_addjob, transcode_time_startwork,
    transcode_time_success, transcode_time_error
  ),
  INDEX transcode_key_idx (transcode_key),
  UNIQUE INDEX transcode_name_key (
    transcode_image_name, transcode_key
  ),
  PRIMARY KEY(transcode_id)
) /*$wgDBTableOptions*/;
