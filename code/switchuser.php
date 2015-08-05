<?

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = getuid();

$newuid = $_GET['id'];

$ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$uid}");
$newded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = {$newuid}");

if ($ded == $newded && !in_array($auth_ded, array('JUST4TEST', 'FRESHMAN', 'MALLUSER')))
	make_login($newuid);
