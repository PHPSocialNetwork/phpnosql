<?php

if(!function_exists("_db")) {
	function _db($database = "mydb") {
		if(!isset(_noSQL_Static::$instances[$database])) {
			_noSQL_Static::$instances[$database] = new noSQLPDO($database);
		}
		return _noSQL_Static::$instances[$database];
	}
}

function _noSQL($database = "mydb") {
	if(!isset(_noSQL_Static::$instances[$database])) {
		_noSQL_Static::$instances[$database] = new noSQLPDO($database);
	}
	return _noSQL_Static::$instances[$database];
}

class _noSQL {
	function __construct($database) {
		return _noSQL($database);
	}
}

class _noSQL_Static {
	public static $instances;
}

require_once(dirname(__FILE__)."/_sources/".NOSQL_VERSION."/pdo.php");