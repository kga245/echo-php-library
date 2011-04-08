<?php
/*
 * EchoLib
 * A PHP library that interfaces with the JS-Kit Echo Platform
 * Author: Jon Cianciullo (jonc@3ones.com)
 * version 0.1.5
*/

/*

Usage:

// create lib object
$EchoLib = new EchoLib($consumer_key, $consumer_secret);
// do api method and get response data
$response = $EchoLib->method_submit($content);
$last_api_call = $EchoLib->last_api_call();
$last_status_code = $EchoLib->last_status_code();

*/

/* Load OAuth lib. You can find it at http://oauth.net */
require_once('OAuth.php');
require_once('simplepie.php');
require_once('activitystreams.php');

class EchoLib {
	public static $API_URL_SUBMIT = "http://api.js-kit.com/v1/submit";
	public static $API_URL_SEARCH = "http://api.js-kit.com/v1/search";
	public static $API_URL_LIST = "http://api.js-kit.com/v1/feeds/list";
	public static $API_URL_REGISTER = "http://api.js-kit.com/v1/feeds/register";
	public static $API_URL_UNREGISTER = "http://api.js-kit.com/v1/feeds/unregister";

	private $last_api_call;
	private $http_status;

	/**
	* Constructor
	**/
	function __construct($consumer_key=null, $consumer_secret=null) {
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
	}

	/**
	* Debug Helpers
	*/
	function last_status_code() { return $this->http_status; }
	function last_api_call() { return $this->last_api_call; }
	
	/**
	* Submit API Method
	* http://wiki.js-kit.com/API-Method-submit
	*/
	function method_submit($content="") {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest(self::$API_URL_SUBMIT, array('content'=>$content), 'POST');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}
	
	/**
	* Search API Method
	* http://wiki.js-kit.com/API-Method-search
	*/
	function method_search($query, $since) {
		$rsp = $this->http(self::$API_URL_SEARCH . '?q=' . $query , '&appkey=' . $this->consumer_key . '&since=' . $since, null);
		return $rsp;
	}

	/**
	* Count API Method
	* http://wiki.aboutecho.com/w/page/27888212/API-method-count
	*/
	function method_count($query) {
		$rsp = $this->http('http://api.echoenabled.com/v1/count'. '?q=' . $query , '&appkey=' . $this->consumer_key, null);
		return $rsp;
	}

	/**
	* Mux Item API Method
	* http://wiki.aboutecho.com/w/page/32433803/API-method-mux
	*/
	function method_mux($requests) {
		$rsp = $this->http('http://api.echoenabled.com/v1/mux'. '?requests=' . $requests , '&appkey=' . $this->consumer_key, null);
		return $rsp;
	}
	
	/**
	* List API Method
	* http://wiki.js-kit.com/API-Method-feeds-list
	*/
	function method_list() {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest(self::$API_URL_LIST, null, 'GET');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}
	
	/**
	* Register API Method
	* http://wiki.js-kit.com/API-Method-feeds-register
	*/
	function method_register($url, $interval=0) {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest(self::$API_URL_REGISTER, array('url'=>$url, 'interval'=>$interval), 'POST');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}
	
	/**
	* Unregister API Method
	* http://wiki.js-kit.com/API-Method-feeds-unregister
	*/
	function method_unregister($url="") {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest(self::$API_URL_UNREGISTER, array('url'=>$url), 'POST');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}

	/**
	* User Get API Method
	* http://wiki.aboutecho.com/w/page/35104884/API-method-users-get
	*/
	function method_user_get($identityURL) {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest('http://api.echoenabled.com/v1/users/get', array('identityURL'=>$identityURL), 'GET');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}

	/**
	* User Update API Method
	* http://wiki.aboutecho.com/w/page/35060726/API-method-users-update
	*/
	function method_user_update($identityURL=null, $subject, $content) {
		$to = new EchoOAuth($this->consumer_key, $this->consumer_secret);
		$rsp = $to->OAuthRequest('http://api.echoenabled.com/v1/users/update=', array('identityURL'=>$identityURL,'subject'=>$subject,'content'=>$content), 'POST');
		$this->last_api_call = $to->lastAPICall();
		$this->http_status = $to->lastStatusCode();
		return $rsp;
	}

