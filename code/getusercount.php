<?php

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth');
$uid = explode("\t", $uid)[1];

$auth_ded = $db->result_first("SELECT `auth_ded` FROM `myauth` WHERE `auth_id` = $uid");
if ($auth_ded == '000') die();
$result = $db->query("SELECT `auth_id` FROM `myauth` WHERE `auth_ded` = '$auth_ded'");

$t = array();
while ($row = $db->fetch_array($result)) {
	$t []= $row['auth_id'];
}

echo implode("\t", $t);

?>