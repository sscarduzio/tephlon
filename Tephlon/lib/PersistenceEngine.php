<?php
/**
 * Generic PersistentEngine
 */
require_once("Record.php");

class PersistenceEngine {
	private static $instance;
	protected $context;
	protected static $stale_age = DEFAULT_STALE_AGE;


	protected function setContext($ctx){
		if($this->context != $ctx){
			if($this->context == null){
				dlog("context init: $ctx", DEBUG);
			}
			else{
				dlog("context changed: $ctx", INFO);
			}
			$this->context = $ctx;
			$this->doSetContext($ctx);
		}
	}
	
	protected function getContext(){
		return $this->context;
	}
	public function clean(){
		$this->doSetContext($this->getContext());
	}
	/**
	 * Set the maximum age for a record to be considered stale
	 *
	 * @param unknown_type $time
	 * @return void
	 */
	public static function setStaleAge($time){
		if(is_integer($time))
		self::$stale_age = $time;
	}
	public static function getStaleAge(){
		return self::$stale_age;
	}

	/**
	 * Abstract retriever, check for common validity issues (content and age)
	 * upon searched record.
	 *
	 * Returns the value of the record from storage.
	 * If it was not found, records the default value
	 * and returns it.
	 *
	 * For technology specific operations (file handling, sql, ..)
	 * please see specific implementations' "protected doRetrieve()"
	 * method of the relative subclass (eg. FileResource.php).
	 *
	 * @param String $label is the searched object's label.
	 * @param Object $default return this value if engine doesn't have a record for "$label"
	 * @return undefined object, or the default value (optional)
	 */
	public function retrieve($label, $default=null){
		if($label == null){
			dlog("retrieve: null label, returning null", INFO);
			return null;
		}
		$key = $this->calculateKey($label);
		$record = $this->doRetrieve($key);
		if($record){
			if($record->isStale()){
				$this->delete($record);
			}
			else{
				return $record->getContent();
			}
		}
		if(!is_null($default)){
			$this->register($default, $label);
			return $default;
		}
		return null;
	}


	private function calculateKey($label){
		try{
			if(!$label || !is_string($label) || strlen($label) < 1){
				throw new Exception("You need to provide a label to register an object");
			}
			if($this->context){
				$key  = $this->context.".";
			}
			$key = $key.$label;
			return ($key."-".md5($key));
		}
		catch(Exception $e){
			die($e);
		}
	}

	/**
	 * Save the record to persistence
	 *
	 * @param unknown_type $object
	 * @param String $label
	 * @param int $expire_in_seconds
	 */
	public function register($object, $label, $expire_in_seconds = DEFAULT_STALE_AGE){
		$key = $this->calculateKey($label);

		$record = new Record($key, $object, $expire_in_seconds);
		if(!$this->doRegister($record)){
			return null;
		}
		return $key;
	}

	/**
	 * Delete record by label or Record object
	 *
	 * @param Record|String $label the label or Record.
	 */
	public function delete($label){
		if($label instanceof Record){
			$record = $label;
			$key = $record->getKey();
			return $this->doDelete($key);
		}
		if(!is_string($label)){
			dlog("non-string given as a key for delete!", INFO);
			return false;
		}
		$key = $this->calculateKey($label);
		return $this->doDelete($key);
	}
}
