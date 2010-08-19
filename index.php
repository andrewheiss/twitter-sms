<?php
	// Configuration
	//-----------------------
	// $config_dir = Directory, preferrably not accessible from the server root, with the configuration file and database files (copied from sms_config.default.php and sms.default.db)
	$config_dir = dirname(__FILE__).'/../../protected';
	$db = new PDO('sqlite:'.$config_dir.'/sms.db');
	require($config_dir."/sms_config.php");
	
	// Load jdp's twitterlibphp (http://github.com/jdp/twitterlibphp)
	// This should be included as a submodule in this project's git repo
	require("lib/twitterlibphp/twitter.lib.php");
	$twitter = new Twitter($username, $password);

	// Check for most recently stored mention
	$query = $db->query("SELECT * FROM id_tracker ORDER BY last_id DESC LIMIT 1");
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	// If this is the first time, get the most recent mention and use that as the base.
	// Obviously no e-mails will get sent during the first run.
	if ($result) {
		$last_id = $result['last_id'];
	} else {
		$get_last = $twitter->getMentions(array('count'=>1));
		$last = new SimpleXMLElement($get_last);
		$last_id = (int)$last->status->id;
		echo "Database has been populated";
	}
	
	// Check Twitter for any mentions since the last stored one
	$check = $twitter->getMentions(array('since_id'=>$last_id));
	$mentions = new SimpleXMLElement($check);

	if ($mentions->status) {
		// Reverse the array.
		foreach ($mentions->status as $status){
			$a[] = $status;
		}
		$a = array_reverse($a);

		// Output the most recent mentions in reverse order and e-mail them to the fancy SMS e-mail address
		foreach ($a as $status) {
			$message = $status->user->name . " (" . $status->user->screen_name . "): " . $status->text;
			$subject = "Tweet from " . $status->user->name;
			$headers = "From: no-reply@andrewheiss.com";
			
			// FUTURE: wordwrap() needed for better line breaking on the phone screen?
			//echo $message . "<br />";
			mail($email, $subject, $message, $headers);
			
			$last_id = $status->id;
		}
		
		// Save the id of the most recent mention to the database
		$query = $db->prepare("INSERT INTO id_tracker (last_id) VALUES (:last_id)");
		$query->bindParam(':last_id', $last_id);
		$query->execute();
	} else {
		// Nothing new…
		echo "No new mentions";
	}
?>