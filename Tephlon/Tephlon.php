<?php

define("DEBUG", 3);
define("INFO", 2);
define("ERROR", 1);

require_once("config/tephlon_config.php");
require_once("lib/Logger.php");
require_once("lib/FileResource.php");
require_once("DataStructures/TMap.php");
require_once("DataStructures/TBuffers/TBuffer_FIFO.php");
require_once("DataStructures/TBuffers/TBuffer_LIFO.php");

class Tephlon {
	/**
	 * Ask Tephlon for a basic persistence resource from Core API.
	 * Are you sure you don't want to ask for a more complete data
	 * structure like TMap?
	 * In that case just simply instantiate it:
	 *
	 * $map = new TMap("label");
	 *
	 * @param string|object $namespace The name of the resource you want. If a resource
	 * with this name exists, it will be returned. With all its records.
	 * If no resources are found with this name, Tephlon will create an empty one.
	 *
	 * @param string $driverName leave this empty, there's just File driver available.
	 *
	 * @return FileResource or false, if problems were detected in the namesapce string
	 * or if something went wrong creating/retrieving the resource itself.
	 */
	public static function getResource($namespace = null, $driverName="File"){
		$ctx = self::extractContext($namespace);
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
		if(PersistenceEngine::validateContext($label)){
			return $label;
		}
		return false;
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