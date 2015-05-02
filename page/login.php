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
		alert('验证失败：用户名或密码错误', '?page=login');
		break;
	case 0:
		$_SESSION['myauth_token'] = $result['token'];
		jumpTo('?page=complete&redirect_uri=' . (isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : base64_encode($_SERVER['REQUEST_URI'])));
		break;
	default:
		$_SESSION['myauth_uid'] = $result['uid'];
		jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI']);
		break;
	}
}

?>
<?php createHeader(); ?>
		<div id="frame1" class="frame">
		<?php if (isset($_SESSION['myauth_uid'])) : ?>
			<h2>你好，<?php echo uc_get_user($_SESSION['myauth_uid'], 1)[1]; ?>。</h2>
			<input type="button" onclick="window.location.href='?action=logout'" value="退出登录">
		<?php else : ?>
			<div class="tabs v2">
				<div id="tab1" class="tab tab-current">论坛账号</div>
				<div id="tab2" class="tab">学号</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo base64_encode(md5(rand(10000))) ?>">
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
						<input type="button" value="登录" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
				<div id="group2" class="group">
					<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo base64_encode(md5(rand(10000))) ?>">
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
						<input type="button" value="登录" onclick="document.querySelector('#group2>form').submit()">
					</div>
				</div>
			</div>
		<?php endif; ?>
		</div>
	</div>
<?php createFooter(); ?>