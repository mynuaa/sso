<?

/*
curl -H 'Host: ded.nuaa.edu.cn' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9' -H 'Referer: http://aao.nuaa.edu.cn/' --compressed 'http://ded.nuaa.edu.cn/NetEAn/user/jwc_login_jk1.asp?usr={$stuid}&pwd={$pwd}'

船新版本的登录方法
2018.5.22
 */
// 本科生登录
function usrverify($stuid, $password) {
	//$cookie = tempnam('/tmp', 'MYAUTH_');

	$curl = curl_init();

	/*
	curl_setopt_array($curl, [
		CURLOPT_NOBODY => true,
		CURLOPT_URL => 'http://' . DED_HOST . '/NetEAn/User/login.asp',
		CURLOPT_COOKIEJAR => $cookie,
		CURLOPT_RETURNTRANSFER => true,
	]);
	curl_exec($curl);
	curl_setopt_array($curl, [
		CURLOPT_POST => true,
		CURLOPT_URL => 'http://' . DED_HOST . '/NetEAn/User/check.asp',
		CURLOPT_POSTFIELDS => 'user=' . $stuid . '&pwd=' . $password,
		CURLOPT_REFERER => 'http://ded.nuaa.edu.cn/netean/user/login.asp',
		CURLOPT_HTTPHEADER => [
			'Origin: http://ded.nuaa.edu.cn',
			'Content-type: application/x-www-form-urlencoded'
		],
		CURLOPT_COOKIEFILE => $cookie
	]);
	*/

	$url = 'http://' . DED_HOST . "/NetEAn/user/jwc_login_jk1.asp?usr={$stuid}&pwd={$password}";
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_REFERER => 'http://aao.nuaa.edu.cn/',
		CURLOPT_HTTPHEADER => [
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9'
		]
	]);
	
	$response = curl_exec($curl);

	$success = strstr($response, 'switch (0){') != false;
	if ($success) {
		global $myauth;
		if (!$myauth->result_first("SELECT `name` FROM `sso` WHERE `auth_ded` = '{$stuid}'")) {
			curl_setopt_array($curl, [
				CURLOPT_URL => 'http://' . DED_HOST . '/netean/newpage/xsyh/title.asp',
				CURLOPT_COOKIEFILE => $cookie,
				CURLOPT_RETURNTRANSFER => true,
			]);
			$result = iconv('GB2312', 'UTF-8', curl_exec($curl));
			preg_match('/^.+\.(.+?)\).+$/s', $result, $arr);
			$myauth->query("UPDATE `sso` SET `name`= '{$arr[1]}' WHERE `auth_ded` = '{$stuid}'");
		}
	}
	curl_close($curl);
	unlink($cookie);
	return $success;
}
// 研究生登录
function gsmverify($gsmid, $password) {
	$gsmid = $gsmid;
	$password = $password;
	$prepare_curl = curl_init();
	curl_setopt_array($prepare_curl, [
		CURLOPT_URL => 'http://' . GSM_HOST . '/pyxx/login.aspx',
		CURLOPT_RETURNTRANSFER => 1,
	]);
	preg_match('/name="__VIEWSTATE" value=".+?"/', curl_exec($prepare_curl), $viewstate);
	$viewstate = substr($viewstate[0], 26);
	$viewstate = preg_replace('/"/', '', $viewstate);
	$viewstate = urlencode($viewstate);
	curl_close($prepare_curl);
	$x = intval(rand(60));
	$y = intval(rand(60));
	$post = "__VIEWSTATE={$viewstate}&_ctl0%3Atxtusername={$gsmid}&_ctl0%3AImageButton1.x={$x}&_ctl0%3AImageButton1.y={$y}&_ctl0%3Atxtpassword={$password}";
	$url = 'http://' . GSM_HOST . '/pyxx/login.aspx';
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_HTTPHEADER, [
			"Content-type: application/x-www-form-urlencoded",
			"Origin: http://gsmis.nuaa.edu.cn"
		],
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $post,
		CURLOPT_RETURNTRANSFER => 1,
	]);
	$response = curl_exec($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	return $http_code == 302 || preg_match('/您已超过学习期限/', $response);
}
// 教师登录
function hrverify($tid, $password) {
	$url = "http://net.nuaa.edu.cn/api/verifyUser.do?token=dd64533c961eb9d527a608f9cd13fb06&username={$tid}&password={$password}";
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => 1,
	]);
	$response = curl_exec($curl);
	curl_close($curl);
	$response = json_decode($response, true);
	return $response['status'] == 0;
}

//继续教育学生登入
function cceverify($stuid, $password) {
	$cookie = tempnam('/tmp', 'MYAUTH_');
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_NOBODY => true,
		CURLOPT_URL => 'http://' . CCE_HOST . '/NetEAn/User/login.asp',
		CURLOPT_COOKIEJAR => $cookie,
		CURLOPT_RETURNTRANSFER => true,
	]);
	curl_exec($curl);
	curl_setopt_array($curl, [
		CURLOPT_POST => true,
		CURLOPT_URL => 'http://' . CCE_HOST . '/netean/user/check.asp',
		CURLOPT_POSTFIELDS => 'user=' . $stuid . '&pwd=' . $password,
		CURLOPT_REFERER => 'http://cce.nuaa.edu.cn/netean/user/login.asp',
		CURLOPT_HTTPHEADER => [
			'Origin: http://cce.nuaa.edu.cn',
			'Content-type: application/x-www-form-urlencoded'
		],
		CURLOPT_COOKIEFILE => $cookie
	]);
	$response = curl_exec($curl);
	$success = strstr($response, 'switch (0){') != false;
	$failed = strstr($response, 'switch (19){') || strstr($response, 'switch (77){') || strstr($response, 'switch (0){') || strstr($response, 'switch (88){') || strstr($response, 'switch (99){');
	if ($success || !$faied) {
		global $myauth;
		if (!$myauth->result_first("SELECT `name` FROM `sso` WHERE `auth_ded` = '{$stuid}'")) {
			curl_setopt_array($curl, [
				CURLOPT_URL => 'http://' . CCE_HOST . '/netean/newpage/xsyh/title.asp',
				CURLOPT_COOKIEFILE => $cookie,
				CURLOPT_RETURNTRANSFER => true,
			]);
			$result = iconv('GB2312', 'UTF-8', curl_exec($curl));
			preg_match('/^.+\.(.+?)\).+$/s', $result, $arr);
			$myauth->query("UPDATE `sso` SET `name`= '{$arr[1]}' WHERE `auth_ded` = '{$stuid}'");
		}
	}
	curl_close($curl);
	unlink($cookie);
	return $success;
}

function dedverify($username, $password) {
	$username = urlencode($username);
	$password = urlencode($password);
	return (
		(preg_match("/(^7020|^LZ)/i", $username) && hrverify($username, $password)) ||
		(preg_match("/(^SX|^SY|^SZ|^BX|^BL)/i", $username) && gsmverify($username, $password)) ||
		(preg_match("/(^CZ)/i", $username) && cceverify($username, $password)) ||
		usrverify($username, $password)
	);
}
