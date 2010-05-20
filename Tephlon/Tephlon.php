<?php

define("DEBUG", 3);
define("INFO", 2);
define("ERROR", 1);

require_once("config/tephlon_config.php");
require_once("lib/Logger.php");
require_once("lib/FileResource.php");
require_once("DataStructures/TMap.php");

class Tephlon {
	public static function getResource($that = null, $driverName="File"){
		$ctx = self::extractContext($that);
		// List of available drivers
		if($driverName == "File"){
			return new FileResource($ctx);
		}
		else{
			dlog("Driver $driverName not found", ERROR);
			die();
		}
	}
	private static function extractContext($that){
		if(!is_null($that)){
			// we hash all the object so that if we have instances which
			// differ by just the content of a field, they will have own
			// context.
			// return(sha1(serialize($that)));
			return(get_class($that).".".sha1(serialize($that)));
		}
		return "_global_context_";

	}
}

/******************** Procedural Practicalities.. **************************/

/**
 *  Logging is more practical to access from global scope
 *   better to call dlog() than $something->dlog(), right?
 */
function dlog($msg, $ll = INFO){
	$l = Logger::getInstance();
	$l->dlog($msg, $ll);
}

/**
 * First log line, to show when we initialized the library.
 */
date_default_timezone_set("EET");
if(LOG_VERBOSITY > 1){
	print("*** Tephlon init: ".date("D, d M Y @ h:i:s")."\n");
}