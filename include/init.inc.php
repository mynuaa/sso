<?

require_once 'defines.inc.php';

// 设置数据库
require_once 'db_mysql.class.php';
$myauth = new dbstuff;
$myauth->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

require_once 'ded_verify.class.php';
require_once 'functions.inc.php';
