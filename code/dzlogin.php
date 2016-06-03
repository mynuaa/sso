<?

(!isset($param['username']) || !isset($param['password'])) && die();

// 解密密码
$param['password'] = my_decrypt($param['password']);

// 验证登录
list($uid, $username, $password, $email) = uc_user_login($param['username'], $param['password']);
if($uid > 0) {
	$t = $myauth->result_first("SELECT `auth_id` FROM `sso` WHERE `auth_id` = $uid");
	if(!$t) {
		// 需要填写更多信息
		$result = array(
			'uid' => 0,
			'token' => my_encrypt("dz\t{$username}\t{$param['password']}")
		);
	}
	else {
		// 登录成功
		$result = array(
			'uid' => $uid
		);
	}
}
else {
	$result = array(
		'uid' => -1,
		'msg' => '验证失败'
	);
}

echo json_encode($result);
