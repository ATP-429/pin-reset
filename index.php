<?php
    require 'data.php';

    $email = $_GET['email'];
    $dh = new DataHandler;
    $dh->connect();
    echo $dh->register($email, 3041);
?>
