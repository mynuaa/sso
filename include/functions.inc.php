<?

function make_login($uid, $appid = '') {
	$arr = ['uid' => $uid];
	setcookie('myauth_uid', my_encrypt(json_encode($arr), $appid), time() + 3600 * 10000, '/', NULL, NULL, true);
}
function getuid() {
	if (!($uid = my_decrypt($_COOKIE['myauth_uid']))) {
		setcookie('myauth_uid', '', time() - 3600);
		return false;
	}
	if (!$uid) return false;
	$uid = json_decode($uid, true);
	return $uid['uid'];
}
function ajax($a) {
	$a['url'] = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . $a['url'];
	switch ($a['method']) {
	case 'GET':
		//初始化
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $a['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	case 'POST':
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $a['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $a['content']);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
}
function alert($content, $redirect = NULL) {
	header('Content-type: text/html; charset=utf-8');
	echo '<script>';
	echo 'alert("' . $content . '");';
	echo 'window.location.replace("' . ($redirect ? $redirect : $_SERVER['REQUEST_URI']) . '")';
	echo '</script>';
	exit();
}
function jumpTo($url = NULL) {
	header('Content-type: text/html; charset=utf-8');
	echo '<script>';
	echo 'window.location.replace("' . ($url ? $url : $_SERVER['REQUEST_URI']) . '")';
	echo '</script>';
	exit();
}
function createHeader($pagetitle = '用户登录') {
	$public_key = get_pubkey_for_js();
	$str = <<<EOF
<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
	<meta name="format-detection" content="telphone=no,email=no">
	<meta name="msapplication-tap-highlight" content="no">
	<title>$pagetitle - 纸飞机南航青年网络社区</title>
	<link rel="stylesheet" href="resources/css/main.css">
	<link rel="stylesheet" href="/lib/mui/css/mui.min.css">
	<!--[if lt IE 10]>
	<script src="resources/js/base64.js"></script>
	<script src="resources/js/ieBetter.js"></script>
	<script src="resources/js/html5.js"></script>
	<script src="resources/js/classList.min.js"></script>
	<![endif]-->
	<script src="resources/js/my_encrypt.js"></script>
	<script>var key=makeKeyPair("{$public_key}")</script>
</head>
<body>
	<!-- <header>
		<div class="container">
			<nav>
				<ul class="nav-ul">
					<a href="/portal.php"><li>门户</li></a>
					<a href="/forum.php"><li>论坛</li></a>
					<a href="/mall/"><li>商城</li></a>
				</ul>
			</nav>
		</div>
	</header> -->
	<div class="wrapper">
		<div class="container center">
			<img src="resources/img/logo.png" alt="纸飞机南航青年网络社区" class="zfjlogo mt1 mb1">
			<!--[if IE 8]>
			<div class="tip tip-warning">你正在使用IE的一个旧版本，<a href="http://browsehappy.com/" target="_blank">点击这里</a>下载现代浏览器。</div>
			<![endif]-->
			<!--[if IE 7]>
			<div class="tip tip-danger">你正在使用的IE浏览器已不被支持！<a href="http://browsehappy.com/" target="_blank">点击这里</a>下载现代浏览器。</div>
			<![endif]-->
EOF;
	echo $str;
}
function createFooter() {
	echo '</div><script src="/lib/mui/js/mui.min.js"></script><script src="resources/js/main.js"></script></body></html>';
	//echo '</div><script src="resources/js/mui.min.js"></script><script src="resources/js/main.js"></script></body></html>';
}
function my_encrypt($str, $appid = '') {
	if ($appid != '') $appid .= '_';
	$public_key = openssl_pkey_get_public(file_get_contents(MYAUTH_CERT_PATH . "/{$appid}public_key.pem"));
	if (!openssl_public_encrypt($str, $encrypted, $public_key)) return false;
	return base64_encode($encrypted);
}
function my_decrypt($str, $appid = '') {
	if ($appid != '') $appid .= '_';
	$encrypted = base64_decode($str);
	$private_key = openssl_pkey_get_private(file_get_contents(MYAUTH_CERT_PATH . "/{$appid}private_key.pem"));
	if (!openssl_private_decrypt($encrypted, $str, $private_key)) return false;
	return $str;
}
function get_pubkey_for_js($appid = '') {
	if ($appid != '') $appid .= '_';
	return trim(file_get_contents(MYAUTH_CERT_PATH . "/{$appid}js_public_key.dat"));
}
