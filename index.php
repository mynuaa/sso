<?php

require_once 'config.inc.php';
require_once 'include/init.inc.php';

$myauth = new dbstuff;
$myauth->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

require_once 'uc_client/client.php';

if (isset($_GET['action'])) {
	// 解包传入参数
	$param = file_get_contents('php://input');
	$param = json_decode($param, true);
	// 设置返回类型为JSON
	header('Content-type: application/json');
	// 通过action判断类型
	switch(@$_GET['action']) {
	case 'login':
		if (in_array($param['type'], array('dz', 'ded', 'wechat')))
			require_once "code/{$param['type']}login.php";
		break;
	case 'logout':
	case 'complete':
	case 'getuser':
	case 'getusercount':
	case 'switchuser':
		require_once 'code/' . $_GET['action'] . '.php';
		break;
	}
}
else if (isset($_GET['page'])) {
	// 通过page判断需要的页面
	if (in_array($_GET['page'], array('login', 'complete', 'newrepeat', 'oauth', 'freshman', 'shop')))
		require_once 'page/' . $_GET['page'] . '.php';
}
else {
	die();
}
