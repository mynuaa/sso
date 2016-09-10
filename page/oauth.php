<?

if (isset($_GET['access_token'])) {
	$result = array();
	if (isset($_GET['uid']))
		$result['uid'] = $myauth->result_first("SELECT `token_uid` FROM `oauth_tokens` WHERE `token_text` = '{$_GET['access_token']}'");
	exit(json_encode($result));
}

$appid = $_GET['appid'];
$appsecret = $myauth->result_first("SELECT `appsecret` FROM `oauth_info` WHERE `appid` = '{$appid}'");
$getappsecret = my_decrypt($_GET['appsecret']);
if ($getappsecret !== $appsecret) die('APPSECRET错误！');
$origin = (isset($_GET['origin']) ? $_GET['origin'] : $_SERVER['HTTP_HOST']);

$appname = $myauth->result_first("SELECT `appname` FROM `oauth_info` WHERE `appid` = '{$appid}'");

if (isset($_POST['token'])) {
	$result = ajax([
		'url' => '?action=login',
		'method' => 'POST',
		'content' => json_encode([
			'type' => $_POST['type'],
			'username' => $_POST['username'],
			'password' => $_POST['password']
		])
	]);
	$result = json_decode($result, true);
	if ($result['uid'] > 0) {
		$access_token = sha1(rand(10000) . time());
		$myauth->query("INSERT INTO `oauth_tokens` (`token_text`, `token_appid`, `token_uid`) VALUES('{$access_token}', '{$appid}', '{$result['uid']}')");

		?>
		<script>
		window.opener.postMessage(JSON.stringify({
				access_token:"<?=$access_token?>"
			}),
			"http://<?=$origin?>"
		);
		window.close();
		</script>
		<?

		exit();
	}
	else if ($result['uid'] === 0) {
		setcookie('myauth_token', $result['token'], time() + 3600 * 10000, '/');
		jumpTo('?page=complete&relogin=1');
	}
	else {
		alert('验证失败：' . $result['msg'], $_SERVER['REQUEST_URI']);
	}
}

// 生成微信登录的加密串
$code = sha1(rand(10000) . "\t" . time());

?>
<? createHeader('第三方授权'); ?>
		<div class="tip tip-info">授权“<?=$appname?>”使用你的纸飞机账号登录</div>
		<div id="frame1" class="frame">
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">学号/工号</div>
				<div id="tab2" class="tab">论坛账号</div>
				<div id="tab3" class="tab">微信号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="type" value="ded">
						<div class="mui-form-group">
							<input type="text" class="mui-form-control" name="username" required>
							<label class="mui-form-label">学号/工号</label>
						</div>
						<div class="mui-form-group">
							<input type="password" class="mui-form-control" name="password" required>
							<input type="password" class="mui-form-control hidden">
							<label class="mui-form-label">密码</label>
						</div>
						<div class="form-footer">
							<button type="submit" class="mui-btn" data-mui-color="primary">登录</button>
						</div>
					</form>
				</div>
				<div id="group2" class="group">
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="type" value="dz">
						<div class="mui-form-group">
							<input type="text" class="mui-form-control" name="username" required>
							<label class="mui-form-label">论坛昵称</label>
						</div>
						<div class="mui-form-group">
							<input type="password" class="mui-form-control" name="password" required>
							<input type="password" class="mui-form-control hidden">
							<label class="mui-form-label">论坛密码</label>
						</div>
						<div class="form-footer">
							<button type="submit" class="mui-btn" data-mui-color="primary">登录</button>
						</div>
					</form>
				</div>
				<div id="group3" class="group">
					<div>
						<img id="wechat_qrcode" src="http://localhost/zfj/mytools/?tool=qrcode&text=wechat://<?=$code?>" alt="扫码登录">
						<div id="wechat_tip">* 请在公众号“南航纸飞机”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var code="<?=$code?>";
		var oauth=true;
		var origin="<?=$origin?>";
		function encrypt(form){
			form.password.value=my_encrypt(form.password.value,key);
		}
	</script>
	<script src="resources/js/wechat_query.js"></script>
<? createFooter();
