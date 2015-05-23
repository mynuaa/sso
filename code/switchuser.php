<?php

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth');
$uid = explode("\t", $uid)[1];

$newuid = $_GET['id'];

$ded = $db->result_first("SELECT `auth_ded` FROM `myauth` WHERE `auth_id` = {$uid}");
$newded = $db->result_first("SELECT `auth_ded` FROM `myauth` WHERE `auth_id` = {$newuid}");

if ($ded == $newded)
	makeLogin($newuid);

?>