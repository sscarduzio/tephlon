<?php
require_once(BASE_PATH . "/lib/adodb5/adodb.inc.php");
require_once(BASE_PATH . "/lib/MySQLConnector.php");

/**
 * Generic SQL DB Connector
 * @author Simone
 *
 */
abstract class DBConnector {
	protected $stm = array();
	protected $db = null;

	protected function createStatements($ctx){

		$this->stm['before_create'] = '';
		// Insert
		// ? = Key
		// ? = Value
		// ? = willExpireAt
		$this->stm['insert'] = "INSERT INTO `$ctx` (`key`,`willExpireAt`,`content`, `lastModified`)".
            " VALUES (? ,?, ?, ?);";

		// Delete
		// ? = Key
		$this->stm['delete'] = "DELETE FROM `$ctx` WHERE `key` = ? ;";

		// Count
		$this->stm['count'] = "SELECT COUNT( * ) FROM `$ctx`";

		// Index
		$this->stm['index'] = "SELECT `key` FROM `$ctx`";

		// Drop
		$this->stm['drop'] = "DROP TABLE IF EXISTS `$ctx`";

		// Truncate (remove all rows)
		$this->stm['truncate'] = "TRUNCATE TABLE `$ctx`";

		// Select
		// ? = key
		// ? = now
		$this->stm['select'] = "SELECT * FROM `$ctx` WHERE `key` = ? ;";

		// Update
		// ? = content
		// ? = key
		$this->stm['update'] = "UPDATE `$ctx` SET `content` = ? , `willExpireAt`=? WHERE `key` = ? ;";

		// Remove stale records
		$this->stm['purge'] = "DELETE FROM `$ctx` WHERE `willExpireAt` < ? AND `willExpireAt` <> 0 ";
		// Hook for subclasses to modify the uncompiled statements
		$this->overrideStatements();

		// Batch compile statements
		foreach($this->stm as $name => $str){
			if($this->contains($str, '?')){
				$this->stm[$name] = $this->db->Prepare($str);
			}
		}
	}
	private function contains($string, $word){
		$x = stristr($string, $word);
		if ($x){
			return true;
		}
		return false;
	}
	// Override this
	abstract function overrideStatements();

	public static function getConnector($ctx, $connectionString){
		$db = &ADONewConnection($connectionString);
		$db->debug = true;
		if(!is_null($db) && $db->IsConnected() ){
			dlog("Detected DB type: ".$db->databaseType, DEBUG);
			switch ($db->databaseType) {
				case 'mysql' : $dbc =  new MySQLConnector($ctx, $db);
				break;
				//case 'oracle' : return new MySQLConnector($db);
			}
			return $dbc;
		}
		dlog("Could not connect to DB ($connStr)", ERROR );
		return false;
			
	}
	protected function __construct($ctx, $db){
		dlog(get_class().": initializing.", INFO);
		$this->db = $db;
		$this->createStatements($ctx);
	}

	/*************************************
	 * Wrap all sql functions
	 ************************************/

	function createTable($ctx){
		$create  =
        'CREATE TABLE IF NOT EXISTS `'.$ctx.'` ('.
        '`key` VARCHAR( 60 ) NOT NULL ,'.
        '`willExpireAt` int NOT NULL DEFAULT 0 ,'.
		'`lastModified` int NOT NULL DEFAULT 0, '.
        '`content` TEXT NULL ,PRIMARY KEY (  `key` ));';

		if(strlen($this->stm['before_create']) > 0 ){
			$this->db->Execute($this->stm['before_create'], array()) or die();
		}
		$dict = NewDataDictionary($this->db);
		$x = $dict->ExecuteSQLArray(array($create)) or die();
		return $x;
	}
	function delete($key){
		$res = $this->db->Execute($this->stm['delete'], array($key));
		return $res instanceof ADORecordSet_empty;
	}
	function count(){
		$res = $this->db->Execute($this->stm['count']);
		if($res instanceof ADORecordSet){
			return intval($res->fields[0]);
		}
		return null;
	}
	function index(){
		$this->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $this->db->Execute($this->stm['index']);
		return ($rs instanceof ADORecordSet) && is_array($rs->fields)
		? $rs->getAll()
		: array();
	}
	function drop(){
		$res = $this->db->Execute($this->stm['drop']);
		return $res instanceof ADORecordSet;
	}
	function  truncate(){
		$res = $this->db->Execute($this->stm['truncate']);
		return $res instanceof ADORecordSet_empty;
	}
	function select($key){
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$rs = $this->db->execute($this->stm['select'],array($key));
		if($rs instanceof ADORecordSet){
			return $rs->fields;
		}
		return false;
	}
	// Delete all record whose willExpireAt != 0 and willExpireAt < time()
	function purge(){
		$this->db->execute($this->stm['purge'],time());
		if($this->count() === 0){
			$this->drop();
		}
	}

	function insert($data){
		$rs = $this->db->execute($this->stm['insert'], $data);
		return $rs instanceof ADORecordSet_empty;
	}

}