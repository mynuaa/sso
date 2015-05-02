<?php

require_once 'config.inc.php';
require_once 'include/init.inc.php';

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
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
		require_once 'code/logout.php';
		break;
	case 'complete':
		require_once 'code/complete.php';
		break;
	case 'user':
		$result = array(
			'uid' => isset($_SESSION['myauth_uid']) ? $_SESSION['myauth_uid'] : -1
		);
		echo json_encode($result);
	case 'pmlist':
		require_once 'code/pmlist.php';
		break;
	case 'pmwin':
		require_once 'code/pmwin.php';
		break;
	case 'friend':
		require_once 'code/friend.php';
		break;
	case 'avatar':
		require_once 'code/avatar.php';
		break;
	}
}
else if (isset($_GET['page'])) {
	// 通过page判断需要的页面
	if (in_array($_GET['page'], array('login', 'logout', 'complete')))
		require_once 'page/' . $_GET['page'] . '.php';
}
else {
	die();
}

?>