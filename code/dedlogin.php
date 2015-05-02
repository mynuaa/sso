<?php

(!isset($param['username']) || !isset($param['password'])) && die();

if (dedverify($param['username'], $param['password'])) {
	$t = $db->result_first("SELECT COUNT(*) FROM `myauth` WHERE `auth_ded` = '{$param['username']}'");
	if (intval($t) > 1) {
		$result = array(
			'uid' => -1,
			'msg' => '有多于一个昵称与此学号绑定'
		);
	}
	else {
		$t = $db->result_first("SELECT `auth_id` FROM `myauth` WHERE `auth_ded` = '{$param['username']}'");
		if(!$t) {
			// 需要填写更多信息
			$result = array(
				'uid' => 0,
				'token' => rawurlencode(uc_authcode("{$param['username']}\tded\t" . time() . "\t" . $param['password'], 'ENCODE', 'myauth'))
			);
		}
		else {
			// 登录成功
			$_SESSION['myauth_uid'] = $t;
			$result = array(
				'uid' => $t
			);
		}
	}
}
else {
	$result = array(
		'uid' => -1,
		'msg' => '教务处验证失败'
	);
}

echo json_encode($result);
?>