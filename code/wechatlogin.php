<?php

if (!isset($param['openid']))
	die();

$t = $db->result_first("SELECT `auth_id` FROM `myauth` WHERE `auth_wechat` = '{$param['openid']}'");
if(!$t) {
	// 需要填写更多信息
	$result = array(
		'uid' => -2,
		'token' => rawurlencode(uc_authcode("{$param['openid']}\twechat\t" . time(), 'ENCODE', 'myauth'))
	);
}
else {
	// 登录成功
	$_SESSION['myauth_uid'] = $t;
	$result = array(
		'uid' => $t
	);
}

echo json_encode($result);

?>