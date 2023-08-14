<?php
require 'vendor/autoload.php';
use TikTokLoginKit\Connector;
$client_id='';
$client_secret='';
$redirect_uri='';
$_TK = new TikTokLoginKit\Connector($client_id, $client_secret, $redirect_uri);
if (TikTokLoginKit\Connector::receivingResponse()) { 
	try {
		$token = $_TK->verifyCode($_GET[TikTokLoginKit\Connector::CODE_PARAM]);
		// Your logic to store the access token
		$user = $_TK->getUser();
		// Your logic to manage the User info
		$videos = $_TK->getUserVideoPages();
		// Your logic to manage the Video info
	} catch (Exception $e) {
		echo "Error: ".$e->getMessage();
		echo '<br /><a href="?">Retry</a>';
	}
}
?>