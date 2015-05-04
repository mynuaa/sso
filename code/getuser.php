<?php

isset($param['uid']) || die();

$result = array();

$sql = "SELECT * FROM `myauth` WHERE `auth_id` = {$param['uid']}";
$query = $db->query($sql);
while ($row = $db->fetch_array($query)) {
	$result []= $row;
}

echo json_encode($result);

?>