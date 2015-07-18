<?

$redirect_uri = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI'];

// 从表单发过来的信息（不包括微信登录）
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
		setcookie('myauth_token', $result['token'], time() + 3600 * 10000, '/');
		jumpTo('?page=complete&redirect_uri=' . (isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : base64_encode($_SERVER['REQUEST_URI'])));
		break;
	default:
		makeLogin($result['uid']);
		jumpTo($redirect_uri);
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
<? createHeader('用户登录'); ?>
		<div class="tip tip-info">最快捷的方法就是用学号/工号登录</div>
		<div class="tip tip-info">不知道学号/工号？2015级新生请点击<a class="green" href="?page=freshman"><b>这里</b></a>，商家请点击<a class="green" href="?page=malluser"><b>这里</b></a></div>
		<div id="frame1" class="frame">
		<? if ($user != NULL) : ?>
			<h2>你好，<? echo $user ?>。</h2>
			<input type="button" onclick="window.location.href='?action=logout'" value="退出登录">
		<? else : ?>
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">学号/工号</div>
				<div id="tab2" class="tab">论坛账号</div>
				<div id="tab3" class="tab">微信号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<? echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="type" value="ded">
						<div class="form-group">
							<div><span class="field">学号/工号</span></div>
							<div><input type="text" name="username" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">密码</span></div>
							<div><input type="password" name="password" class="area" required></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<input type="button" value="登录" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
				<div id="group2" class="group">
					<form action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<? echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="type" value="dz">
						<div class="form-group">
							<div><span class="field">论坛昵称</span></div>
							<div><input type="text" name="username" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">论坛密码</span></div>
							<div><input type="password" name="password" class="area" required></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<input type="button" value="登录" onclick="document.querySelector('#group2>form').submit()">
					</div>
				</div>
				<div id="group3" class="group">
					<div>
						<img id="wechat_qrcode" src="http://qr.liantu.com/api.php?text=wechat://<? echo $queryCode; ?>" alt="扫码登录" style="width:200px;height:200px;border:2px solid;border-radius:0.5em;margin-bottom:0.5em">
						<div id="wechat_tip" style="margin:0 1em;font-size:0.9em;text-align:left">* 请在公众号“南航纸飞机”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					</div>
				</div>
			</div>
		<? endif; ?>
		</div>
	</div>
	<script>
		var queryCode="<?=$queryCode?>";
		var redirect_uri="<?=$redirect_uri?>";
		var bredirect_uri="<?=base64_encode($redirect_uri)?>";
	</script>
	<script src="resources/js/wechat_query.js"></script>
<?

createFooter();