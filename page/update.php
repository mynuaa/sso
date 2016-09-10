<?

(!isset($_COOKIE['myauth_uid'])) && die();

// $arr = my_decrypt($_COOKIE['myauth_token']);
// $arr = explode("\t", $arr);
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
	case 'bind':
		switch ($_POST['target']) {
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
			print_r($result['uid']);
			if ($result['uid'] >= 0) {
				$number = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '{$_POST['username']}'");
				$number = intval($number);
				if ($number >= 2) {
					alert('该学号/工号已绑定两个账号，无法继续绑定', $_SERVER['REQUEST_URI']);
				}
				$uid = my_decrypt($_COOKIE['myauth_uid']);
				$uid = json_decode($uid, true);
				$uid = $uid['uid'];
				$myauth->query("UPDATE `sso` SET `auth_ded` = '{$_POST['username']}' WHERE `auth_id` = {$uid}");

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
						<!-- <div class="mui-form-group">
							<input class="mui-form-control" type="text" value="<?=$arr[1]?>" disabled>
							<label class="mui-form-label">论坛昵称</label>
						</div> -->
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