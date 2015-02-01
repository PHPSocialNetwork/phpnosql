<?php



class noSQLPDO_Model {

	var $tableName = "";
	var $tmpTable;
	var $caching = false;
	var $cachetype = "auto";
	var $cachetime = 300;
	var $connection = "";

	function __construct() {
		if($this->connection == "") {

		}
	}

	function setupConnection($config) {
		$str = md5(json_encode($config));
		if(!isset(myPDO::$tmp_connections[$str])) {
			myPDO::$tmp_connections[$str] = new MySubPDO();
			$this->connection =  myPDO::$tmp_connections[$str];
		} else {
			$this->connection =  myPDO::$tmp_connections[$str];
		}

		$this->connection->connect($config);
	}

	function cache($time = 300, $type = "auto") {
		$this->caching = true;
		$this->cachetime = $time;
		$this->cachetype = $type;
	}

	function _clearCache() {
		$this->caching = false;
		$this->cachetime = 300;
		$this->cachetype = "auto";
	}

	function _keyCache($query, $data = array()) {
		$key = md5($query.json_encode($data));
		return $key;
	}

	function createTmpTable($tableName) {
		if(!isset($this->tmpTable[$tableName])) {
			$this->tmpTable[$tableName] = new noSQLPDO_Model();
			$this->tmpTable[$tableName]->tableName = $tableName;
			return $this->tmpTable[$tableName];
		} else {
			return $this->tmpTable[$tableName];
		}
		// hack tricks for phpStorm
		return new noSQLPDO_Model();
	}

	function replace($data = array(), $where = array()) {
		$row = $this->getRow($where);
		if(!empty($row)) {
			foreach($row as $key=>$value) {
				$this->update($data, $where, 1);
				return $row[$key];
			}
		} else {
			$data = array_merge($data, $where);
			$id = $this->insert($data);
		}

		return $id;
	}




	function getRow($where = array(), $order = "") {

		/*

		 * Example $where = array( "user_id" => 1, "email" => "khoa");

		 * ORDER = "user_id asc" or "user_id DESC"

		 * return array as column=>value

		 */

		$exe = array();

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`=:".$col." ";

		}

		$wherestr = substr($wherestr,4);



		if($order !="") {

			$order = " ORDER BY ".$order;

		}



		$sql = "SELECT * FROM ".$this->tableName." WHERE ".$wherestr." ".$order." LIMIT 1";

		//  echo $sql;
		if($this->connection == "") {
			$stm = MyPDO::prepare($sql);
		} else {
			$stm = $this->connection->prepare($sql);
		}


		$stm->execute($exe);

		$row = $stm->fetch(PDO::FETCH_ASSOC);

		return $row;



	}

	function isExisting($where = array()) {
		if($this->count($where) > 0) {
			return true;
		} else {
			return false;
		}
	}



	function getRows($where = array(), $order = "", $limit = "") {

		/*

		 * Example $where = array( "user_id" => 1, "email" => "khoa");

		 * ORDER = "user_id asc" or "user_id DESC"

		 * return array for foreach

		 */

		$exe = array();

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`= :".$col." ";

		}

		$wherestr = substr($wherestr,4);

		if($wherestr!="") {

			$wherestr=" WHERE  ".$wherestr;

		}



		if($limit != "") {

			$limit = " LIMIT ".$limit;

		}



		if($order !="") {

			$order = " ORDER BY ".$order;

		}



		$sql = "SELECT * FROM ".$this->tableName." ".$wherestr." ".$order." ".$limit ;



		if($this->connection == "") {
			$stm = MyPDO::prepare($sql);
		} else {
			$stm = $this->connection->prepare($sql);
		}

		$stm->execute($exe);

		$rows = $stm->fetchAll(PDO::FETCH_ASSOC);

