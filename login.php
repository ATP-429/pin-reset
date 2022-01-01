<?php
	session_start();
	require 'server.php';
	$server = new Server;
	$server->connect();
	
	if($server->is_user_logged())
		echo "Already logged into ".$_SESSION['username'];
	else
	{
		$email = $_GET['email'];
		$password = $_GET['password'];
		$server->login($email, $password);
		if($server->is_user_logged())
			echo "Logged into ".$_SESSION['username'];
		else
			echo "Invalid username/password";
	}
	session_destroy();
?>