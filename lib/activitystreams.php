<?php
/*
 * ActivityStrea.ms PHP Library
 * Author: Jon Cianciullo (jon.cianciullo@gmail.com)
 * Homepage: http://github.com/jonnyjon/ActivityStreams-PHP-Library
 * version 0.1
*/

class ActivityStreams {
	
	function __construct() {
	}
	
	/*
	 * Converts RSS data to ActivityStreams format
	 * Requires SimplePie PHP Library: http://simplepie.org/
	*/
	function rss_to_activity_streams($data) {
		//
		$feed = new SimplePie();
		$feed->set_raw_data($data);
		//
		unset($data);
		//
		$feed->set_stupidly_fast(true);
		$feed->init();
		$feed->handle_content_type();
		//
		$id = md5($url);
		$title = 'submit';
		$link = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$activityStream = new ActivityStreamsDoc($id, $title, $link);
		//
		foreach($feed->get_items() as $item) :
			$author = $item->get_author();
			if(!$author) $author = $feed->get_author();
			//
			$activityStream->entry(
				$item->get_id(),
				date("r", $item->get_date()),
				($author ? $author->get_name() : null),
				($author ? $author->get_link() : null),
				$item->get_title(),
				$item->get_permalink(),
				$item->get_description()
			);
		endforeach;
		
		return $activityStream;
	}
	
}

class ActivityStreamsDoc {
	var $xml;	
	
	function __construct($id=null, $title=null, $link=null) {
	    $xml = <<<EOT
<feed 
xml:lang="en-US" 
xmlns="http://www.w3.org/2005/Atom" 
xmlns:activity="http://activitystrea.ms/spec/1.0/"
xmlns:thr="http://purl.org/syndication/thread/1.0" 
xmlns:media="http://purl.org/syndication/atommedia">
</feed>
EOT;
    	$this->xml = new SimpleXMLElement($xml);
		//
		if($id) $this->xml->addChild('id', $id);
		if($title) $this->xml->addChild('title', $title);
		if($link) {
			// <link rel="self" href="http://js-kit.com/atom/protobuilt.com" type="application/atom+xml"/>	
			 $node = $this->xml->addChild('link');
			 $node->addAttribute('rel', 'self');
			 $node->addAttribute('href', $link);
			 $node->addAttribute('type', 'application/atom+xml');
		}
	}
	
	function entry($id, $published, $name=null, $uri=null, $title, $link, $content=null) {
		//
		$title = '<![CDATA[' . $title . ']]>';
		$content = '<![CDATA[' . $content . ']]>';
		//
		$entry = $this->xml->addChild('entry');
		$entry->addChild('id', $id);
		$entry->addChild('published', $published);
		//
		$node = $entry->addChild('author');
		if($name) $node->addChild('name', $name);
		if($uri) $node->addChild('uri', $uri);
		//
		$entry->addChild('title', $title);
		$node = $entry->addChild('link');
			 $node->addAttribute('rel', 'alternate');
			 $node->addAttribute('href', $link);
			 $node->addAttribute('type', 'text/html');
		$entry->addChild('activity:verb', 'http://activitystrea.ms/schema/1.0/post', 'http://activitystrea.ms/spec/1.0/');
		//
		$node = $entry->addChild('activity:actor', null, 'http://activitystrea.ms/spec/1.0/');
			$node->addChild('activity:object-type', 'http://activitystrea.ms/schema/1.0/person', 'http://activitystrea.ms/spec/1.0/');
			if($link) $node->addChild('id', $uri, "");
			if($name) $node->addChild('title', $name, "");
		//
		$node = $entry->addChild('activity:object', null, 'http://activitystrea.ms/spec/1.0/');
			$node->addChild('activity:object-type', 'http://activitystrea.ms/schema/1.0/comment', 'http://activitystrea.ms/spec/1.0/');
			if($title) $node->addChild('summary', $title, "");
			if($content && !empty($content)) {
				$a = $node->addChild('content', "", "");
				$a[0] = $content;
			} else {
				$a = $node->addChild('content', "", "");
				$a[0] = $title;
			}
			if($link) $node->addChild('id', $link, "");
		$node = $entry->addChild('activity:target', null, 'http://activitystrea.ms/spec/1.0/');
			if($link) $node->addChild('id', $link, "");
			if($title) $node->addChild('title', $title, "");
		$node2 = $node->addChild('link', "", "");
			 $node2->addAttribute('rel', 'alternate');
			 $node2->addAttribute('href', $link);
			 $node2->addAttribute('type', 'text/html');
	}
	
	function __toString() {
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->xml->asXML());
		$str = $dom->saveXML();
		$str = str_replace(' xmlns=""', '', $str);
		$str = str_replace('&lt;![CDATA[', '<![CDATA[', $str);
		$str = str_replace(']]&gt;', ']]>', $str);
		return $str;
	}
}

?>