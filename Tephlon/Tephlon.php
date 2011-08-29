<?php
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
define("CONN_STR", "_TephlonSQLConnectionString");


require_once( BASE_PATH . "/lib/log/log4php/Logger.php");
require_once( BASE_PATH . "/config/tephlon_config.php");
date_default_timezone_set(TZONE);
require_once( BASE_PATH . "/lib/log/AELogger.php");
require_once( BASE_PATH . "/lib/FileResource.php");
require_once( BASE_PATH . "/lib/SQLResource.php");
require_once( BASE_PATH . "/DataStructures/TMap.php");
require_once( BASE_PATH . "/DataStructures/TBuffers/TBuffer_FIFO.php");
require_once( BASE_PATH . "/DataStructures/TCounter.php");

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
	
	public static $log;
	
	public static function getResource($namespace = null, $driverName="File"){
		$ctx = self::extractContext($namespace);
		if($ctx === false){
			self::$log->error("Can't create this resource");
			return false;
		}

		if($driverName == "File"){
			return new FileResource($ctx);
		}
		if(defined($driverName.CONN_STR)){
			$connectionString = constant($driverName.CONN_STR);
			self::$log->debug("Found connection string: ".$connectionString);
			return new SQLResource($ctx, $connectionString);
		}
		$log->error("Driver $driverName not found");
		return false;
	}

	private static function extractContext($label){
		if(is_null($label)){
			return "_global_context_";
		}
		if(is_object($label)){
			$label = sha1(serialize($label));
			return $label;
		}
		if(PersistenceEngine::validateContext($label)){
			return $label;
		}
		return false;
	}
}

initLogger();