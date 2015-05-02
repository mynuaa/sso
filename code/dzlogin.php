<?php

(!isset($param['username']) || !isset($param['password'])) && die();

// 验证登录
list($uid, $username, $password, $email) = uc_user_login($param['username'], $param['password']);

if($uid > 0) {
	$t = $db->result_first("SELECT `auth_id` FROM `myauth` WHERE `auth_id` = $uid");
	if(!$t) {
		// 需要填写更多信息
		$result = array(
			'uid' => 0,
			'token' => rawurlencode(uc_authcode("$username\tdz\t" . time() . "\t" . $param['password'], 'ENCODE', 'myauth'))
		);
	}
	else {
		// 登录成功
		$_SESSION['myauth_uid'] = $uid;
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

?>