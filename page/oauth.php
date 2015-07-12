<?

// TODO: 判定应用是否合法
$appid = $_GET['appid'];
$appsecret = $myauth->result_first("SELECT `appsecret` FROM `oauth_info` WHERE `appid` = '{$appid}'");
$timestamp = $_GET['timestamp'];
$validatecode = explode("\t", uc_authcode(base64_decode($_GET['authcode']), 'DECODE', 'myauth'));
if ($validatecode[0] != $appid ||
	$validatecode[1] != $appsecret ||
	$validatecode[2] != $timestamp) die('该应用未被授权！');

if (isset($_GET['access_token'])) {
	$result = array();
	if (isset($_GET['uid']))
		$result['uid'] = $myauth->result_first("SELECT `token_uid` FROM `oauth_tokens` WHERE `token_text` = '{$_GET['access_token']}'");
	exit(json_encode($result));
}

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
	if ($result['uid'] > 0) {
		$access_token = base64_encode(uc_authcode(sha1(base64_encode($_POST['username']) . rand(10000)) . "\t" . $result['uid'], 'ENCODE', 'myauth'));
		$myauth->query("INSERT INTO `oauth_tokens` (`token_text`, `token_appid`, `token_uid`) VALUES('{$access_token}', '{$appid}', '{$result['uid']}')");

		?>
		<script>
		window.opener.postMessage(JSON.stringify({
				access_token:"<?=$access_token?>"
			}),
			window.opener.location.origin
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
$queryCode = sha1(rand(10000) . "\t" . time());

?>
<? createHeader('用户登录'); ?>
		<div class="tip tip-info">最快捷的方法就是用学号/工号登录</div>
		<div id="frame1" class="frame">
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">学号/工号</div>
				<div id="tab2" class="tab">论坛账号</div>
				<div id="tab3" class="tab">微信号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off">
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
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off">
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
						<img id="wechat_qrcode" src="http://qr.liantu.com/api.php?text=<?=$queryCode?>" alt="扫码登录" style="width:200px;height:200px;border:2px solid;border-radius:0.5em;margin-bottom:0.5em">
						<div id="wechat_tip" style="margin:0 1em;font-size:0.9em;text-align:left">* 请在公众号“南航纸飞机”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var queryCode="wechat://<?=$queryCode?>";
	</script>
	<script src="resources/js/wechat_query.js"></script>
<? createFooter(); ?>
