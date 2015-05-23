<?php

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = $_GET['id'];

makeLogin($uid);

?>