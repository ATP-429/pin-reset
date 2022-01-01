<?php
	require 'server.php';

	$email = $_GET['email'];
	$password = $_GET['password'];
	$verification_id = $_GET['verification_id'];
	
	$server = new Server;
	$server->connect();
	$auth = $server->auth($email, $password);
	if($auth['success'])
		echo $server->set_id($email, $verification_id);
	else
		echo "Invalid username/password";
?>