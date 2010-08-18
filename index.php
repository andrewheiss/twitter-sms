<?php
	phpinfo();
	exit;
	$config_dir = $_SERVER['DOCUMENT_ROOT'].'/../protected';
	require("twitterlibphp/twitter.lib.php");
	require($config_dir."/sms_config.php");

	// Database configuration
	// $db = new PDO('sqlite:'.dirname(__FILE__).'/db/db.db');
	$db = new PDO('sqlite:'.$config_dir.'/sms.db');
	$query = $db->query("SELECT * FROM id_tracker ORDER BY last_id DESC LIMIT 1");
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	if ($result) {
		$last_id = $result['last_id'];
	} else {
		$last_id = 19412551379;
	}

	$twitter = new Twitter($username, $password);
	$check = $twitter->getMentions(array('since_id'=>$last_id));
	$mentions = new SimpleXMLElement($check);

	if ($mentions->status) {
		// Reverse the array. This is messy. Ugh.
		foreach ($mentions->status as $status){
			$a[] = $status;
		}
		$a = array_reverse($a);

		// Output the most recent mentions in reverse order and e-mail them to myself
		foreach ($a as $status) {
			$message = $status->user->name . " (" . $status->user->screen_name . "): " . $status->text;
			$subject = "Tweet from " . $status->user->name;
			$headers = "From: no-reply@andrewheiss.com";
			
			// wordwrap() needed?
			
			mail($email, $subject, $message, $headers);
			
			$last_id = $status->id;
		}
		
		// Save the last id to the database
		$query = $db->prepare("INSERT INTO id_tracker (last_id) VALUES (:last_id)");
		$query->bindParam(':last_id', $last_id);
		$query->execute();
	} else {
		// Nothing new…
		// echo "No new mentions";
	}

?>