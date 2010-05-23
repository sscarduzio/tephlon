<?php
/**
 * This is a map
 * Let's implement the majority of Java's Map methods 
 * These few remained unimplemented for choice or for will.

 boolean
 containsValue(Object value)
 Returns true if this map maps one or more keys to the specified value.

 Set
 entrySet()
 Returns a collection view of the mappings contained in this map.
 
 void
 putAll(Map m)
 Copies all of the mappings from the specified map to this map These mappings will replace any mappings that this map had for any of the keys currently in the specified map.

 */
require_once("TephlonType.php");

class TMap extends TephlonType{
	private $map = array();
	//private $rmap = array();

	public function __construct($that){
		parent::__construct($that);
	}
    
	/**
	 * Associates the specified value with the specified key in this map.
	 * @param unknown_type $mkey
	 * @param unknown_type $mval
	 */
	public function put($mkey, $mval){
		if(!strlen($mkey) > 0){
			dlog("Key $mkey is invalid: ignoring..", DEBUG);
			return false;
		}
		return $this->tr->register($mval, $mkey, $this->tephlon_lifetime);
	}
	
	/**
	 * Returns the value to which the specified key is mapped, 
	 * or null if the map contains no mapping for this key.
	 * @param unknown_type $mkey
	 */
	public function get($mkey){
		return $this->tr->retrieve($mkey);
	}
	
	/**
	 * Removes the mapping for this key from this map if present.
	 * @param unknown_type $mkey
	 */
	public function remove($mkey){
		if(!is_string($mkey)|| !strlen($mkey) > 0){
			dlog("can't delete invalid map key, ignoring..", WARNING);
			return false;
		}
		if(!$this->tr->delete($mkey)){
			dlog("key $mkey not found", INFO);
			return false;
		}
		return true;
	}
	
    /**
     * Removes all mappings from this map.
     */
	public function clear(){
		return $this->tr->clear();
	}
	
    /**
     * Returns true if this map contains no key-value mappings.
     */
	public function isEmpty(){
		$map = $this->tr->getIndex();
		foreach($map as $k){
			return false;
		}
		return true;
	}
	
	/**
	 * Return an associative array key => value representing all the content
	 * of this map
	 */
	public function getAll(){
		$map = $this->tr->getIndex();
		$v = array();
		foreach($map as $key){
			$v[$key] = $this->get($key);
		}
		return $v;
	}
	public function refresh(){
		$this->tr->refresh();
	}
	
	/**
	 * Returns the number of key-value mappings in this map.
	 */
	public function size(){
		return count($this->tr->getIndex());
	}
	
    /**
     * Returns true if this map contains a mapping for the specified key.
     * @param unknown_type $mkey
     */
	public function containsKey($mkey){
		if(is_null($mkey)){
			return false;
		}
		return $this->tr->exists($mkey);
	}
	
    /**
     *  Returns a collection view of the values contained in this map.
     */
	public function values(){
		$map = $this->tr->getIndex();
		$v = array();
		foreach($map as $key){
			$val = $this->get($key);
			if(!is_null($val)){
				$v[] = $val;
			}
            else {
            	dlog("TMap.values: returned a null value for $key", DEBUG);
            }
		}
		return $v;
	}
}