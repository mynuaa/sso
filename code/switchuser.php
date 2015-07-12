<?

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth');
$uid = explode("\t", $uid)[1];

$newuid = $_GET['id'];

$ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$uid}");
$newded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$newuid}");

if ($ded == $newded && !in_array($auth_ded, array('JUST4TEST', 'FRESHMAN', 'MALLUSER')))
	makeLogin($newuid);
