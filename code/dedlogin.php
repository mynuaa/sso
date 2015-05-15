<?php

(!isset($param['username']) || !isset($param['password'])) && die();

// 分离输入中的子帐号
$split = split(':', $param['username']);
$param['username'] = $split[0];
$order = (count($split) == 2) ? intval($split[1]) : 1;
if ($order < 1) $order = 1;

if (dedverify($param['username'], $param['password'])) {
	$t = $db->result_first("SELECT COUNT(*) FROM `myauth` WHERE `auth_ded` = '{$param['username']}'");
	if (intval($t) == 0) {
		// 需要填写更多信息
		$result = array(
			'uid' => 0,
			'token' => rawurlencode(uc_authcode("{$param['username']}\tded\t" . time() . "\t" . $param['password'], 'ENCODE', 'myauth'))
		);
	}
	else if ($count > $t) {
		$result = array(
			'uid' => -1,
			'msg' => '没有该子帐号'
		);
	}
	else {
		$t = $db->query("SELECT `auth_id` FROM `myauth` WHERE `auth_ded` = '{$param['username']}'");
		while ($order--) {
			$row = $db->fetch_array($t);
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
?>