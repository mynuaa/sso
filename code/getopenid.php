<?

if (!isset($_GET['code']))
	header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa04c7656484a07d2&redirect_uri=http%3A%2F%2Fmy.nuaa.edu.cn%2Fsso%2F%3Faction%3Dgetopenid&response_type=code&scope=snsapi_base&state=mynuaa#wechat_redirect");
else {
	$result = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxa04c7656484a07d2&secret=66fe85f09de7ce2fac6d11e075886686&code={$_GET['code']}&grant_type=authorization_code");
	$result = json_decode($result);
	var_dump($result);
}
