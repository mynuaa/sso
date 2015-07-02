<?php

if ($param['action'] === 'set') {
	// 解密queryCode
	$queryCode = uc_authcode($param['queryCode'], 'DECODE', 'myauth');
	$queryCode = explode("\t", $queryCode);
	(allAscii($queryCode[0]) && allAscii($queryCode[1]) && allAscii($queryCode[2])) || die();
	echo $sql = "UPDATE `sso` SET `auth_logincode` = '{$queryCode[0]}' WHERE `auth_wechat` = '{$queryCode[2]}'";
	$myauth->query($sql);
	echo "\nQuery successful.";
}
else if ($param['action'] === 'get') {
	$t = $myauth->result_first("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
	if(isset($_COOKIE['myauth_uid']) || !$t) {
		// 需要填写更多信息
		$result = array(
			'uid' => -1,
			'msg' => '没有信息'
		);
	}
	else {
		// 删除登录凭证
		$myauth->query("UPDATE `sso` SET `auth_logincode` = NULL WHERE `auth_logincode` = '{$param['queryCode']}'");
		// 登录成功
		$result = array(
			'uid' => $t
		);
		makeLogin($t);
	}
	echo json_encode($result);
}
