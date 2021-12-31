<?php
    require 'data.php';

	$email = $_GET['email'];
	$password = $_GET['password'];
    $dh = new DataHandler;
    $dh->connect();
    echo $dh->register($email,$password);
?>
