<?php

/* ************** Tephlon library configuration ***************************** */

	// The default lifetime of a record, 0 = forever
	define("DEFAULT_STALE_AGE", 0);
	
	// Resources are directories, Records are files containing your objects/vars
	// In which dir to store them? You may want to put absolute path for clarity.
	define("FILE_CACHE_DIR", "cache/");
	
	// View debug messages
	define("DEBUG_MODE", true);

/* ************************************************************************** */

require_once("lib/FileResource.php");

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
		if(!is_null($that)){
			return get_class($that);
		}
		return "";
	}
}


function dlog($msg, $debug_level = false){
	date_default_timezone_set("EET");
	if(DEBUG_MODE || !$debug_level){
		echo "[".date("h:i:s u")."] ".$msg."\n";
	}
}