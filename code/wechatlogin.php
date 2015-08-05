<?

switch ($param['action']) {
case 'set':
	// 解密code
	$logincode = my_decrypt($param['code']);
	$openid = my_decrypt($param['openid']);
	$sql = "UPDATE `sso` SET `auth_logincode` = '{$logincode}' WHERE `auth_wechat` = '{$openid}'";
	$myauth->query($sql);
	$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_wechat` = '{$openid}'");
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
		$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_logincode` = '{$param['code']}'");
		switch (intval($cnt)) {
		// 绑定一个账号：直接登录
		case 1:
			$t = $myauth->result_first("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['code']}'");
			// 登录成功
			$result = array('uid' => [$t]);
			make_login($t);
			break;
		// 绑定两个账号：选择登录账号
		case 2:
			$result = array('uid' => array());
			$t = $myauth->query("SELECT `auth_id` FROM `sso` WHERE `auth_logincode` = '{$param['code']}'");
			while ($ids = $myauth->fetch_array($t))
				$result['uid'] []= $ids['auth_id'];
			break;
		}
	}
	echo json_encode($result);
	break;
case 'bind':
	$data = json_decode(my_decrypt($param['hash']), true);
	$uid = $data['uid'];
	$openid = $data['openid'];
	$wechat = $myauth->result_first("SELECT `auth_wechat` FROM `sso` WHERE `auth_id` = '{$uid}'");
	if ($wechat != null)
		$result = '你的纸飞机账号已经绑定微信啦，不能重复绑定哦:)';
	else {
		$cnt = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_wechat` = '{$openid}'");
		if (intval($cnt) >= 2)
			$result = '一个微信号最多只能绑定两个纸飞机账号呢:)';
		else {
			echo $sql = "UPDATE `sso` SET `auth_wechat` = '{$openid}' WHERE `auth_id` = '{$uid}'";
			die();
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
	exit(($wechat != null) ? true : '');
	break;
}
