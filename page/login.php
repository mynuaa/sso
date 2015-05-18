<?php

if (isset($_POST['token'])) {
	$result = ajax(array(
		'url' => '?action=login',
		'method' => 'POST',
		'content' => json_encode(array(
			'type' => $_POST['type'],
			'username' => $_POST['username'],
			'password' => $_POST['password']
		))
	));
	$result = json_decode($result, true);
	switch ($result['uid']) {
	case -1:
		alert('验证失败：' . $result['msg'], $_SERVER['REQUEST_URI']);
		break;
	case 0:
		$_SESSION['myauth_token'] = $result['token'];
		jumpTo('?page=complete&redirect_uri=' . (isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : base64_encode($_SERVER['REQUEST_URI'])));
		break;
	default:
		makeLogin($result['uid']);
		jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI']);
		break;
	}
}

if (isset($_COOKIE['myauth_uid'])) {
	$uid = explode("\t", uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth'));
	$uid = intval($uid[1]);
	$user = uc_get_user($uid, 1)[1];
}
else {
	$user = NULL;
}

// 生成微信登录的加密串
$queryCode = sha1(rand(10000) . "\t" . time());

?>
<?php createHeader(); ?>
		<div id="frame1" class="frame">
		<?php if ($user != NULL) : ?>
			<h2>你好，<?php echo $user ?>。</h2>
			<input type="button" onclick="window.location.href='?action=logout'" value="退出登录">
		<?php else : ?>
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">学号</div>
				<div id="tab2" class="tab">论坛账号</div>
				<div id="tab3" class="tab" style="color:#BBB">微信号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="type" value="ded">
						<div class="form-group">
							<div><span class="field">学号</span></div>
							<div><input type="text" name="username" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">密码</span></div>
							<div><input type="password" name="password" class="area" required></div>
						</div>
					</form>
					<div class="form-footer">
						<input type="button" value="登录" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
				<div id="group2" class="group">
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="type" value="dz">
						<div class="form-group">
							<div><span class="field">账号</span></div>
							<div><input type="text" name="username" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">密码</span></div>
							<div><input type="password" name="password" class="area" required></div>
						</div>
					</form>
					<div class="form-footer">
						<input type="button" value="登录" onclick="document.querySelector('#group2>form').submit()">
					</div>
				</div>
				<div id="group3" class="group">
					<div>
						<img id="wechat_qrcode" src="http://qr.liantu.com/api.php?text=即将推出，敬请期待<?php // echo $queryCode; ?>" alt="扫码登录" style="width:200px;height:200px;border:2px solid;border-radius:0.5em;margin-bottom:0.5em;opacity:0.3">
						<!-- <div id="wechat_tip" style="margin:0 1em;font-size:0.9em;text-align:left">* 请在公众号“飞机耳朵”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div> -->
						<div id="wechat_tip" style="margin:0 1em;font-size:0.9em">* 微信登录功能即将推出，敬请期待！</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		</div>
	</div>
	<script>
		var queryCode="<?php echo $queryCode; ?>";
	</script>
	<script src="resources/js/wechat_query.js"></script>
<?php createFooter(); ?>