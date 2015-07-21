<?

switch ($param['action']) {
case 'set':
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
	break;
case 'get':
	// 已登录
	if (isset($_COOKIE['myauth_uid']) && !isset($_GET['inoauth'])) {
		$result = array('uid' => -1);
	}
	else {
		// 当前微信登录码绑定的总账号数
		$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
		switch (intval($cnt)) {
		// 绑定一个账号：直接登录
		case 1:
			$t = $myauth->result_first("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
			// 登录成功
			$result = array('uid' => [$t]);
			makeLogin($t);
			break;
		// 绑定两个账号：选择登录账号
		case 2:
			$result = array('uid' => array());
			$t = $myauth->query("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['queryCode']}'");
			while ($ids = $myauth->fetch_array($t))
				$result['uid'] []= $ids['auth_id'];
			break;
		}
	}
	echo json_encode($result);
	break;
case 'bind':
	list($uid, $openid) = explode("\t", uc_authcode($param['hash'], 'DECODE', 'myauth'));
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
			$result = '绑定成功！';
		}
	}
	echo $result;
	break;
case 'querybind':
	if (isset($param['uid']))
		$uid = $param['uid'];
	else
		die();
	$wechat = $myauth->result_first("SELECT `auth_wechat` FROM `sso` WHERE `auth_id` = '{$uid}'");
	exit(($wechat != NULL) ? true : '');
	break;
}
