<?

if ($param['action'] === 'set') {
	// 解密queryCode
	$queryCode = uc_authcode($param['queryCode'], 'DECODE', 'myauth');
	$queryCode = explode("\t", $queryCode);
	(allAscii($queryCode[0]) && allAscii($queryCode[1]) && allAscii($queryCode[2])) || die();
	$sql = "UPDATE `sso` SET `auth_logincode` = '{$queryCode[0]}' WHERE `auth_wechat` = '{$queryCode[2]}'";
	$myauth->query($sql);
	$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_wechat` = '{$queryCode[2]}'");
	switch (intval($cnt)) {
	case 0:
		echo '你还没有绑定微信哦:)';
		break;
	case 1:
		echo '登录成功:)';
		break;
	case 2:
		echo '请在浏览器中选择你要登录的账号:)';
		break;
	}
}
else if ($param['action'] === 'get') {
	// 已登录
	if (isset($_COOKIE['myauth_uid'])) {
		$result = array('uid' => -1);
	}
	else {
		// 当前微信登录码绑定的总账号数
		$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
		switch (intval($cnt)) {
		// 绑定一个账号：直接登录
		case 1:
			$t = $myauth->result_first("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
			// 删除登录凭证
			$myauth->query("UPDATE `sso` SET `auth_logincode` = NULL WHERE `auth_logincode` = '{$param['queryCode']}'");
			// 登录成功
			$result = array('uid' => [$t]);
			makeLogin($t);
			break;
		// 绑定两个账号：选择登录账号
		case 2:
			$result = array('uid' => []);
			$t = $myauth->query("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
			while ($ids = $t->fetch_assoc())
				$result['uid'] []= $ids['auth_id'];
			$myauth->query("UPDATE `sso` SET `auth_logincode` = NULL WHERE `auth_logincode` = '{$param['queryCode']}'");
			break;
		}
	}
	echo json_encode($result);
}
else if ($param['action'] === 'bind') {
	list($uid, $nop, $openid) = uc_authcode($hash, 'DECODE', 'myauth');
	$wechat = $myauth->result_first("SELECT `auth_wechat` FROM `sso` WHERE `auth_id` = '{$uid}'");
	if ($wechat != NULL)
		$result = '你的纸飞机账号已经绑定微信啦，不能重复绑定哦:)';
	else {
		$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_wechat` = '{$openid}'");
		if (intval($cnt) >= 2)
			$result = '一个微信号最多只能绑定两个纸飞机账号呢:)';
		else {
			$sql = "UPDATE `sso` SET `auth_wechat` = '{$openid}' WHERE `auth_id` = '{$uid}'";
			$myauth->query($sql);
			$result = $uid . ' ' . $openid . ' ' . '绑定成功！';
		}
	}
	echo $result;
}
