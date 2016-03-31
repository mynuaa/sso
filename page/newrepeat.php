<?

(!isset($_COOKIE['myauth_uid'])) && die();

$uid = getuid();
$user = uc_get_user($uid, 1)[1];

$auth_ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = $uid");
$authcount = $myauth->result_first("SELECT COUNT(*) FROM `sso` WHERE `auth_ded` = '$auth_ded'");
$errormsg = '';
if (in_array($auth_ded, array('JUST4TEST', 'FRESHMAN', 'MALLUSER')))
	$errormsg = '你的验证信息不完整，无法注册新马甲。';
else if (intval($authcount) >= 2)
	$errormsg = '你已经有两个马甲了。';

if (isset($_POST['token'])) {
	if ($_POST['password'] != $_POST['reppassword'])
		alert('注册失败：两次输入的密码不一致');
	$uid = uc_user_register($_POST['username'], $_POST['password'], $_POST['email']);
	if ($uid > 0) {
		$myauth->query("INSERT INTO `sso` (`auth_id`, `auth_ded`) VALUES ($uid, '$auth_ded')");
		make_login($uid);
		jumpTo(isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : '/sso/?page=login');
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

?>
<? createHeader('设置新马甲'); ?>
		<h2>设置新马甲</h2>
		<div id="frame1" class="frame">
			<div class="groups">
				<div id="group1" class="group group-current">
				<? if ($errormsg == '') : ?>
					<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="center" autocomplete="off" onsubmit="formCheck()">
						<input type="hidden" name="token" value="<?=base64_encode(sha1(rand(10000)))?>">
						<div class="mui-form-group">
							<input class="mui-form-control" type="text" name="username" class="area" required check-valid="username">
							<label class="mui-form-label">论坛昵称</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="email" name="email" class="area" required check-valid="email">
							<label class="mui-form-label">邮箱</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="password" name="password" class="area" required>
							<label class="mui-form-label">密码</label>
						</div>
						<div class="mui-form-group">
							<input class="mui-form-control" type="password" name="reppassword" class="area" required>
							<label class="mui-form-label">重复密码</label>
						</div>
					</form>
					<button class="mui-btn" data-mui-color="primary" onclick="document.querySelector('#group1>form').submit()">完成注册</button>
				<? else : ?>
					<h4><?=$errormsg?></h4>
					<button class="mui-btn" data-mui-color="primary" onclick="history.go(-1)">返回</button>
				<? endif; ?>
				</div>
			</div>
		</div>
	</div>
	<script>
	function formCheck(){
		if(document.querySelector("[name=password]").value==document.querySelector("[name=reppassword]").value)return true;
		alert("注册失败：两次输入的密码不一致");
		return false;
	}
	</script>
<?

createFooter();