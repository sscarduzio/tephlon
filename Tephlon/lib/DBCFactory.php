<?php
/**
 * DBCFactory
 *
 * @author Simone Scarduzio
 */

require_once(BASE_PATH . "/lib/adodb5/adodb.inc.php");
require_once(BASE_PATH . "/lib/MySQLConnector.php");
require_once(BASE_PATH . "/lib/SQLiteConnector.php");

class DBCFactory {
	
	public static $log;
	
	public static final function getConnector($ctx, $connString){
		
		$db = null;
		
		// SQLite db init
		if(strpos($connString, "sqlite") === 0){
			$db = &ADONewConnection('sqlite');
			$db->debug = true;
			$connString = substr($connString, strlen("sqlite://"), strlen($connString));
			$db->PConnect($connString);
			$connectorClass = "SQLiteConnector";
		}
		
		// MySQL db init
		else if(strpos($connString, "mysql") === 0){
			$db = &ADONewConnection($connectionString);
			$db->debug = true;
			$connectorClass = "MySQLConnector";
		}
		
		if(!is_null($db) && $db->IsConnected() ){
			self::$log->debug("Detected DB type: ".$db->databaseType);
			$dbc = new $connectorClass($ctx, $db);
		}
		else {
			self::$log->error("Could not connect to DB ($connString)" );
			return false;
		}
		
		return $dbc;
	}
}
