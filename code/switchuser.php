<?

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = my_decrypt($_COOKIE['myauth_uid']) || setcookie('myauth_uid', '', time() - 3600);
$uid = json_decode($uid, true);
$uid = intval($uid['uid']);

$newuid = $_GET['id'];

$ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$uid}");
$newded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$newuid}");

if ($ded == $newded && !in_array($auth_ded, array('JUST4TEST', 'FRESHMAN', 'MALLUSER')))
	make_login($newuid);
