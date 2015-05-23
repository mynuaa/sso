<?php

setcookie('myauth_token', '', 0, '/');
setcookie('myauth_uid', '', 0, '/');
$url = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : '?page=login';

header('Location: ' . $url);

?>