<?php

if ($param['action'] === 'set') {
	// 解密queryCode
	$queryCode = uc_authcode($param['queryCode'], 'DECODE', 'myauth');
	$queryCode = explode("\t", $queryCode);
	var_dump($queryCode);
	(allAscii($queryCode[0]) && allAscii($queryCode[1]) && allAscii($queryCode[2])) || die();
	$db->query("UPDATE `myauth` SET `auth_logincode` = '{$queryCode[0]}' WHERE `auth_wechat` = '{$queryCode[2]}'");
	exit('');
}
else if ($param['action'] === 'get') {
	$t = $db->result_first("SELECT `auth_id` FROM `myauth` WHERE `auth_logincode` = '{$param['queryCode']}'");
	if(!$t) {
		// 需要填写更多信息
		$result = array(
			'uid' => -1,
			'msg' => '该微信未绑定'
		);
	}
	else {
		// 删除登录凭证
		$db->query("UPDATE `myauth` SET `auth_logincode` = NULL WHERE `auth_logincode` = '{$param['queryCode']}'");
		// 登录成功
		$result = array(
			'uid' => $t
		);
		$_SESSION['myauth_uid'] = $t;
	}
	echo json_encode($result);
}

?>