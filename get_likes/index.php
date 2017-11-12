<?php
session_start();
require_once __DIR__ . '/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '****',
  'app_secret' => '****',
  'default_graph_version' => 'v2.9'
  ]);

$helper = $fb->getRedirectLoginHelper();

// app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
define('APP_URL', 'http://localhost:8888//');

$permissions = ['user_posts', 'user_photos']; // optional

try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();

  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }

if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;

	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();

		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}

	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./');
	}

	// validating user access token
	try {
		$user = $fb->get('/me');
		$user = $user->getGraphNode()->asArray();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		// if access token is invalid or expired you can simply redirect to login page using header() function
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	// getting likes data of recent 100 posts by user
	$getPostsLikes = $fb->get('/me/posts?fields=likes.limit(1000){name,id}&limit=100');
	$getPostsLikes = $getPostsLikes->getGraphEdge()->asArray();

	// printing likes data as per requirements
	foreach ($getPostsLikes as $key) {
		if (isset($key['likes'])) {
			echo count($key['likes']) . '<br>';
			foreach ($key['likes'] as $key) {
				echo $key['name'] . '<br>';
			}
		}
	}

	// getting likes data of recent 100 photos by user
	$getPhotosLikes = $fb->get('/me/photos?fields=likes.limit(1000){name,id}&limit=100&type=uploaded');
	$getPhotosLikes = $getPhotosLikes->getGraphEdge()->asArray();

	// printing likes data as per requirements
	foreach ($getPhotosLikes as $key) {
		if (isset($key['likes'])) {
			echo count($key['likes']) . '<br>';
			foreach ($key['likes'] as $key) {
				echo $key['name'] . '<br>';
			}
		}
	}

  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
} else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	$loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
	echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
}
