<?
// 此页面必须登录
isset($_COOKIE['myauth_uid']) || die();
$uid = explode("\t", uc_authcode($_COOKIE['myauth_uid'], 'DECODE', 'myauth'));
$uid = intval($uid[1]);
$user = uc_get_user($uid, 1)[1];

$sql = "SELECT `auth_wechat` FROM `sso` WHERE `auth_id` = $uid";
$result = $myauth->result_first($sql);
if ($result != NULL)
	$errormsg = '你的纸飞机账号已经绑定微信了哦:)';

// 生成微信绑定的加密串
$queryCode = base64_encode(uc_authcode($uid . "\t" . sha1(rand(10000) . "\t" . time()), 'ENCODE', 'myauth'));

?>
<? createHeader('微信绑定'); ?>
		<div id="frame1" class="frame">
			<div class="groups">
				<h3>你正在绑定的账号是：<?=$user?></h3>
				<div id="group3" class="group group-current">
				<? if ($errormsg == '') : ?>
					<div>
						<img id="wechat_qrcode" src="http://qr.liantu.com/api.php?text=wechatbind://<?=$queryCode?>" alt="扫码登录" style="width:200px;height:200px;border:2px solid;border-radius:0.5em;margin-bottom:0.5em">
						<div id="wechat_tip" style="margin:0 1em;font-size:0.9em;text-align:left">* 请在公众号“南航纸飞机”的菜单中找到“纸飞机→万能扫码”，并将手机摄像头对准上方二维码。</div>
					</div>
				<? else: ?>
					<h4><?=$errormsg?></h4>
					<input type="button" value="返回" onclick="history.go(-1)">
				<? endif; ?>
				</div>
			</div>
		</div>
	</div>
	<script>
		var queryCode="<?=$queryCode?>";
		var redirect_uri="<?=$redirect_uri?>";
		var oauth=false;
	</script>
	<script src="resources/js/wechat_query.js"></script>
<?

createFooter();