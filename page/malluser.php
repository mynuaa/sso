<?php

$redirect_uri = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI'];

if (isset($_POST['token'])) {
	foreach (array('username', 'password', 'passwordrep', 'email', 'intro') as $key)
		if (!isset($_POST[$key]) || $_POST[$key] === '')
			alert('请将信息填写完整！', $_SERVER['REQUEST_URI']);
	if ($_POST['password'] !== $_POST['passwordrep'])
		alert('两次填写的密码不一致！', $_SERVER['REQUEST_URI']);
	$uid = uc_user_register($_POST['username'], $_POST['password'], $_POST['email']);
	if ($uid > 0) {
		$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, 'MALLUSER')");
		makeLogin($uid, 'MALLUSER');
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
	$uid = explode("\t", uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth'));
	$uid = intval($uid[1]);
	$user = uc_get_user($uid, 1)[1];
}
else {
	$user = NULL;
}

?>
<?php createHeader('商家注册'); ?>
		<div class="tip tip-info">注册成功后，请使用论坛账号登录</div>
		<div id="frame1" class="frame">
		<?php if ($user != NULL) : ?>
			<h2>你好，<?php echo $user ?>。</h2>
			<input type="button" onclick="window.location.href='?action=logout'" value="退出登录">
		<?php else : ?>
			<div class="groups">
				<div id="group1" class="group-current">
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="type" value="dz">
						<div class="form-group">
							<div><span class="field">论坛昵称</span></div>
							<div><input type="text" name="username" class="area" required autofocus></div>
						</div>
						<div class="form-group">
							<div><span class="field">论坛密码</span></div>
							<div><input type="password" name="password" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">确认密码</span></div>
							<div><input type="password" name="passwordrep" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">邮箱</span></div>
							<div><input type="email" name="email" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">验证信息</span></div>
							<div><input type="text" name="intro" class="area" required placeholder="让我们认识一下你！"></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<div class="mb1">
							<input type="checkbox" name="like">
							<label for="like">喜欢纸飞机就点个赞吧</label>
						</div>
						<input type="button" value="立即注册" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
			</div>
		<?php endif; ?>
		</div>
	</div>
<?php createFooter(); ?>