-- Add unique constrain on name/key
DROP INDEX /*i*/transcode_name_inx ON /*_*/transcode;
CREATE UNIQUE INDEX /*i*/transcode_name_key ON /*_*/transcode (transcode_image_name,transcode_key);
