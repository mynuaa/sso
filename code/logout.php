<?php

if (isset($_SESSION['myauth_token'])) unset($_SESSION['myauth_token']);
if (isset($_SESSION['myauth_uid'])) unset($_SESSION['myauth_uid']);
setcookie('myauth_uid', '', 0, '/');
$url = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : '?page=login';

header('Location: ' . $url);

?>