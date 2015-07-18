<?

$redirect_uri = isset($_GET['redirect_uri']) ? base64_decode($_GET['redirect_uri']) : $_SERVER['REQUEST_URI'];

(!isset($_GET['code']) || !isset($_GET['uid'])) && die();
$code = $_GET['code'];
$uid = explode(':', $_GET['uid']);

// 已选择一个账号，需要立即登录
if (count($uid) === 1) {
	$result = ajax(array(
		'url' => '?action=login',
		'method' => 'POST',
		'content' => json_encode(array(
			'type' => 'wechat',
			'queryCode' => $code,
			'action' => 'get'
		))
	));
	$result = json_decode($result, true);
	// 选择的uid在结果中
	if (in_array($uid[0], $result['uid'])) {
		makeLogin($uid[0]);
		jumpTo($redirect_uri);
	}
	exit();
}

// 列出全部用户
$users = array();
foreach ($uid as $value)
	$users[$value] = uc_get_user($value, 1)[1];

?>
<? createHeader('选择登录账号'); ?>
		<div id="frame1" class="frame">
			<div class="groups">
				<div id="group1" class="group group-current">
					<h3>请选择一个账号来登录</h3>
					<ul class="userlist">
						<? foreach ($users as $uid => $username): ?>
						<a href="/sso/?page=choose&uid=<?=$uid?>&code=<?=$code?>&redirect_uri=<?=$redirect_uri?>">
							<li title="以<?=$username?>的身份登录">
								<img src="/ucenter/avatar.php?uid=<?=$uid?>&size=middle" alt="<?=$username?>">
								<div><?=$username?></div>
							</li>
						</a>
						<? endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?

createFooter();