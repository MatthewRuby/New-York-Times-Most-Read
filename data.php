<?php
		$article = array();
		$article["title"] = array();
		$article["url"] = array();
		$article["abstract"] = array();
		$article["section"] = array();
		
		$quote = chr(34);
		$replace = chr(39);
		
		$num = 8;

		require "apiKey.php";
		
		for($i = 0; $i < $num; $i++) {

			$xml = simplexml_load_file('http://api.nytimes.com/svc/mostpopular/v2/mostviewed/all-sections/30.xml?&offset=' . ($i*20) . '&api-key=' . $apiKey);
			
			foreach ($xml->results->result as $results) {	
				$url = $results->url;
				$title = $results->title;
				$section = $results->section;
				$abstract = $results->abstract;
				
				
				$article["title"][] = str_replace($quote,$replace,$title);
				$article["url"][] = $url;
				$article["abstract"][] = str_replace($quote,$replace,$abstract);
				$article["section"][] = $section;
				
			}
		}
?>