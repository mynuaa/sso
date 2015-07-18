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
		if (isset($_GET['inoauth'])) {
			$username = uc_get_user($uid[0], 1)[1];
			$access_token = base64_encode(uc_authcode(sha1(base64_encode($username) . rand(10000)) . "\t" . $uid[0], 'ENCODE', 'myauth'));
			$myauth->query("INSERT INTO `oauth_tokens` (`token_text`, `token_appid`, `token_uid`) VALUES('{$access_token}', '{$appid}', '{$uid[0]}')");
			?>
			<script>
			window.opener.postMessage(JSON.stringify({
					access_token:"<?=$access_token?>"
				}),
				"http://<?=(isset($_GET['origin']) ? $_GET['origin'] : $_SERVER['HTTP_HOST'])?>"
			);
			window.close();
			</script>
			<?
		}
		else {
			makeLogin($uid[0]);
			jumpTo($redirect_uri);
		}
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
							<? if (isset($_GET['inoauth'])): ?>
								<a href="/sso/?page=choose&uid=<?=$uid?>&code=<?=$code?>&inoauth">
							<? else: ?>
								<a href="/sso/?page=choose&uid=<?=$uid?>&code=<?=$code?>&redirect_uri=<?=$redirect_uri?>">
							<? endif; ?>
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