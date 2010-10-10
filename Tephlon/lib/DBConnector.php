<?php
require_once("adodb5/adodb.inc.php");
require_once("MySQLConnector.php");
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
		$this->stm['insert'] = "INSERT INTO $ctx (`key`, `payload`)".
            " VALUES (? ,?);";

		// Delete
		// ? = Key
		$this->stm['delete'] = "DELETE FROM $ctx WHERE `key` = ? ;";

		// Count
		$this->stm['count'] = "SELECT COUNT( * ) FROM $ctx";
        
		// Index
		$this->stm['index'] = "SELECT `key` FROM $ctx";
        
		// Drop
		$this->stm['drop'] = "DROP TABLE IF EXISTS $ctx";

		// Truncate (remove all rows)
		$this->stm['truncate'] = "TRUNCATE TABLE $ctx";

		// Select
		// ? = key
		$this->stm['select'] = "SELECT * FROM $ctx WHERE `key` = ?";

		// Update
		// ? = payload
		// ? = key
		$this->stm['update'] = "UPDATE $ctx SET `payload` = ? WHERE `key` = ?";

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
	function createTable($ctx){
		$create  =
        'CREATE TABLE IF NOT EXISTS '.$ctx.' ('.
        '`key` VARCHAR( 60 ) NOT NULL ,'.
        '`created` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,'.
        '`payload` TEXT NULL ,PRIMARY KEY (  `key` ));';

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
		return $res;
	}
	function index(){
		$this->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $this->db->Execute($this->stm['index']);
		//$rs = new ADORecordSet_mysql(1,1);
		
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

		$rs = $this->db->execute($this->stm['select'],$key);
		if($rs instanceof ADORecordSet){
			return $rs->fields;
		}
		return false;
	}

	function insert($record){
		// Construct data
		$data = array('key' => $record->getKey(),
		              'payload' => $record->getContent());

		// See if we have it, then update it
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $this->db->execute($this->stm['select'], $record->getKey());
		if($rs instanceof ADORecordSet){
			$this->db->execute($this->stm['update'],
			array($record->getContent(),
			$record->getKey()));
		}
		// Otherwise Insert it
		$rs = $this->db->execute($this->stm['insert'], $data);
		return $rs;
	}

}