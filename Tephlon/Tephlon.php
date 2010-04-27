<?php
require_once("lib/FileResource.php");

define("DEFAULT_STALE_AGE", 0);
define("DEBUG_MODE", true);


class Tephlon {
	public static function getResource($that = null, $driverName="File"){
		$ctx = self::extractContext($that);
		// List of available drivers
		if($driverName == "File"){
			return new
			FileResource($ctx);
		}
		else{
			dlog("Driver $driverName not found");
			die();
		}
	}
	private static function extractContext($that){
		//print_r($debug_backtrace);
		if(!is_null($that)){
			return get_class($that);
		}
		return "";
		//		$caller=$debug_backtrace[count($debug_backtrace)-1];
		//		if (isset($caller['class'])){
		//			return ($caller['class']);
		//		}
		//		dlog("context is global",true);
		//		return ("");
	}
}


function dlog($msg, $debug_level = false){
	date_default_timezone_set("EET");
	if(DEBUG_MODE || !$debug_level){
		echo "[".date("h:i:s u")."] ".$msg."\n";
	}
}