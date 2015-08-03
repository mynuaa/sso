<?

(!isset($param['username']) || !isset($param['password'])) && die();

// 解密密码
$param['password'] = my_decrypt($param['password']);

// 分离输入中的子帐号
$split = split(':', $param['username']);
$param['username'] = $split[0];
$order = (count($split) == 2) ? intval($split[1]) : 1;
if ($order < 1) $order = 1;

if (dedverify($param['username'], $param['password'])) {
	$t = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '{$param['username']}'");
	if (intval($t) == 0) {
		// 需要填写更多信息
		$result = array(
			'uid' => 0,
			'token' => my_encrypt("ded\t{$param['username']}\t{$param['password']}")
		);
	}
	else if ($order > intval($t)) {
		$result = array(
			'uid' => -1,
			'msg' => '没有该子帐号'
		);
	}
	else {
		$t = $myauth->query("SELECT `auth_id` FROM `sso` WHERE `auth_ded` = '{$param['username']}' ORDER BY `auth_id`");
		while ($order--) {
			$row = $myauth->fetch_array($t);
		}
		// 登录成功
		$result = array(
			'uid' => $row['auth_id']
		);
	}
}
else {
	$result = array(
		'uid' => -1,
		'msg' => '教务处验证失败'
	);
}

echo json_encode($result);
