<?php
/**
 * Record data type class
 * 
 * This class represent the generic record data type
 * which can be handled by any type of Persistence mechanism.
 * 
 * @author Simone Scarduzio
 * 
 */

class Record {
	private $key;
	// UNSERIALIZED CONTENT (Object or array or var..)
	private $content;
	/**
	 * Will be deleted at <latest_access> + lifetime
	 * @var time
	 */
	private $willExpireAt;
	
	public function __construct($key, $content, $lifetime){
		$this->key = $key;
		$this->content = $content;
		if($lifetime == 0){
			$this->willExpireAt = 0;
		}
		else {
			$this->willExpireAt = time()+$lifetime;	
		}
	}
	private function resetAge(){
		$this->willExpireAt += $lifetime; 
	}
	public function getKey(){
		return $this->key;
	}
	public function getContent(){
		return $this->content;
	}
	public function getExpireTime(){
		return is_int($this->willExpireAt) ? $this->willExpireAt : 0;
	}
	public function updateContent($content){
		$this->content = $content;
		$this->resetAge();
	}
	public function isStale(){
		if($this->willExpireAt == 0){
			return false;
		}
		return ($this->willExpireAt < time());
	}
}