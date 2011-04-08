<?php
// include lib
include('../lib/EchoLib.php');

// add your Echo consumer key/secret
$default_key = '';
$default_secret = '';

// collect form params
$consumer_key = ($_POST['consumer_key'] ? $_POST['consumer_key'] : null);
$consumer_secret = ($_POST['consumer_secret'] ? $_POST['consumer_secret'] : null);
$method = ($_POST['method'] ? $_POST['method'] : null);
$content = ($_POST['content'] ? stripslashes($_POST['content']) : null);
$query = ($_POST['query'] ? $_POST['query'] : null);
$url = ($_POST['field-url'] ? $_POST['field-url'] : null);
$interval = ($_POST['interval'] ? $_POST['interval'] : null);

function getRequest($key) {
	return ($_REQUEST[$key] ? $_REQUEST[$key] : null);
}

// declare vars
$rsp = '';

// do api method
if($method) {
	// create new Echo Lib object
	$EchoLib = new EchoLib($consumer_key, $consumer_secret);
	// complete method
	switch($method) {
		case 'submit':
			$rsp = $EchoLib->method_submit($content);
		break;
		case 'search':
			$rsp = $EchoLib->method_search(getRequest('query'), getRequest('since'));
		break;
		case 'count':
			$rsp = $EchoLib->method_count(getRequest('query2'));
		break;
		case 'mux':
			$rsp = $EchoLib->method_mux(getRequest('requests'));
		break;
		case 'list':
			$rsp = $EchoLib->method_list();
		break;
		case 'register':
			$rsp = $EchoLib->method_register($url, $interval);
		break;
		case 'unregister':
			$rsp = $EchoLib->method_unregister(getRequest('url2'));
		break;
		case 'userget':
			$rsp = $EchoLib->method_user_get(getRequest('identityURL'));
		break;
		case 'userupdate':
			$rsp = $EchoLib->method_user_update(getRequest('identityURL2'), getRequest('subject'), getRequest('content2'));
		break;
		case 'userwhoami':
			$rsp = $EchoLib->method_user_whoami(getRequest('sessionID'));
		break;
	}
	// get api call info
	$last_api_call = $EchoLib->last_api_call();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Echo Plaform Console</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script>
$(document).ready(function() {
	//
	$("#method-container").find("a").bind("click", function() {
		//
		var rel = $(this).attr("rel");
		// hide all
		$(".forms-hidden").hide();
		//
		$("#method-"+rel).slideDown("slow");
		//
		$("#form-field-method").attr("value", rel);
		//
		$("#buttons").show();
		//
		$('#output').remove();
	});
});
</script>
<link href="screen.css" rel="stylesheet" type="text/css" />
<link href="application.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="container title">
  <h1>RealTidBits Echo Platform Console</h1></div>

<div class="page">

<div class="container">
<div id="method-container">
	<div class="wrapper">
        <h3>Methods</h3>
        <ul>
            <li><a href="javascript:void(0);" rel="submit">Item - Submit</a></li>
            <li><a href="javascript:void(0);" rel="search">Item - Search</a></li>
            <li><a href="javascript:void(0);" rel="count">Item - Count</a></li>
            <li><a href="javascript:void(0);" rel="mux">Item - MUX</a></li>
            <li><a href="javascript:void(0);" rel="list">Feeds - List</a></li>
            <li><a href="javascript:void(0);" rel="register">Feeds - Register</a></li>
            <li><a href="javascript:void(0);" rel="unregister">Feeds - Unregister</a></li>
            <li><a href="javascript:void(0);" rel="userget">User - Get</a></li>
            <li><a href="javascript:void(0);" rel="userupdate">User - Update</a></li>
            <li><a href="javascript:void(0);" rel="userwhoami">User - WhoAmI</a></li>
        </ul>
    </div>
</div>

<div id="method-output">
<div class="wrapper">

<form method="post">
	<input type="hidden" id="form-field-method" name="method" value="<?php echo $method; ?>" />

<div style="border-bottom:1px solid #ccc;">
	<p>Key: <input type="text" name="consumer_key" value="<?php echo (!$consumer_key ? $default_key : $consumer_key); ?>" style="width:300px;" /></p>
    <p>Secret: <input type="text" name="consumer_secret" value="<?php echo (!$consumer_secret ? $default_secret : $consumer_secret); ?>" style="width:300px;" />
</div>

<div class="content">
    <div id="method-submit" class="forms-hidden" style="<?php if($method != 'submit') { echo 'display:none;'; } ?>">
    <h3>API Method - submit</h3>
		<p>Submit items in the Activity Streams XML format. <a href="http://wiki.js-kit.com/API-Method-submit">Documentation.</a></p>
        Content: <br/>
        <textarea name="content" rows="10" style="width:95%;"><?php echo $content; ?></textarea>
        <br /> <small>URL-encoded Activity Streams XML with one or more activity entries</small>
    </div>
<!-- -->
    <div id="method-search" class="forms-hidden" style="<?php if($method != 'search') { echo 'display:none;'; } ?>">
    <h3>API Method - search</h3>
		<p>Returns items that match a specified query in Activity Stream format. <a href="http://wiki.js-kit.com/API-Method-search">Documentation.</a></p>
    
        Query: <br/>
        <input type="text" name="query" value="<?php echo getRequest('query'); ?>" style="width:500px;" />
        <br/> <small> Specify a search query, ex: "scope:http://js-kit.com/"</small>
        <br/>
        Since: <br/>
        <input type="text" name="since" value="<?php echo getRequest('since'); ?>" style="width:100px;" />
        <br />
    </div>
<!-- -->
<!-- -->
    <div id="method-count" class="forms-hidden" style="<?php if($method != 'count') { echo 'display:none;'; } ?>">
    <h3>API Method - Count</h3>
		<p>Returns a number of items that match the specified query in JSON format. <a href="http://wiki.aboutecho.com/w/page/27888212/API-method-count">Documentation.</a></p>
    
        Query: <br/>
        <input type="text" name="query2" value="<?php echo getRequest('query2'); ?>" style="width:500px;" />
        <br/><small> Specify a search query, ex: "scope:http://js-kit.com/"</small><br/>
    </div>
<!-- -->
<!-- -->
    <div id="method-mux" class="forms-hidden" style="<?php if($method != 'mux') { echo 'display:none;'; } ?>">
    <h3>API Method - MUX</h3>
		<p>The API method "mux" allows to "multiplex" requests, i.e. use a single API call to "wrap" several requests. The multiplexed requests are executed concurrently and independently by the Echo server. <a href="http://wiki.aboutecho.com/w/page/32433803/API-method-mux">Documentation.</a></p>
    
        Requests: <br/>
        <input type="text" name="requests" value="<?php echo getRequest('requests'); ?>" style="width:500px;" />
        <br/> <small>URL-encoded JSON structure detailing the API calls to be multiplexed.</small><br/>
    </div>
<!-- -->
    <div id="method-list" class="forms-hidden" style="<?php if($method != 'list') { echo 'display:none;'; } ?>">
    <h3>API Method - feeds/list</h3>
<p>Echo Platform allows you to register Activity Stream feeds with the system. We will then agressively poll those feeds looking for new data. This method returns a list of registered feeds for specified API key. <a href="http://wiki.js-kit.com/API-Method-feeds-list">Documentation.</a></p>
		No properties to configure.
    </div>
    <div id="method-register" class="forms-hidden" style="<?php if($method != 'register') { echo 'display:none;'; } ?>">
        <h3>API Method - feeds/register</h3>
        <p>Echo Platform allows you to register Activity Stream feeds with the system. We will then agressively poll those feeds looking for new data. This method registers a new Activity Stream feed by URL. <a href="http://wiki.js-kit.com/API-Method-feeds-register">Documentation.</a></p>    
        <p>
        	Url: <br/>
        	<input type="text" name="field-url" value="<?php echo $url; ?>" style="width:500px;" />
        	<br /> <small>URL of page with feed in Activity Streams XML format</small>
        </p>
        <p>Interval: <br/>
        <input type="text" name="interval" value="<?php echo $interval; ?>" style="width:50px;" /> seconds
        <br /> <small>Feed refresh rate in seconds (time between poll actions)</small>
        </p>
    </div>
    <div id="method-unregister" class="forms-hidden" style="<?php if($method != 'unregister') { echo 'display:none;'; } ?>">
    	<h3>API Method - feeds/unregister</h3>
		<p>Echo Platform allows you to register Activity Stream feeds with the system. We will then agressively poll those feeds looking for new data. This method unregisters an Activity Stream feed by URL. <a href="http://wiki.js-kit.com/API-Method-feeds-unregister">Documentation.</a></p>
        <p>
        	Url: <br/>
        	<input type="text" name="url2" value="<?php echo $url2; ?>" style="width:500px;" />
        	<br /> <small>URL of page with feed in Activity Streams XML format</small>
        </p>
    </div>
<!-- -->
    <div id="method-userget" class="forms-hidden" style="<?php if($method != 'userget') { echo 'display:none;'; } ?>">
    <h3>API Method - User Get</h3>
		<p>Endpoint for fetching user information. <a href="http://wiki.aboutecho.com/w/page/35104884/API-method-users-get">Documentation.</a></p>
    
        identityURL: <br/>
        <input type="text" name="identityURL" value="<?php echo getRequest('identityURL'); ?>" style="width:500px;" />
        <br/><small>User identity URL</small><br/>
    </div>
<!-- -->
<!-- -->
    <div id="method-userupdate" class="forms-hidden" style="<?php if($method != 'userupdate') { echo 'display:none;'; } ?>">
    <h3>API Method - User Update</h3>
		<p>Endpoint for updating user information. <a href="http://wiki.aboutecho.com/w/page/35060726/API-method-users-update">Documentation.</a></p>
    
        identityURL: <br/>
        <input type="text" name="identityURL2" value="<?php echo getRequest('identityURL2'); ?>" style="width:500px;" />
        <br/><small>User identity URL</small><br/>
        subject: <br/>
        <input type="text" name="subject" value="<?php echo getRequest('subject'); ?>" style="width:500px;" />
        <br/><small>Shows which user parameter should be updated.</small><br/>
        content: <br/>
        <textarea name="content2" rows="10" style="width:95%;"><?php echo getRequest('content2'); ?></textarea>
        <br/><small>Contains data to be used for the user update.</small><br/>
    </div>
<!-- -->
<!-- -->
    <div id="method-userwhoami" class="forms-hidden" style="<?php if($method != 'userwhoami') { echo 'display:none;'; } ?>">
    <h3>API Method - User WhoAmI</h3>
		<p>Endpoint for retrieving currently logged in user information by session ID, which equals to Backplane channel ID for this user. <a href="http://wiki.aboutecho.com/w/page/35485894/API-method-users-whoami">Documentation.</a></p>
    
        sessionID: <br/>
        <input type="text" name="sessionID" value="<?php echo getRequest('sessionID'); ?>" style="width:500px;" />
        <br/><small>Backplane channel ID, which is the user session ID at the same time.</small><br/>
    </div>
<!-- -->
    
    
    
</div>

   <br />
   <div id="buttons" style="<?php if(!$method) { echo 'display:none;'; } ?>">
    <input type="submit" id="form-button-submit" value="Send to Echo"" /> 
    <input type="button" name="" value="Clear Output" onclick="$('#output').remove();" />
    </div>
</form>
	</div>
</div>

<div id="right_column">

<div class="promo">This is ad space or promo space for 3ones.</div>
<div class="promo">This is ad space or promo space for 3ones.</div>

</div>

<div id="output">
<?php if($rsp) {
	echo '<div class="response"><p><b>API Call:</b> ' . $last_api_call . '</p><p><b>Response:</b> <pre>' . $rsp . '</pre></p></div>';
} ?>
</div>


</div></div>
</body>
</html>
