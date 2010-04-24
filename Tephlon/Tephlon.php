<?php
require_once("PHPSerializationPersistenceEngine.php");

define("DEFAULT_STALE_AGE", 0);
define("DEBUG_MODE", true);


class Tephlon {
	public static function getResource($driverName="File"){
		$ctx = self::extractContext(debug_backtrace(false));
		// List of available drivers
		if($driverName == "File"){
			return new
			PHPSerializationPersistenceEngine($ctx);
		}
		else{
			dlog("Driver $driverName not found");
			die();
		}
	}
	private static function extractContext($debug_backtrace){
		//print_r($debug_backtrace);
		$caller=$debug_backtrace[count($debug_backtrace)-1];
		if (isset($caller['class'])){
			return ($caller['class']);
		}
		dlog("context is global");
		return ("");
	}
}


function dlog($msg, $debug_level = false){
	date_default_timezone_set("EET");
	if(DEBUG_MODE || !$debug_level){
		echo "[".date("h:i:s u")."] ".$msg."\n";
	}
}