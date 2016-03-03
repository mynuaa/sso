<?

// 本科生登录
function usrverify($stuid, $password) {
	$url = "http://ded.nuaa.edu.cn/NetEAn/User/check.asp";
	$post = "user=" . $stuid . "&pwd=" . $password;
	$cookie = tempnam('/tmp', 'MYAUTH_');
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $post,
		CURLOPT_COOKIEJAR => $cookie,
		CURLOPT_RETURNTRANSFER => 1,
	]);
	curl_exec($curl);
	curl_setopt_array($curl, [
		CURLOPT_COOKIEFILE => $cookie,
		CURLOPT_REFERER => 'http://ded.nuaa.edu.cn'
	]);
	$response = curl_exec($curl);
	curl_close($curl);
	return (strstr($response, 'switch (0){') != false);
}
// 研究生登录
function gsmverify($gsmid, $password) {
	$gsmid = $gsmid;
	$password = $password;
	$prepare_curl = curl_init();
	curl_setopt_array($prepare_curl, [
		CURLOPT_URL => "http://gsmis.nuaa.edu.cn/nuaapyxx/login.aspx",
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
	$url = "http://gsmis.nuaa.edu.cn/nuaapyxx/login.aspx";
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_HTTPHEADER, array(
			"Content-type: application/x-www-form-urlencoded",
			"Origin: http://gsmis.nuaa.edu.cn"
		),
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $post,
		CURLOPT_RETURNTRANSFER => 1,
	]);
	$response = curl_exec($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	return $http_code == 302;
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

function dedverify($username, $password) {
	$username = urlencode($username);
	$password = urlencode($password);
	return (
		(preg_match("/(^7020|^LZ)/i", $username) && hrverify($username, $password)) ||
		(preg_match("/(^SX|^SY|^SZ|^BX)/i", $username) && gsmverify($username, $password)) ||
		usrverify($username, $password)
	);
}