	/**
	* User whoami API Method
	* http://wiki.aboutecho.com/w/page/35485894/API-method-users-whoami
	*/
	function method_user_whoami($sessionID) {
		$rsp = $this->http('http://api.echoenabled.com/v1/users/whoami'
			. '?sessionID=' . $sessionID , '&appkey=' . $this->consumer_key, null);
		return $rsp;
	}
	
	/**
	* Creates string of post variables
	*/
	public static function to_postdata($data) {
		$total = array();
		foreach ($data as $k => $v) {
		  if (is_array($v)) {
			foreach ($v as $va) {
			  $total[] = OAuthUtil::urlencode_rfc3986($k) . "[]=" . OAuthUtil::urlencode_rfc3986($va);
			}
		  } else {
			$total[] = OAuthUtil::urlencode_rfc3986($k) . "=" . OAuthUtil::urlencode_rfc3986($v);
		  }
		}
		$out = implode("&", $total);
		return $out;
	}
	
	/**
	* cURL method to send data
	*/
	function http($url, $post_data = null) {
		$ch = curl_init();
		if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		if (isset($post_data)) {
		  curl_setopt($ch, CURLOPT_POST, 1);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$response = curl_exec($ch);
		//var_dump($url);
		$this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->last_api_call = $url . $post_data;
		curl_close ($ch);
		return $response;
	}
}


/*
  <entry>
    <id>http://protobuilt.com/activity/comment/add/protobuilt.com/path/jsid-1252922954-934</id>
    <published>2010-04-01T14:10:00Z</published>
    <author>
      <name>Test User</name>
      <uri>http://protobuilt.com/</uri>
      <email>test@protobuilt.com</email>
    </author>
    <title>Test User wrote this comment</title>
    <link rel="alternate" type="text/html" href="http://protobuilt.com/page.html#jsid-1252922954-934"/>
    <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
    <activity:actor>
      <activity:object-type>http://activitystrea.ms/schema/1.0/person</activity:object-type>
      <id>http://js-kit.com/users/202cb962ac59075b964b07152d234b70</id>
      <title>Somebody</title>
    </activity:actor>
    <activity:object>
      <activity:object-type>http://activitystrea.ms/schema/1.0/comment</activity:object-type>
      <summary>This is a test comment</summary>
      <content><![CDATA[ 
	  
	  comment goes here
	  
http://www.twitvid.com/A0EET
	  
	   ]]></content>
      <id>http://protobuilt.com/comment/992</id>
    </activity:object>
    <activity:target>
      <id>http://protobuilt.com/article/1252922954</id>
      <title>Title of the test article</title>
      <link rel="alternate" type="text/html" href="http://protobuilt.com/"/>
    </activity:target> 
  </entry>
  */

/**
 * Echo OAuth class
 */
class EchoOAuth {/*{{{*/
  /* Contains the last HTTP status code returned */
  private $http_status;

  /* Contains the last API call */
  private $last_api_call;

  /* Set up the API root URL */
  public static $TO_API_ROOT = "http://api.js-kit.com/v1";

  /**
   * Set API URLS
   */
  function requestTokenURL() { return self::$TO_API_ROOT.'/oauth/request_token'; }
  function authorizeURL() { return self::$TO_API_ROOT.'/oauth/authorize'; }
  function accessTokenURL() { return self::$TO_API_ROOT.'/oauth/access_token'; }

  /**
   * Debug helpers
   */
  function lastStatusCode() { return $this->http_status; }
  function lastAPICall() { return $this->last_api_call; }

  /**
   * construct OAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {/*{{{*/
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }/*}}}*/

  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $args = array(), $method = NULL) {/*{{{*/
    if (empty($method)) $method = empty($args) ? "GET" : "POST";
    $req = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $args);
    $req->sign_request($this->sha1_method, $this->consumer, $this->token);
    switch ($method) {
    case 'GET': return $this->http($req->to_url());
    case 'POST': return $this->http($req->get_normalized_http_url(), $req->to_postdata());
    }
  }/*}}}*/

  /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $post_data = null) {/*{{{*/
    $ch = curl_init();
    if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //////////////////////////////////////////////////
    ///// Set to 1 to verify SSL Cert           //////
    //////////////////////////////////////////////////
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    if (isset($post_data)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $response = curl_exec($ch);
    $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->last_api_call = $url . $post_data;
    curl_close ($ch);
    return $response;
  }/*}}}*/
}/*}}}*/