<?php

/**
 * General variables
 * ----------------------------------------------------------------
 */

	$client_id = '9bfba816e906ad58d13cb9e6b65ad62f';
	$client_secret = '30859d290b24fbde3a77e87f43c922c1';
	$scope = 'accounts_read';
	
	$error = '';
	$accountMessage = '';
	
	
	/* Account information */
	$mentions = array();
	$encodedMentions;
	
	
	// Start session
	session_start();
	
	// Action for logout message
	if (isset($_GET['action']) && $_GET['action'] === 'logout'){
		$accountMessage = 'You are succesfully logged out';
	} 


/**
 * Handle action 'btnLogin' Authorize the app
 * ----------------------------------------------------------------
 */
	
		if (isset($_POST['btnLogin'])) {
			
			header('Location: http://app.engagor.com/oauth/authorize/?client_id='.$client_id.'&response_type=code&scope=accounts_read');
			
		}
	
		if (isset($_POST['btnLogout'])){
			session_destroy();
			header('Location: index.php?action=logout');
		}
	
	
	/**
	 * App is authorized 
	 * ----------------------------------------------------------------
	 */
	 
	 		$code = isset($_REQUEST['code']) ? $_REQUEST["code"]  : null ;
	 		
	 		
	 		if (isset($_GET['code'])){
	 			if (isset($_GET['error']) && $_GET["error"] && $_GET["error"] === "access_denied") {
	 				$error = "The user did not authorize your application to use your Engagor account.";
	 			}		
	 			
	 			$token_url = 'http://app.engagor.com/oauth/access_token/?'
	 			               . 'client_id=' . $client_id . '&client_secret=' . $client_secret
	 			               . '&grant_type=authorization_code&code='.$code;
	 			
	 			$response = @file_get_contents($token_url);
	 			$params = json_decode($response, true); 
	 			      
	 			       
	 			if (!$params["access_token"]) {
	 				$error =  "We could not validate your access token.";
	 			}
	 			else {
	 				$_SESSION['access_token'] = $params["access_token"]; //Stores the token in a session
	 			}
	 			
	 			// API call: Account information
	 			$response = @file_get_contents('http://api.engagor.com/me/accounts?access_token='.$_SESSION['access_token']);
	 			$params = json_decode($response, true); 
	 			$_SESSION['name'] = $params['response']['data'][0]['name'];
	 			$_SESSION['accountId'] = $params['response']['data'][0]['id'];
				
				// Projects & topics
				// $projects = $params['response']['data'][0]['projects'];
				//dump($projects);
				
	 			isset($_SESSION['name']) ? $accountMessage = $_SESSION['name'].' is successfully logged in' : null;
	 			
	 			
	 			// API call get inbox messages
	 			$response = @file_get_contents('http://api.engagor.com/'.$_SESSION['accountId'].'/inbox/mentions?access_token='.$_SESSION['access_token'].'&limit=100');

	 			$params = json_decode($response, true); 
	 			
	 			// loop throug the mentions & search for location
	 			foreach ($params['response']['data'] as $mention) {
	 				if (isset($mention['location']) && isset($mention['location']['latitude'])){
	 					array_push($mentions, $mention);
	 				}
	 			}
	 			$encodedMentions = json_encode($mentions);
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
	 		
?>









<!DOCTYPE html>
<html lang="en">

	<head>
	    <title>Engagor :: MentionMap</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!-- Bootstrap -->
	    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
	    	    
	   	<script src="http://code.jquery.com/jquery-latest.js"></script>
	  	<script src="js/bootstrap.min.js"></script>
	   	<script src="js/map.js"></script>
	      
	      	    
  	</head>
  	
  		<body>
  			
			<div class="navbar navbar-inverse navbar-fixed-top">
			  	<div class="navbar-inner">
			   		<div class="container">
			            <a class="brand" href="index.php"><img src="img/logo.png" alt="" /></a>
			            <div class="nav-collapse collapse">
			              	<ul class="nav">
			                	<?php if(!isset($_SESSION['access_token'])){ ?>
			                	<li>
			                		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="loginForm">
			                			<input type="submit" class="btn btn-primary" id="btnLogin" name="btnLogin" value="Login" />
			                		</form>	
			                	</li>
			                	<?php } else { ?>
			                	<li>
			                		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="logOutForm">
			                			<input type="submit" class="btn btn-primary" id="btnLogout" name="btnLogout" value="Logout" />
			                		</form>	
			                	</li>
			                	<?php } ?>
			              	</ul>
			            </div><!--/.nav-collapse -->
			     	</div>
				</div>
			</div>
			
	  
	  
			<div class="container">
				<!-- Messages -->
				<?php if ($accountMessage){ ?>
					<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo($accountMessage) ?></div>
				<?php } if ($error){ ?>
					<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo($error) ?></div>
				<?php } ?>
				<!-- End section Messages -->	
				
				
				<?php if (!isset($_SESSION['access_token'])) { ?>	
					<div class="hero-unit">		
						<h2><img src="img/engagorlogo.png" alt="" /> MentionsMap</h2>
						<p>Login with your Engagor account and view your mentions on google maps</p>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="loginForm">
							<input type="submit" class="btn btn-primary" id="btnLogin" name="btnLogin" value="Login" />
						</form>	
						<a href="http://www.engagor.com" class="btn">Visit Engagor</a>
					</div>	
				<?php } ?>		
				

			</div>
			
			
				
			<!-- Google maps -->
			<div id="map"></div>
			
			

			
				<footer>
					<div class="container">
			     		<p>This app is powered by: <a href="http://engagor.com/"><img class="logoPow" src="img/engagorlogo.png" alt="" /></a></p>
			     	</div>
				</footer>
			</div>
	            
	        
	  </body>




</html>