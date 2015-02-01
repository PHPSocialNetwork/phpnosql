<?php


class noSQLPDO {


	var $connection = NULL;
	var $___configX = Null;

	function connect($config) {
		$this->___configX = $config;
	}

	function prepare($sql) {
		return $this->db()->prepare($sql);

	}



	function db() {
		if($this->connection == NULL) {
			$this->connection = new PDO('mysql:host='.$this->___configX['database']['hostname'].';dbname='.$this->___configX['database']['database'].';charset=utf8', $this->___configX['database']['username'], $this->___configX['database']['password'], array(PDO::ATTR_EMULATE_PREPARES => false,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

		}
		return $this->connection;
	}



	function lastInsertId() {

		return $this->db()->lastInsertId();

	}



	function begin() {

		$this->db()->beginTransaction();

	}



	function commit() {

		$this->db()->commit();

	}



	function rollback() {

		$this->db()->rollBack();

	}


}

