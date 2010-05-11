--
-- Table structure for table `play_tracking`
--

CREATE TABLE IF NOT EXISTS /*_*/play_tracking (

	-- Unique id of play tracking event  
	track_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

	-- Filename of resource played
	track_filename varchar(255) binary NOT NULL,

	-- Anonymous hash of client
	track_client_id varbinary(32) NOT NULL,

	-- Browser and playback system dump.
	track_clientplayer tinyblob NOT NULL,

	-- Rate we are tracking ( a value of 10 means 1 in 10 tracked )   
	track_rate integer
	
) /*$wgDBTableOptions*/;




