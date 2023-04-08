
<?php 
require_once 'vendor/autoload.php';
require_once 'IDP/autoload.php';
$client = new \IDP_Client();
$client->setClientId('nwzFcJD2Rv2hK7agoyyj');
$client->setClientSecret('S9yGNoP1uB7lI4CsFkhq.X5hul7bXtL');
$client->setRedirectUri('http://localhost/sso');
$authUrl = $client->logoutRequest();
header("Location: ".$authUrl);
