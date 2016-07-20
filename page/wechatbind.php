<?

$errormsg = '';

// 此页面必须登录
isset($_COOKIE['myauth_uid']) || $errormsg = '请先登录！';

// 获取用户信息
$uid = getuid();
$user = uc_get_user($uid, 1)[1];
if($uid) {
	$auth_ded = $myauth->result_first("SELECT `auth_ded` FROM `sso` WHERE `auth_id` = $uid");
	if (in_array($auth_ded, array('JUST4TEST', 'FRESHMAN'/*, 'MALLUSER'*/)))
		$errormsg = '你的验证信息不完整，无法绑定微信。';
	$sql = "SELECT `auth_wechat` FROM `sso` WHERE `auth_id` = $uid";
	$result = $myauth->result_first($sql);
	if ($result != null)
		$errormsg = '你的纸飞机账号已经绑定微信了哦:)';

	// 生成微信绑定的加密串
	$logincode = my_encrypt($uid, 'zfjoffice');
}

?>
<? createHeader('微信绑定'); ?>
		<div id="frame1" class="frame">
			<div class="groups">
			<?php
				if($uid) {
			?>
				<h3>你正在绑定的账号是：<?=$user?></h3>
			<?php	
				}
			?>
				<div id="group3" class="group group-current">
				<? if ($errormsg == '') : ?>
					<div>
						<img id="wechat_qrcode" src="http://my.nuaa.edu.cn/mytools/?tool=qrcode&text=wechatbind://<?=rawurlencode($logincode)?>" alt="扫码登录">
						<div id="wechat_tip">* 请在公众号“南航纸飞机”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					</div>
					<h2 id="bind-successful" style="display:none;text-align:center">绑定成功！三秒后页面关闭……</h2>
				<? else: ?>
					<h4><?=$errormsg?></h4>
					<button class="mui-btn" data-mui-color="primary" onclick="window.close()">关闭</button>
				<? endif; ?>
				</div>
			</div>
		</div>
	</div>
	<script>
		var uid=<?=$uid?>;
		var oauth=true;
	</script>
	<script src="resources/js/wechat_query.js"></script>
<?

createFooter();