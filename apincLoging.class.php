<?php 
require_once dirname(__FILE__)."/config.php";

// apinc loging for mysql DB class

class ApincLog {
	public $dbc = "NULL";
	public $uid = "NULL";
	public $connectid = "NULL";
	public $coordinate = "NULL";
	public $accessid = "NULL";

	public function __construct($dbName) {
		$this->dbc = $this->connectDb($dbName);
		$this->getConnectId();
		$this->setGETAttributes();
		$this->setLog();
		$this->closeDb();
	}

	public function connectDb($dbName) {
		// connect database
		if (!isset($dbName))
			die('fail to set database name');

		$dbc = new mysqli("localhost", "root", DB_PASS, $dbName);
		if (!$dbc)
			die('fail to connect mysql' . $dbc->connect_error);
		if (!$dbc->set_charset('utf8'))
			die('fail to change charset');

		return $dbc;
	}
	public function closeDb() {
		$this->dbc->close();
	}


	public function getConnectId() {
		// get new connectid
		$sql = 'select MAX(connectid) from accesslog;';
		$result = $this->dbc->query($sql);
		if (!$result)
			die('fail to get new connectid');
		$row = $result->fetch_assoc();
		$this->connectid = $row['MAX(connectid)'] + 1;
	}

	public function setGETAttributes() {
		// set uid
		if ($_GET['uid'])
			$this->uid = $_GET['uid'];
		else
			$this->uid = "not from smartphone";

		// set coordinate
		if ($_GET['l'] == "0.0,0.0,0.0") {
			$this->coordinate = "NULL";
		} else if ($_GET['l']) {
			$location = split(",", $_GET[l]);
			$this->coordinate = $location[0].",".$location[1];
		} else {
			$this->coordinate = "NULL";
		}
	}

	public function setLogSql() {
		$sql = 'insert into accesslog (uid,connectid,date,time,coordinate,request,status) values ("' . $this->uid . '",' . $this->connectid . ',"' . date('Y-m-d') .'","'. date('H:i:s') .'","'. $this->coordinate . '","channel","start");';

		return $sql;
	}
	public function setLog() {
		$sql = $this->setLogSql();
		$result = $this->dbc->query($sql);
		if (!$result)
			die('fail to insert sql' . $this->dbc->error);

		$this->accessid = $this->dbc->insert_id;
	}

	public function putDataForJs() {
		echo '<script type="text/javascript">'."\n";
		echo "var userData = {};\n";
		$location = split(",", $_GET[l]);
		echo "userData.l = [".$location[0].", ".$location[1]."];\n";
		echo "userData.aid = ".$this->accessid.";\n";
		echo "userData.cid = ".$this->connectid.";\n";
		echo "userData.uid = \"".$this->uid."\";\n";
		echo "</script>\n";
	}
}

 ?>