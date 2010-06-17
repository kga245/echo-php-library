<?php

// time limit (10 mins)
ini_set("max_execution_time", "600");
ini_set("max_input_time", "600");
set_time_limit(600);

//
header('Content-Type: application/xml; charset=utf-8');

// include lib
include('../lib/EchoLib.php');

$feed = ($_GET['feed'] ? $_GET['feed'] : null);

if($feed) {
	try {
		// init lib
		$EchoLib = new EchoLib();
		// get rss feed
		$data = $EchoLib->http($feed);
		if(!$data) throw new Exception("Feed returned no data.");	
		// convet rss to activitystrea.ms xml
		$streamy = new ActivityStreams();
		$rsp = $streamy->rss_to_activity_streams($data);
		// print
		echo $rsp;
	} catch(Exception $e) {
		$msg = $e->getMessage();
		echo "<error>$msg</error>";
	}
}
?>