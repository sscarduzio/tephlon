<?php
/**
 * This is a map
 * Let's implement the majority of Java's hashmap methods ( the ones with # are
 * implemented, the others make few sense to implement.)

 void
 clear()
 Removes all mappings from this map.

 boolean
 # containsKey(Object key)
 Returns true if this map contains a mapping for the specified key.

 boolean
 containsValue(Object value)
 Returns true if this map maps one or more keys to the specified value.

 Set
 entrySet()
 Returns a collection view of the mappings contained in this map.

 Object
 # get(Object key)
 Returns the value to which the specified key is mapped in this identity hash map, or null if the map contains no mapping for this key.

 boolean
 # isEmpty()
 Returns true if this map contains no key-value mappings.

 Object
 # put(Object key, Object value)
 Associates the specified value with the specified key in this map.

 void
 putAll(Map m)
 Copies all of the mappings from the specified map to this map These mappings will replace any mappings that this map had for any of the keys currently in the specified map.

 Object
 # remove(Object key)
 Removes the mapping for this key from this map if present.

 int
 # size()
 Returns the number of key-value mappings in this map.

 Collection
 # values()
 Returns a collection view of the values contained in this map.
 */
require_once("TephlonType.php");

class TMap extends TephlonType{
	private $map = array();
	//private $rmap = array();

	public function __construct($that){
		parent::__construct($that);
	}

	public function put($mkey, $mval){
		if(!strlen($mkey) > 0){
			dlog("Key $mkey is invalid: ignoring..", DEBUG);
			return false;
		}
		return $this->tr->register($mval, $mkey, $this->tephlon_lifetime);
	}
	public function get($mkey){
		return $this->tr->retrieve($mkey);
	}
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

	public function clear(){
		return $this->tr->clear();
	}

	public function isEmpty(){
		$map = $this->tr->getIndex();
		foreach($map as $k){
			return false;
		}
		return true;
	}
	// Associative array k->v
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
	public function size(){
		return count($this->tr->getIndex());
	}

	public function containsKey($mkey){
		if(is_null($mkey)){
			return false;
		}
		return $this->tr->exists($mkey);
	}

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