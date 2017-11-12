<?php
session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new \Facebook\Facebook([
  'app_id' => '*Your APP ID',
  'app_secret' => 'Your APP Secret',
  'default_graph_version' => 'v2.9',
]);
   $permissions = ['user_posts']; // optional
   $helper = $fb->getRedirectLoginHelper();
   $accessToken = $helper->getAccessToken();

if (isset($accessToken)) {

 		$url = "https://graph.facebook.com/v2.9/me?fields=posts.limit(100)&access_token={$accessToken}";
		$headers = array("Content-type: application/json");


		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_URL, $url);
	   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');
		 curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		 $st=curl_exec($ch);
		 $result=json_decode($st,TRUE);;
		 $data = $result['posts']['data'];
//echo("<pre>");
//print_r($messg);
//echo("</pre>");

		 foreach ($data as $item) {
		 	if (isset($item['full_picture'])) {
		 		echo $item['message']."<br>";
		 		echo "<img src=".$item['full_picture']."><br>";
		 	}else{
		 		echo $item['message']."<br>";
		 	}
		 }
} else {
	$loginUrl = $helper->getLoginUrl('http://localhost:8888', $permissions);
	echo '<a href="' . $loginUrl . '">Login with Facebook</a>';
}
//database stats
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "myDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach($data as $post){
  $sql = "INSERT INTO Myposts (id, post)
  VALUES ('id', '".$post['story']."')";

echo $conn->query($sql);

  if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
}


echo"$data";

$conn->close();

//database ends

?>
