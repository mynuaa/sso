<?

// 若未设置返回链接则默认到论坛
$redirect_uri = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : '/';

// 从表单发过来的信息（不包括微信登录）
if (isset($_POST['token'])) {
	if($_POST['type'] == 'ded'){//教务处登录需要验证码
		if(!isset($_POST['code'])){
			alert('验证码错误', $_SERVER['REQUEST_URI']);
		}
		require_once SSO_ROOT . '/dxcode/CaptchaClient.php';
		$appId = "069ae57274e54291f373478057e1d796";
		$appSecret = "b684a38e770f263dd1e306b26937363c";
		$client = new \CaptchaClient($appId,$appSecret);
		$client->setTimeOut(2);
	
		$response = $client->verifyToken($_POST['code']);
		if($response->result != true){
			alert('验证码错误', $_SERVER['REQUEST_URI']);
		}

	}
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
	switch ($result['uid']) {
	case -1:
		alert('验证失败：' . $result['msg'], $_SERVER['REQUEST_URI']);
		break;
	case 0:
		setcookie('myauth_token', $result['token'], time() + 3600 * 10000, '/');
		jumpTo('?page=complete&redirect_uri=' . (isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : base64_encode($_SERVER['REQUEST_URI'])));
		break;
	default:
		make_login($result['uid']);
		jumpTo($redirect_uri);
		break;
	}
}

if (isset($_COOKIE['myauth_uid'])) {
	$uid = getuid();
	$user = uc_get_user($uid, 1)[1];
}
else {
	$user = null;
}

// 生成微信登录的加密串
$code = sha1(rand(10000) . "\t" . time());

?>
<? createHeader('用户登录'); ?>
		<div id="frame1" class="frame">
		<? if ($user != null) : ?>
			<h2>你好，<?=$user?>。</h2>
			<div>
				<h3>点击访问
					<a href="http://my.nuaa.edu.cn/" data-mui-color="primary" class="a_link">纸飞机论坛</a>
					<a href="http://my.nuaa.edu.cn/mall/" data-mui-color="primary" class="a_link">南航mall</a>
					<a href="http://my.nuaa.edu.cn/sso/?page=wechatbind" target="_banket" data-mui-color="primary" class="a_link">微信绑定</a>
					<a href="http://my.nuaa.edu.cn/xiaohongmao" data-mui-color="primary" class="a_link">小红帽</a>
				</h3>
			</div>
			<button class="mui-btn" data-mui-color="primary" onclick="window.location.href='?action=logout'">退出登录</button>
		<? else : ?>
			<!-- <div><h3><a href="/sso/?page=freshman" data-mui-color="primary" class="new-man">2017新生请点击这里注册</a></h3></div> -->
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">学号/工号</div>
				<div id="tab2" class="tab">论坛账号</div>
				<div id="tab3" class="tab">微信号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<div class="tip tip-info">使用你的学号/工号登录或注册。</div>
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="return encrypt(this, true)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="type" value="ded">
						<div class="mui-form-group">
							<input class="mui-form-control" type="text" name="username" required>
							<label class="mui-form-label">学号/工号</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="password" name="password" required>
							<input class="mui-form-control hidden" type="password">
							<label class="mui-form-label">密码</label>
						</div>
						<input class="mui-form-control hidden" type="text" name="code" id="codeI" >
						<div id="captchaBox" class="mui-form-group"></div>
						<button type="submit" class="mui-btn" data-mui-color="primary">登录</button>
					</form>
				</div>
				<div id="group2" class="group">
					<div class="tip tip-info">使用你在纸飞机的昵称和密码登录。</div>
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="type" value="dz">
							<div class="mui-form-group">
								<input class="mui-form-control" type="text" name="username" required>
								<label class="mui-form-label">论坛昵称</label>
							</div>
							<div class="mui-form-group">
								<input class="mui-form-control" type="password" name="password" required>
								<input class="mui-form-control hidden" type="password">
								<label class="mui-form-label">论坛密码</label>
							</div>
							<button type="submit" class="mui-btn" data-mui-color="primary">登录</button>
						</table>
					</form>
				</div>
				<div id="group3" class="group">
					<div class="tip tip-info">点击公众号“南航纸飞机”菜单中的“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					<img id="wechat_qrcode" src="http://my.nuaa.edu.cn/mytools/?tool=qrcode&text=wechat://<?=$code?>" alt="扫码登录">
				</div>
			</div>
		<? endif; ?>
		</div>
	</div>
	<script>
		var code="<?=$code?>";
		var redirect_uri="<?=$redirect_uri?>";
		var bredirect_uri="<?=base64_encode($redirect_uri)?>";
		var oauth=false;
		function encrypt(form, valid = false){
			if(valid && form.code.value.length < 2){
				alert('请拖动验证码验证身份~')
				return false
			}
			form.password.value=my_encrypt(form.password.value,key);
		}
	</script>
	<script src="resources/js/wechat_query.js"></script>
	<script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js"></script>
	<script>
		function dxCap(){
			console.log(6)
			var Captcha = _dx.Captcha(document.getElementById('captchaBox'), {
				appId: '069ae57274e54291f373478057e1d796',
				style: 'inline',
				inlineFloatPosition: 'up',
				success: function(token){
					document.getElementById('codeI').value = token
				},
				fail: function(e){
					console.log(e)
				}
			})
		}
		window.onload = dxCap()
	</script>
<?

createFooter();
