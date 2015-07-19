<?

isset($param['token']) || die();

$param['token'] = rawurldecode($param['token']);
$info = explode("\t", uc_authcode($param['token'], 'DECODE', 'myauth'));
$info = array(
	'user' => $info[0],
	'from' => $info[1],
	'time' => $info[2]
);

if ($info['from'] === 'dz') {
	(!isset($param['username']) || !isset($param['password'])) && die();
	if (dedverify($param['username'], $param['password'])) {
		$t = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '{$param['password']}'");
		if (intval($t) >= 2) {
			$result = array(
				'uid' => -1,
				'msg' => '该学号已绑定两个账号，无法继续绑定'
			);
		}
		else {
			$sql = "INSERT INTO `sso` (`auth_dz`, `auth_ded`) VALUES ('{$info['user']}', '{$param['username']}')
					ON DUPLICATE KEY UPDATE `auth_ded` = '{$param['username']}'";
			$myauth->query($sql);
			$user = uc_get_user($info['user']);
			$result = array(
				'uid' => intval($user[0])
			);
		}
	}
	else {
		$result = array(
			'uid' => -1,
			'msg' => '教务处验证失败'
		);
	}
}
if ($info['from'] === 'ded') {
	(!isset($param['username']) || !isset($param['email'])) && die();
	$uid = uc_user_register($info['user'], $param['password'], $param['email']);
	if ($uid < 0) {
		$msg = array(
			-1 => '用户名不符合要求',
			-2 => '用户名有敏感词汇',
			-3 => '该用户名已经存在',
			-4 => 'Email格式有错误',
			-5 => 'Email不允许注册',
			-6 => 'Email已经被注册'
		);
		$result = array(
			'uid' => -1,
			'msg' => $msg[$uid]
		);
	}
}
if ($info['from'] === 'wechat') {
	(!isset($param['stuid']) ||
	 !isset($param['password']) ||
	 !isset($param['username']) ||
	 !isset($param['email'])) && die();
	$t = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '{$param['password']}'");
	if (intval($t) >= 2) {
		$result = array(
			'uid' => -1,
			'msg' => '该微信已绑定两个账号，无法继续绑定'
		);
	}
	else {
		// 
	}
}

echo json_encode($result);
