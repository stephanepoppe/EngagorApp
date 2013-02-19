<?php	

		$mentions = array();
		
		
		session_start();
		
		// API call get inbox messages
		$response = @file_get_contents('http://api.engagor.com/'.$_SESSION['accountId'].'/inbox/mentions?access_token='.$_SESSION['access_token'].'&limit=100');

	 	$params = json_decode($response, true); 

	 	
	 	
	 	for ($i = 0; $i < count($params); $i++) {
			if (isset($params['response']['data'][$i]['location']) && isset($params['response']['data'][$i]['location']['latitude'])){				
				array_push($mentions, $params['response']['data'][$i]);
			}
	 	}
	 	
	 	
	 	if (sizeof($mentions) == 0){
	 		print(json_encode(array("error" => "There are no mentions with a location")));
	 	}
	 	else{
	 		print json_encode($mentions, JSON_FORCE_OBJECT);
	 	}	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	/**
	 	* Debug: Array
	 	* ----------------------------------------------------------------
	 	*/
	 	
	 	function dump($var) { 
	 		echo '<pre>';
	 		print_r($var); 
	 		echo '</pre>';
	 	}