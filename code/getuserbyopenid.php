<?

isset($_GET['openid']) ? $openid = $_GET['openid'] : die('未设置openid！');

$info = $myauth->query("SELECT `auth_id`, `auth_ded` FROM `sso` WHERE `auth_wechat` = '{$openid}' ORDER BY `auth_id` LIMIT 1");
$info = $myauth->fetch_array($info);
$user = uc_get_user($info['auth_id'], 1);

$return = [
	'uid' => $user[0],
	'username' => $user[1],
	'stuid' => $info['auth_ded']
];

echo json_encode($return);
