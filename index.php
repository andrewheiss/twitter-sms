<?php
	require("twitterlibphp/twitter.lib.php");
	require("config.php");

	// Database configuration
	$db = new PDO('sqlite:'.dirname(__FILE__).'/db/db.db');
	$query = $db->query("SELECT * FROM id_tracker ORDER BY last_id DESC LIMIT 1");
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	if ($result) {
		$last_id = $result['last_id'];
	} else {
		$last_id = 1;
	}

	$twitter = new Twitter($username, $password);
	// $check = $twitter->getMentions(array('since_id'=>$last_id));
	$check = $twitter->getMentions(array('since_id'=>'19412551379'));
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
			echo $message . "will be sent to $email";
			
			
			
			$last_id = $status->id;
		}
		
		// Save the last id to the database
		$query = $db->prepare("INSERT INTO id_tracker (last_id) VALUES (:last_id)");
		$query->bindParam(':last_id', $last_id);
		$query->execute();
	} else {
		// Nothing new…
		echo "No new mentions";
	}

?>