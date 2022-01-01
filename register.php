<?php
    require 'server.php';

	$username = $_GET['username'];
	$email = $_GET['email'];
	$password = $_GET['password'];
    $server = new Server;
    $server->connect();
    echo $server->register($username, $email, $password);
?>
