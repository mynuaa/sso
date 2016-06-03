<?

(!isset($_COOKIE['myauth_token'])) && die();

$arr = my_decrypt($_COOKIE['myauth_token']);
$arr = explode("\t", $arr);
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
	case 'new':
		switch ($_POST['target']) {
		case 'dz':
			$uid = uc_user_register($_POST['username'], $arr[2], $_POST['email']);
			if ($uid > 0) {
				$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '{$arr[1]}')");
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
				$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '{$arr[1]}')
								ON DUPLICATE KEY UPDATE `auth_ded` = '{$arr[1]}'");
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
					alert('该学号/工号已绑定两个账号，无法继续绑定', $_SERVER['REQUEST_URI']);
				}
				$uid = uc_get_user($arr[1])[0];
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
			<? if ($arr[0] === 'dz') : ?>
			<div class="tabs v2">
				<div id="tab1" class="tab tab-current">绑定</div>
				<div id="tab2" class="tab">放弃</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<p>请验证你的学号/工号信息</p>
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="action" value="bind">
						<input type="hidden" name="target" value="ded">
						<div class="mui-form-group">
							<input class="mui-form-control" type="text" value="<?=$arr[1]?>" disabled>
							<label class="mui-form-label">论坛昵称</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="text" name="username">
							<label class="mui-form-label">学号/工号</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="password" name="password">
							<input class="hidden" type="password">
							<label class="mui-form-label">密码</label>
						</div>
						<button type="submit" class="mui-btn" data-mui-color="primary">学号/工号激活</button>
					</form>
				</div>
				<div id="group2" class="group">
					<p>中断登录流程</p>
					<p>你的数据不会被插入到数据库中</p>
					<input type="button" onclick="window.location.href='?action=logout'" style="background:#D00" value="点此取消登录">
				</div>
			</div>
			<? endif; ?>
			<? if ($arr[0] === 'ded') : ?>
			<div class="tabs v3">
				<div id="tab1" class="tab tab-current">注册</div>
				<div id="tab2" class="tab">绑定</div>
				<div id="tab3" class="tab">放弃</div>
			</div>
			<div class="groups">
				<div id="group1" class="group group-current">
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="action" value="new">
						<input type="hidden" name="target" value="dz">
						<table>
							<div class="mui-form-group">
								<input class="mui-form-control" type="text" value="<?=$arr[1]?>" disabled>
								<label class="mui-form-label">学号/工号</label>
							</div>
							<div class="mui-form-group">
								<input class="mui-form-control" type="text" name="username" required check-valid="username">
								<label class="mui-form-label">论坛昵称</label>
							</div>
							<div class="mui-form-group">
								<input class="mui-form-control" type="email" name="email" required check-valid="email">
								<label class="mui-form-label">邮箱</label>
							</div>
							<button type="submit" class="mui-btn" data-mui-color="primary">完成注册</button>
						</table>
					</form>
				</div>
				<div id="group2" class="group">
					<p>请验证你的论坛信息</p>
					<form id="dzlogin" action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="encrypt(this)">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<input type="hidden" name="action" value="bind">
						<input type="hidden" name="target" value="dz">
						<table>
							<div class="mui-form-group">
								<input class="mui-form-control" type="text" value="<?=$arr[1]?>" disabled>
								<label class="mui-form-label">学号/工号</label>
							</div>
							<div class="mui-form-group">
								<input class="mui-form-control" type="text" name="username" required>
								<label class="mui-form-label">论坛昵称</label>
							</div>
							<div class="mui-form-group">
								<input class="mui-form-control" type="password" name="password" required>
								<input class="hidden" type="password">
								<label class="mui-form-label">密码</label>
							</div>
							<button type="submit" class="mui-btn" data-mui-color="primary">绑定论坛账号</button>
						</table>
					</form>
				</div>
				<div id="group3" class="group">
					<p>中断登录流程</p>
					<p>你的数据不会被插入到数据库中</p>
					<button class="mui-btn" data-mui-color="danger" onclick="window.location.href='?action=logout'">点此取消登录</button>
				</div>
			<? endif; ?>
			</div>
		</div>
	</div>
	<script>
		function encrypt(form){
			form.password.value=my_encrypt(form.password.value,key);
		}
	</script>
<?

createFooter();