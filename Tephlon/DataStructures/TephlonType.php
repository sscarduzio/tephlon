<?php
/**
 * This class is meant to be a superclass for the other data structures.
 * It will make available:
 * - a protected field which represents the tephlon resource.
 * - a modifier of default stale age.
 *
 *
 * @author Simone
 *
 */

require_once(dirname(__FILE__)."/../Tephlon.php");
class TephlonType {
	protected $tephlon_lifetime = DEFAULT_STALE_AGE;
	protected $tr = null;
	public static $log;

	protected function __construct($that){
		$this->tr = Tephlon::getResource($that);
		if(! ($this->tr instanceof PersistenceEngine) ){
			self::$log->error("Tephlon resource did not initialize  correctly");
			return false;
		}
		return true;
	}
	public function setDefaultStaleAge($time){
		if(is_int($time) && $time >= 0){
			$this->tephlon_lifetime = $time;
			return true;
		}
		return false;
		
	}
}