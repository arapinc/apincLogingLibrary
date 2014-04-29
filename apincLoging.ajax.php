<?php
require_once dirname(__FILE__)."/config.php";

class ApincAjaxLog {
	public static $dbc;

	public static function ajaxLog() {
		self::connentDb($_GET['dbname']);
		self::sendLogSql();
		self::closeDb();
	}

	public static function connentDb($dbName) {
		// connect mysql DB
		if (!isset($dbName))
			die('fail to set database name');

		self::$dbc = new mysqli("localhost", "root", DB_PASS, $dbName);
		if (!self::$dbc)
			die('fail to connect mysql'.self::$dbc->connect_error);
		if (!self::$dbc->set_charset("utf8"))
		    die('fail to change charset');
	}
	public static function closeDb() {
		// kill DB connection
		self::$dbc->close();
	}
	
	public static function setFirstLogSql() {
		// return sql when start channel to set address which get from google maps API
		$sql = "update accesslog set address=\"".$_GET['address']."\" where accessid=".$_GET['aid'].";";
		echo $sql;
		return $sql;
	}
	public static function setContentsLogSql() {
		// return log sql when open contents or close contents
		$sql = "insert into accesslog (uid, connectid, date, time, request, status) values (\"".$_GET['uid']."\", ".$_GET['cid'].", \"".date('Y-m-d')."\", \"".date('H:i:s')."\", \"".$_GET['request']."\", \"".$_GET['status']."\");";
		echo $sql;
		return $sql;
	}
	public static function sendLogSql() {
		if (isset($_GET['first']) && $_GET['first'] == 'true') {
			$sql = self::setFirstLogSql();
		} else if ((isset($_GET['first']) && $_GET['first'] == 'false') || !isset($_GET['first'])) {
			$sql = self::setContentsLogSql();
		}
		$result = self::$dbc->query($sql);
		if (!isset($result))
			die('fail to insert sql '.self::$dbc->error);
		print_r($result);
	}
}

ApincAjaxLog::ajaxLog();

?>