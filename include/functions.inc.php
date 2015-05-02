<?php

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
	echo '<script>';
	echo 'alert("' . $content . '");';
	echo 'window.location.href="' . ($redirect ? $redirect : $_SERVER['REQUEST_URI']) . '"';
	echo '</script>';
	exit();
}
function jumpTo($url = NULL) {
	echo '<script>';
	echo 'window.location.href="' . ($url ? $url : $_SERVER['REQUEST_URI']) . '"';
	echo '</script>';
	exit();
}
function createHeader() {
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
	<title>纸飞机用户登录</title>
	<link rel="stylesheet" href="resources/css/main.css">
</head>
<body>
	<div class="background"></div>
	<header>
		<div class="container">
			<nav>
				<ul class="nav-ul">
					<a href="http://my.nuaa.edu.cn/portal.php"><li>门户</li></a>
					<a href="http://my.nuaa.edu.cn/forum.php"><li>论坛</li></a>
					<a href="/mall/"><li>商城</li></a>
				</ul>
			</nav>
		</div>
	</header>
	<div class="container center">
		<!--[if lt IE 9]>
		<div class="tip tip-warning">你正在使用IE的一个不安全版本！<a href="http://browsehappy.com/" target="_blank">点击这里</a>下载现代浏览器。</div>
		<![endif]-->
			<img src="resources/img/logo.png" alt="纸飞机南航青年网络社区" class="zfjlogo mt1 mb1">
EOF;
	echo $str;
}
function createFooter() {
	$str = <<<EOF
	<script src="resources/js/main.js"></script>
</body>
</html>
EOF;
	echo $str;
}

?>