<?php
/**
 * Generic PersistentEngine
 */
require_once("PersistenceEngineInterface.php");
require_once("Record.php");
define("DEFAULT_STALE_AGE", 0);
define("DEBUG_MODE", true);

class PersistenceEngine implements PersistenceEngineInterface{
	private static $instance;
	protected static $stale_age = DEFAULT_STALE_AGE;
	
	/**
	 * Private constructor, always called once.
	 *
	 * @return PersistenceEngine's implementation instance
	 */
	private function __construct(){
		self::$stale_age = DEFAULT_STALE_AGE;
	}

	/**
	 * Public accessor to our unique instance of the engine.
	 * Actual architecture imposes to have just one persistence
	 * technology in use at a time.
	 *
	 * @return PersistenceEngine
	 */
	public static function getInstance(){
		// We don't want instances of this superclass!
		// but just subclasses to be created.
		if(self::$instance instanceof PersistenceEngine &&
		!get_class(self::$instance) != "PersistentEngine")
		{
			##TODO## Support for multi-singleton:
			## array of instances of different technologies
			return self::$instance;
		}
		$class = get_called_class();
		return self::$instance = new $class();
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
	 * method of the relative subclass (eg. PHPSerializationPersistence.php).
	 *
	 * @param String $label is the searched object's label.
	 * @param Object $default return this value if engine doesn't have a record for "$label"
	 * @return undefined object, or the default value (optional)
	 */
	public function retrieve($label, $default=null){
		if($label == null){
			return null;
		}
		$key = $this->calculateKey(debug_backtrace(false), $label);
		$record = $this->doRetrieve($key);
		if($record){
			if($record->isStale()){
				$this->delete($record);	
			}
			else{
				return $record->getContent();
			}
		}
		if($default != null){
			$this->register($default, $label);
			return $default;
		}
		return null;
	}

	/**
	 * Every record must have a unique key, but we can't achieve
	 * this just pretenting every instance of every object registers
	 * its piece of data with an always unique key.
	 * So we generate the key from label and the stacktrace from where 
	 * we called the register/retrieve. So every object will have its own data.
	 * 
	 * @param unknown_type $debug_backtrace
	 * @param unknown_type $label
	 * @return unknown
	 */
	##TODO## What about two instances of the same object accessing same label and having same stacktrace?
	
	private function calculateKey($debug_backtrace, $label){
		try{
			if(!$label || !is_string($label) || strlen($label) < 1){
				throw new Exception("You need to provide a label to register an object");
			}
			$key = "";
			//for($i = 1 ; $i < count($debug_backtrace); $i++){
			for($i = count($debug_backtrace)-1 ; $i < count($debug_backtrace); $i++){
				$caller=$debug_backtrace[$i];
				//$key.="[".$i." ";
				if (isset($caller['class'])){
					$key .= $caller['class'].".";
				}
//				if (isset($caller['function'])){
//					$key .= $caller['function']."]";
//				}
			}
			$key.=$label;
			// a checksum function is needed to avoid to exceed filename lenght
			return ($key);
		}
		catch(Exception $e){
			die($e);
		}
	}


	public function register($object, $label, $expire_in_seconds = DEFAULT_STALE_AGE){
		
		$key = $this->calculateKey(debug_backtrace(false), $label);
		
		$record = new Record($key, $object, $expire_in_seconds);
		if(!$this->doRegister($record)){
			return null;
		}
		return $key;
	}
	/**
	 * 
	 * @param unknown_type $label is a label string  or Record object
	 */
	public function delete($label){
		if($label instanceof Record){
			$record = $label;
			$key = $record->getKey(); 
			return $this->doDelete($key);
		}
		if(!is_string($key)){
			$this->log("non-string given as a key for delete!");
			return false;
		}
		$key = $this->calculateKey(debug_backtrace(false), $label);
		return $this->doDelete($key);
	}
	
	protected function log($message, $debug = false){
		if($debug && DEBUG_MODE){
			print(date()." [DBG] ".get_called_class().": ".$message);
		}
		else {
			print(date()." [INF] ".get_called_class().": ".$message);
		}
	}
	
}