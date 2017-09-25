<?

// 新生可以登录教务处之后此页面隐藏
die();
$redirect_uri = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI'];

if (isset($_POST['token'])) {
	foreach (array('username', 'password', 'passwordrep', 'email', 'intro') as $key)
		if (!isset($_POST[$key]) || $_POST[$key] === '')
			alert('请将信息填写完整！', $_SERVER['REQUEST_URI']);
	if ($_POST['password'] !== $_POST['passwordrep'])
		alert('两次填写的密码不一致！', $_SERVER['REQUEST_URI']);
	$uid = uc_user_register($_POST['username'], $_POST['password'], $_POST['email']);
	if ($uid > 0) {
		$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, 'FRESHMAN')");
		make_login($uid);
		unset($_COOKIE['myauth_token']);
		jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI']);
	}
	$msg = array(
		-1 => '用户名不合法',
		-2 => '包含不允许注册的词语',
		-3 => '用户名已经存在',
		-4 => 'Email格式有误',
		-5 => 'Email不允许注册',
		-6 => '该Email已经被注册'
	);
	alert('注册失败：' . $msg[$uid], $_SERVER['REQUEST_URI']);
}

if (isset($_COOKIE['myauth_uid'])) {
	$uid = getuid();
	$user = uc_get_user($uid, 1)[1];
}
else {
	$user = NULL;
}
?>
<? createHeader('新生注册'); ?>
		<div class="tip tip-info">注册成功后，请使用论坛账号登录，仅可访问新生版块。</div>
		<div class="tip tip-warning">本页面将在新生允许登录教务处后关闭，届时请重新登录，以获取更多权限。</div>
		<div id="frame1" class="frame">
		<? if ($user != NULL) : ?>
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
			<div class="groups">
				<div id="group1" class="group-current group_up">
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="type" value="dz">
						<div class="mui-form-group">
							<label class="mui-form-label">论坛昵称</label>
							<input type="text" name="username" class="mui-form-control" required autofocus check-valid="username">
						</div>
						<div class="mui-form-group">
							<label class="mui-form-label">论坛密码</label>
							<input type="password" name="password" class="mui-form-control" required>
						</div>
						<div class="mui-form-group">
							<label class="mui-form-label">确认密码</label>
							<input type="password" name="passwordrep" class="mui-form-control" required>
						</div>
						<div class="mui-form-group">
							<label class="mui-form-label">邮箱</label>
							<input type="email" name="email" class="mui-form-control" required check-valid="email">
						</div>
						<div class="mui-form-group">
							<label class="mui-form-label">验证消息</label>
							<input type="text" name="intro" class="mui-form-control" required placeholder="让我们认识一下你！">
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<input type="button" value="立即注册" onclick="document.querySelector('#group1>form').submit()" class="mui-btn" data-mui-color="primary">
					</div>
				</div>
			</div>
		<? endif; ?>
		</div>
	</div>
<?

createFooter();
