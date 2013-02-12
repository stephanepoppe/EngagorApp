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
	 			$response = @file_get_contents('http://api.engagor.com/me?access_token='.$_SESSION['access_token']);
	 			$params = json_decode($response, true); 
	 			$_SESSION['name'] = $params['response']['name'];
	 			$_SESSION['userId'] = $params['response']['id'];
	 			var_dump($params, true);
	 			isset($_SESSION['name']) ? $accountMessage = $_SESSION['name'].' is successfully logged in' : null;
	 			
	 			
	 			// API call get inbox messages
	 			//http://api.engagor.com/:account_id/inbox/mentions
	 			$response = @file_get_contents('http://api.engagor.com/'.$_SESSION['userId'].
	 				'/inbox/mentions?access_token='.$_SESSION['access_token']);
	 				
	 			$params = json_decode($response, true); 
	 			var_dump($params, true);
	 		}
	 		
?>









<!DOCTYPE html>
<html lang="en">

	<head>
	    <title>Engagor :: MentionMap</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!-- Bootstrap -->
	    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	    
	    <style>
	      body {
	        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	      }
	      
	      /* Sticky footer styles
	            -------------------------------------------------- */
	      
	            html,
	            body {
	              height: 100%;
	              /* The html and body elements cannot have any padding or margin. */
	            }
	      
	            /* Wrapper for page content to push down footer */
	            #wrap {
	              min-height: 100%;
	              height: auto !important;
	              height: 100%;
	              /* Negative indent footer by it's height */
	              margin: 0 auto -60px;
	            }
	      
	            /* Set the fixed height of the footer here */
	            #push,
	            #footer {
	              height: 60px;
	            }
	            #footer {
	              background-color: #f5f5f5;
	            }
	      
	    </style>
  	</head>
  	
  		<body>
  			
			<div class="navbar navbar-inverse navbar-fixed-top">
			  	<div class="navbar-inner">
			   		<div class="container">
			            <a class="brand" href="#">Engagor App</a>
			            <div class="nav-collapse collapse">
			              	<ul class="nav">
			                	<li class="active"><a href="index.php">Home</a></li>
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
			</div>
			
			<!--	    
	    	<div id="footer">
	        	<div class="container">
	    			<p>Stephane Poppe</p>
	    		</div>
	 		</div>
	        -->
	        
	            <script src="http://code.jquery.com/jquery.js"></script>
	            <script src="js/bootstrap.min.js"></script>
	        
	  </body>




</html>