		return $rows;



	}





	function sum($column, $where = array(),  $order = "", $limit = "") {

		/*

		 * Example $where = array( "user_id" => 1, "email" => "khoa");

		 * ORDER = "user_id asc" or "user_id DESC"

		 * return array for foreach

		 */

		$exe = array(



		);

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`= :".$col." ";

		}

		$wherestr = substr($wherestr,4);

		if($wherestr!="") {

			$wherestr=" WHERE  ".$wherestr;

		}



		if($limit != "") {

			$limit = " LIMIT ".$limit;

		}



		if($order !="") {

			$order = " ORDER BY ".$order;

		}



		$sql = "SELECT SUM(`".$column."`) as `total` FROM ".$this->tableName." ".$wherestr." ".$order." ".$limit ;



		//  echo "<br><br>".$sql."<br><br>";


		if($this->connection == "") {
			$stm = MyPDO::prepare($sql);
		} else {
			$stm = $this->connection->prepare($sql);
		}


		$stm->execute($exe);

		$rows = $stm->fetch(PDO::FETCH_ASSOC);

		return $rows['total'];



	}



	function count($where = array()) {

		/*

		 * Example $where = array( "user_id" => 1, "email" => "khoa");

		 * ORDER = "user_id asc" or "user_id DESC"

		 * return array for foreach

		 */

		$first = key($where);

		$exe = array(
			":mykey"    =>  $first
		);

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`= :".$col." ";

		}
		$wherestr = substr($wherestr,4);

		if($wherestr!="") {

			$wherestr=" WHERE  ".$wherestr;

		}

		$query = "SELECT COUNT(:mykey) as `total` FROM  ".$this->tableName." ".$wherestr;
		if($this->connection == "") {
			$stm = MyPDO::prepare($query);
		} else {
			$stm = $this->connection->prepare($query);
		}

		$stm->execute($exe);
		$row = $stm->fetch(PDO::FETCH_ASSOC);
		return $row['total'];



	}



	function deleteRow($where = array()) {

		/*

		* Example $where = array( "user_id" => 1, "email" => "khoa");



		*/

		$exe = array();

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`=:".$col." ";

		}

		$wherestr = substr($wherestr,4);



		$sql = "DELETE FROM ".$this->tableName." WHERE ".$wherestr." LIMIT 1";
		if($this->connection == "") {
			$stm = MyPDO::prepare($sql);
		} else {
			$stm = $this->connection->prepare($sql);
		}


		$stm->execute($exe);



	}



	function deleteRows($where = array(), $limit = "") {

		/*

		* Example $where = array( "user_id" => 1, "email" => "khoa");



		*/

		$exe = array();

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":".$col] = $value;

			$wherestr .= "AND  `".$col."`=:".$col." ";

		}

		$wherestr = substr($wherestr,4);



		if($limit != "") {

			$limit = " LIMIT ".$limit;

		}



		$sql = "DELETE FROM ".$this->tableName." WHERE ".$wherestr." ".$limit ;
		if($this->connection == "") {
			$stm = MyPDO::prepare($sql);
		} else {
			$stm = $this->connection->prepare($sql);
		}


		$stm->execute($exe);



	}



	function insert($data = array()) {

		/*

		 * Example $data = aarray(

		 *      "name"  => "Khoa",

		 *      "email" =>  "khoaofgod@gmail.com",

		 * );

		 * return 0 or last Inserted ID

		 */

		$exe = array();

		$cols = "";

		$values = "";

		foreach($data as $col=>$value) {

			$exe[":".$col] = $value;

			$cols .= ",`".$col."`";

			$values .= ", :".$col;

		}

		$cols = substr($cols,1);

		$values = substr($values,1);



		$sql = "INSERT INTO ".$this->tableName." (".$cols.") VALUES (".$values.")";

		try {


			if($this->connection == "") {
				$stm = MyPDO::prepare($sql);
			} else {
				$stm = $this->connection->prepare($sql);
			}


			$stm->execute($exe);





		} catch (PDOException $e) {
			$this->catchException($e);
			return 0;

		}

		if($this->connection == "")    {
			return MyPDO::lastInsertId();
		} else {
			return $this->connection->lastInsertId();
		}




	}





	function update($data = array(), $where = array(), $limit = "") {

		/*

		 * Example $data = aarray(

		 *      "name"  => "Khoa",

		 *      "email" =>  "khoaofgod@gmail.com",

		 * );

		 * return 0 or last Inserted ID

		 */





		$exe = array();

		$wherestr = "";

		foreach($where as $col => $value) {

			$exe[":W".$col] = $value;
			$wherestr .= "AND  `".$col."`=:W".$col." ";

		}

		$wherestr = substr($wherestr,4);

		if($wherestr!="") {

			$wherestr= " WHERE ".$wherestr." ";

		}



		$cols = "";



		foreach($data as $col=>$value) {

			$exe[":".$col] = $value;

			$cols .= ", `".$col."`=:".$col." ";



		}

		$cols = substr($cols,1);



		if($limit!="") {

			$limit = " LIMIT ".$limit;

		}



		$sql = "UPDATE `".$this->tableName."` SET ".$cols." ".$wherestr." ".$limit;



		try {
			if($this->connection == "") {
				$stm = MyPDO::prepare($sql);
			} else {
				$stm = $this->connection->prepare($sql);
			}


			$stm->execute($exe);

		} catch (PDOException $e) {

			echo $sql;

			$this->catchException($e);

			return false;

		}

		return true;



	}





	function catchException($e) {

		echo "Problem!";



		echo "<pre>";

		print_r($e);

		echo "</pre>";

		exit;

	}



	function query($query, $data = array()) {

		$sql = $query;

		try {
			if($this->connection == "") {
				$stm = MyPdo::prepare($sql);
			}    else {
				$stm = $this->connection->prepare($sql);
			}




			$stm->execute($data );





		} catch(PDOException $e) {



			echo $sql;

			$this->catchException($e);

			return false;

		}

		return true;

	}



	function fetch($query, $data = array()) {
		$sql = $query;
		if($this->caching == true) {

		}

		try {
			if($this->connection == "") {
				$stm = MyPdo::prepare($sql);
			} else {
				$stm = $this->connection->prepare($sql);
			}


			$stm->execute($data);

			$row = $stm->fetch(PDO::FETCH_ASSOC);

			return $row;



		} catch(PDOException $e) {

			echo $sql;

			$this->catchException($e);



		}

		return array();

	}





	function fetchAll($query, $data = array()) {

		$sql = $query;

		try {

			if($this->connection == "") {
				$stm = MyPdo::prepare($sql);
			} else {
				$stm = $this->connection->prepare($sql);
			}

			$stm->execute($data);

			$row = $stm->fetchAll(PDO::FETCH_ASSOC);

			return $row;

		} catch(PDOException $e) {

			echo $sql;

			$this->catchException($e);

		}

		return array();

	}



	function __toString() {

		return $this->tableName;

	}

	function setTable($tableName) {
		$this->tableName = $tableName;
		return $this;
	}





}