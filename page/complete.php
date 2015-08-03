<?

(!isset($_COOKIE['myauth_token'])) && die();

$arr = my_decrypt($_COOKIE['myauth_token']);
$arr = json_decode($arr, true);

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
	case 'new':
		switch ($_POST['target']) {
		case 'dz':
			$uid = uc_user_register($_POST['username'], $arr['password'], $_POST['email']);
			if ($uid > 0) {
				$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '{$arr['username']}')");
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
			break;
		}
		break;
	case 'bind':
		switch ($_POST['target']) {
		case 'dz':
			$result = ajax(array(
				'url' => '?action=login',
				'method' => 'POST',
				'content' => json_encode(array(
					'type' => 'dz',
					'username' => $_POST['username'],
					'password' => $_POST['password']
				))
			));
			$result = json_decode($result, true);
			if ($result['uid'] >= 0) {
				$uid = uc_get_user($_POST['username'])[0];
				$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '{$arr['username']}')
								ON DUPLICATE KEY UPDATE `auth_ded` = '{$arr['username']}'");
				make_login($uid);
				jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI']);
			}
			alert('验证失败！', $_SERVER['REQUEST_URI']);
			break;
		case 'ded':
			$result = ajax(array(
				'url' => '?action=login',
				'method' => 'POST',
				'content' => json_encode(array(
					'type' => 'ded',
					'username' => $_POST['username'],
					'password' => $_POST['password']
				))
			));
			$result = json_decode($result, true);
			if ($result['uid'] >= 0) {
				$number = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '{$_POST['username']}'");
				$number = intval($number);
				if ($number >= 2) {
					alert('该学号已绑定两个账号，无法继续绑定', $_SERVER['REQUEST_URI']);
				}
				$uid = uc_get_user($arr['username'])[0];
				$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '{$_POST['username']}')");
				make_login($uid);
				jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI']);
			}
			alert('验证失败！', $_SERVER['REQUEST_URI']);
			break;
		}
		break;
	}
}

?>
<? createHeader('完善信息'); ?>
		<h2>请完善您的信息</h2>
		<div id="frame1" class="frame">
<? if ($arr['from'] === 'dz') : ?>
			<div class="tabs v2">
				<div id="tab1" class="tab tab-current">绑定</div>
				<div id="tab2" class="tab">放弃</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<p>请验证你的教务处信息</p>
					<form action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<? echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="action" value="bind">
						<input type="hidden" name="target" value="ded">
						<div class="form-group">
							<div><span class="field">论坛昵称</span></div>
							<div><input type="text" value="<? echo $arr['username']; ?>" disabled></div>
						</div>
						<div class="form-group">
							<div><span class="field">学号/工号</span></div>
							<div><input type="text" name="username" class="area"></div>
						</div>
						<div class="form-group">
							<div><span class="field">教务处密码</span></div>
							<div><input type="password" name="password" class="area"></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<input type="button" value="教务处激活" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
				<div id="group2" class="group">
					<p>中断登录流程</p>
					<p>你的数据不会被插入到数据库中</p>
					<input type="button" onclick="window.location.href='?action=logout'" style="background:#D00" value="点此取消登录">
				</div>
			</div>
<? endif; ?>
<? if ($arr['from'] === 'ded') : ?>
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">注册</div>
				<div id="tab2" class="tab">绑定</div>
				<div id="tab3" class="tab">放弃</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<? echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="action" value="new">
						<input type="hidden" name="target" value="dz">
						<div class="form-group">
							<div><span class="field">学号/工号</span></div>
							<div><span class="area"><input type="text" value="<? echo $arr['username']; ?>" disabled></span></div>
						</div>
						<div class="form-group">
							<div><span class="field">论坛昵称</span></div>
							<div><input type="text" name="username" class="area" required></div>
						</div>
						<div class="form-group">
							<div><span class="field">邮箱</span></div>
							<div><input type="email" name="email" class="area" required></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer">
						<input type="button" value="完成注册" onclick="document.querySelector('#group1>form').submit()">
					</div>
				</div>
				<div id="group2" class="group">
					<p>请验证你的论坛信息</p>
					<form id="dzlogin" action="<? echo $_SERVER['REQUEST_URI']; ?>" method="post" class="center" autocomplete="off">
						<input type="hidden" name="token" value="<? echo base64_encode(sha1(rand(10000))) ?>">
						<input type="hidden" name="action" value="bind">
						<input type="hidden" name="target" value="dz">
						<div class="form-group">
							<div><span class="field">学号/工号</span></div>
							<div><span class="area"><input type="text" value="<? echo $arr['username']; ?>" disabled></span></div>
						</div>
						<div class="form-group">
							<div><span class="field">论坛昵称</span></div>
							<div><input type="text" name="username" class="area"></div>
						</div>
						<div class="form-group">
							<div><span class="field">密码</span></div>
							<div><input type="password" name="password" class="area"></div>
						</div>
						<input type="submit" class="hidden">
					</form>
					<div class="form-footer mb1">
						<input type="button" value="绑定论坛" onclick="document.querySelector('#group2>form').submit()">
					</div>
				</div>
				<div id="group3" class="group">
					<p>中断登录流程</p>
					<p>你的数据不会被插入到数据库中</p>
					<input type="button" onclick="window.location.href='?action=logout'" style="background:#D00" value="点此取消登录">
				</div>
			</div>
<? endif; ?>
		</div>
	</div>
<?

createFooter();