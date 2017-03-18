<?

class dbstuff {

	var $version = '';
	var $querynum = 0;
	var $link;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE) {
		$this->link = mysqli_connect($dbhost, $dbuser, $dbpw, $dbname, 3306);
		if (!$this->link) {
			$this->halt('Can not connect to MySQL server');
		}
		$this->link->set_charset('utf8');
	}

	function fetch_array($query) {
		$t = mysqli_fetch_array($query);
		return $t;
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {
		if(!($query = mysqli_query($this->link, $sql))) {
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysqli_affected_rows();
	}

	function error() {
		return mysqli_error();
	}

	function errno() {
		return mysqli_errno();
	}

	function result($query, $row) {
		$query = $this->fetch_array($query)[$row];
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
		return mysqli_insert_id();
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
