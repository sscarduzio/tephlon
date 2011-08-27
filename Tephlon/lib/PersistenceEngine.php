<?php
/**
 * Generic PersistentEngine
 * Superclass for all drivers, handles creation, deletion and expirations of 
 * records, validates labels and namespaces.
 * Wraps up all the common logic, so that driver just needs to 
 * perform low level CRUD, listing present keys and such basic stuff.
 */

require_once("Record.php");
require_once("Mutex/Mutex.class.php");

abstract class PersistenceEngine {
	protected $context;
	protected static $stale_age = DEFAULT_STALE_AGE;
    
	protected function __construct($context){
        if($this->setContext($context) === false){
            $this->context = false;
        }
    }
	
	protected function setContext($ctx){
		if($this->context != $ctx){
			if($this->context == null){
				dlog("context init: $ctx", DEBUG);
			}
			else{
				dlog("context changed: $ctx", INFO);
			}
			$this->context = $ctx;
			return $this->doSetContext($ctx);
		}
	}
	abstract protected function doSetContext($ctx);

	/**
	 * Returns the namespace of this resource
	 */
	public function getContext(){
		return $this->context;
	}

	/**
	 * Scans for stale records and deletes the expired ones
	 */
	public function refresh(){
		$this->doSetContext($this->getContext());
	}
	public function clear(){
		return $this->doClear();
	}
	abstract protected function doClear();

	/**
	 * Set the desired lifetime in seconds for a record before it's considered
	 * stale.
	 * Once it's stale it will be eventually wiped out next time you try to 
	 * retrieve it or call refresh().
	 * IMPORTANT: if you want records to last forever, set this value to zero.
	 * 
	 *
	 * @param unknown_type $time
	 * @return void
	 */
	public static function setLifetime($time){
		if(is_integer($time) && $time >= 0 ){
			self::$stale_age = $time;
			return true;
		}
		return false;
	}

	/**
	 * Gets the configured lifetime of a record
	 */
	public static function getLifetime(){
		return self::$stale_age;
	}

	/**
	 * Returns the value of the record from storage.
	 * If it was not found, and $default is defined, creates a new record containing
	 * the $default value and having $label as label. And finally returns the call
	 * with $default value
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
			$log->info("retrieve: null label, returning null");
			return null;
		}
		if(!$this->validateName($label)){
			return null;
		}
		$key = $this->label2key($label);
		$record = $this->doRetrieve($key);
		if($record instanceof Record){
			if($record->isStale()){
				$this->delete($record);
				$record = null;
			}
			else{
				return $record->getContent();
			}
		}
		// Record would now be null just because found as stale!
		if(!is_null($default) && !$record){
			$this->register($default, $label);
			// Automatic check-select-insert assignment (return the value)
			return $default;
		}
		return null;
	}
	abstract protected function doRetrieve($key);

	private function validateName($label){
		if($label === null){
			$log->error("Name was null, invalid name.");
			return false;
		}
		//if(is_numeric($label))
		$len = strlen($label);
		if($len > 200 || $len < 1){
			$log->error("Invalid name length: ".strlen($label));
			return false;
		}
		if(is_string($label)){
			$badchars = array(' ', '\\', '/', ':', '*', '?', '"', '<', '>', '|');
			foreach ( $badchars as $bc){
				if( is_numeric(strpos( $label, $bc )) ){
					$log->error("Invalid character found in name: ".$bc);
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Validates if a string is legal to be a label or namespace value
	 */
	public static function validateContext($ctx){
		return self::validateName($ctx);
	}

	private function label2key($label){
		if($this->validateName($label)){
			return $label;
		}
	}

	/**
	 * Save a record to persistence.
	 *
	 * @param unknown_type $object The generic item you want to memorize
	 * @param String $label The label for retrieving it in future
	 * @param int $expire_in_seconds Optional custom per-record lifetime
	 */
	public function register($object, $label, $expire_in_seconds = -1){
		if( $expire_in_seconds < 0){
			$expire_in_seconds = $this->getLifetime();
		}
		if(!$this->validateName($label)){
			return null;
		}
		if(is_null($object)){
			$log->warning("Registering null object, skipping writing.");
			// Returning true because it's not unsuccessful, we anyway return null
			// if we can't retrieve a label, and that will be the null he registered.
			return true;
		}
		$key = $this->label2key($label);

		$record = new Record($key, $object, $expire_in_seconds);
		if(!$this->doRegister($record)){
			return null;
		}
		return $key;
	}
	abstract protected function doRegister($record);

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
		if(!$this->validateName($label)){
			return null;
		}
		$key = $this->label2key($label);
		return $this->doDelete($key);
	}
    abstract protected function doDelete($key);
	/**
	 * Check if we have in storage a valid record for this label
	 * @param unknown_type $label
	 */
	public  function exists($label){
		if(!$this->validateName($label)){
			return null;
		}
		$key = $this->label2key($label);
		return $this->doExists($key);
	}
	
	abstract protected function doExists($key);
	/**
	 * Returns an array of all labels of valid records
	 */
	public function getIndex(){
		return $this->doGetIndex();
	}
	abstract protected function doGetIndex();
	/**
	 * Start an atomic operation on a record
	 * Important: use this just if you need atomic logic between read and write,
	 * for example visit counter.
	 * Simple write operations are already automatically atomic, and don't need
	 * this method to be called.
	 */
    public function atomicBegin($label){
       if(!$this->exists($label)){
      	$log->error("Trying to get Mutex for a record whose label does not exist");
        return false;
       }
       $m = new Mutex($this->label2key($this->getContext().$label));
       $m->lock();
       return true;
    }
    
    /**
     * Release generic per-record mutex lock
     */
    public function atomicEnd($label){
       if(!$this->exists($label)){
        $log->error("Trying to release Mutex for a record whose label does not exist");
        return false;
       }
       $m = new Mutex($this->label2key($this->getContext().$label));
       $m->unlock();
       return true;
    }
    
    /**
     * Gets the timestamp (epoch format) of when the record was modified
     * last time. 
     */
    public function getLastModified($label){
    	return $this->doGetLastModified($this->label2key($label));
    }
    abstract protected function doGetLastModified($key);
    
    /**
     * Gets the timestamp (epoch format) of when the record was accessed
     * last time. 
     */
    public function getLastAccessed($label){
    	return $this->doGetLastModified($this->label2key($label));
    }
    abstract protected function doGetLastAccessed($key);
}
