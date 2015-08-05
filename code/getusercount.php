<?

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = getuid();

$auth_ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = $uid");

// 禁止测试账号、新生、商家的小号
if (in_array($auth_ded, array('JUST4TEST', 'FRESHMAN', 'MALLUSER'))) die();

$result = $myauth->query("SELECT `auth_id` FROM `sso` WHERE `auth_ded` = '$auth_ded'");

$t = array();
while ($row = $myauth->fetch_array($result)) {
	$t []= $row['auth_id'];
}

echo implode("\t", $t);
