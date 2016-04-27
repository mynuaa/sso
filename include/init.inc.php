<?

// 设置数据库
require_once 'db_mysql.class.php';
$myauth = new dbstuff;
$myauth->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

require_once 'ded_verify.class.php';
require_once 'functions.inc.php';
