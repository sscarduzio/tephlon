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

require_once("../Tephlon.php");
class TephlonType {
	protected $tephlon_lifetime = DEFAULT_STALE_AGE;
	protected $tr = null;

	protected function __construct($that){
		$this->tr = Tephlon::getResource($that);
		if(! ($this->tr instanceof PersistenceEngine) ){
			dlog("Tephlon resource did not initialize  correctly", ERROR);
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