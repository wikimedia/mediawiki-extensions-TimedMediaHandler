<?php 

	// Register all the timedText modules 
	return array(			
		"mw.TimedText" => array(
			'scripts' => "mw.TimedText.js",
			'styles' => "css/mw.style.TimedText.css",
			'dependencies' => array(
				'mw.EmbedPlayer'
			)
		),
		"mw.TimedTextEdit" => array(
			'scripts' => "mw.TimedTextEdit.js",
			'styles' => "css/mw.style.TimedTextEdit.css",
			'dependencies' => array(
				'mw.TimedText',
				'jquery.ui.dialog',
				'jquery.ui.tabs'
			)
		),
		"RemoteMwTimedText" =>array(
			'scripts' => "remotes/RemoteMwTimedText.js"
		)
	);	