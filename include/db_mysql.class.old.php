<?

class dbstuff {

	var $version = '';
	var $querynum = 0;
	var $link;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE) {

		$func = empty($pconnect) ? 'mysqli_connect' : 'mysqli_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw)) {
			$halt && $this->halt('Can not connect to mysqli server');
		} else {
			if($this->version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysqli_query("SET $serverset", $this->link);
			}
			$dbname && @mysqli_select_db($dbname, $this->link);
		}

	}

	function select_db($dbname) {
		return mysqli_select_db($dbname, $this->link);
	}

	function fetch_array($query, $result_type = mysqli_ASSOC) {
		return mysqli_fetch_array($query, $result_type);
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}
	function query($sql, $type = '') {
		global $debug, $discuz_starttime, $sqldebug, $sqlspenttimes;

		$func = $type == 'UNBUFFERED' && @function_exists('mysqli_unbuffered_query') ?
			'mysqli_unbuffered_query' : 'mysqli_query';
		if(!($query = $func($sql, $this->link))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->close();
				require './config.inc.php';
				$this->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
				$this->query($sql, 'RETRY'.$type);
			} elseif($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt('mysqli Query Error', $sql);
			}
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysqli_affected_rows($this->link);
	}

	function error() {
		return (($this->link) ? mysqli_error($this->link) : mysqli_error());
	}

	function errno() {
		return intval(($this->link) ? mysqli_errno($this->link) : mysqli_errno());
	}

	function result($query, $row) {
		$query = @mysqli_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysqli_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysqli_num_fields($query);
	}

	function free_result($query) {
		return mysqli_free_result($query);
	}

	function insert_id() {
		return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysqli_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysqli_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysqli_get_server_info($this->link);
		}
		return $this->version;
	}

	function close() {
		return mysqli_close($this->link);
	}

	function halt($message = '', $sql = '') {
		echo 'SQL Error:<br />'.$message.'<br />'.$sql;
	}
}
