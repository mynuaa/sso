<?

$code = file_get_contents('https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa04c7656484a07d2&redirect_uri=http%3A%2F%2Fmy.nuaa.edu.cn%2Fsso%2F%3Faction%3Ddump&response_type=code&scope=snsapi_base&state=my.nuaa.edu.cn/sso/?action=dump#wechat_redirect');
$code = preg_match('/code=.?&/', $code);
var_dump($code);
