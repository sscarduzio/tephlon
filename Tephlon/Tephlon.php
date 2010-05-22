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
		if($ctx === false){
			dlog("Can't create this resource", ERROR);
			return false;
		}
		// List of available drivers
		if($driverName == "File"){
			$r = new FileResource($ctx);
		}
		else{
			dlog("Driver $driverName not found", ERROR);
			return false;
		}
		return $r;
	}
	
	private static function extractContext($label){
		if(is_null($label)){
			return "_global_context_";
		}
		if(is_object($label)){
			$label = get_class($label).".".sha1(serialize($label));
			return $label;
		}
		return PersistenceEngine::validateContext($label);
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