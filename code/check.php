<?

(!isset($_GET['param']) || !isset($_GET['value'])) && die('请求信息不完整');

$result = array();
$msgArray = [
	'1' => '',
	'-1' => '用户名不合法',
	'-2' => '用户名包含不允许注册的词语',
	'-3' => '用户名已经存在',
	'-4' => 'Email格式有误',
	'-5' => 'Email不允许注册',
	'-6' => '该Email已经被注册'
];

switch ($_GET['param']) {
case 'email':
	$result['msg'] = $msgArray[uc_user_checkemail($_GET['value'])];
	break;
case 'username':
	$result['msg'] = $msgArray[uc_user_checkname($_GET['value'])];
	break;
}

echo json_encode($result);
