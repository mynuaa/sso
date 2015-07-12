<?

isset($param['uid']) || die();

$result = array();

$sql = "SELECT * FROM `sso` WHERE `auth_id` = {$param['uid']}";
$query = $myauth->query($sql);
while ($row = $myauth->fetch_array($query)) {
	$result []= $row;
}

echo json_encode($result);
