<?php
/**
 * PHPSerializationPersistenceEngine class
 *
 * Store the data structures to file using the internal PHP
 * Serialization/Unserialization mechanism.
 *
 * @author Simone Scarduzio
 */

require_once ("PersistenceEngine.php");

class PHPSerializationPersistenceEngine extends PersistenceEngine {

	private $cache_path = "cache/PSPE/";
	private $cache_suffix = "txt";

	protected function __construct(){
		// Populate internal record map
		if(!file_exists($this->cache_path)){
			mkdir($this->cache_path, 0777, true);
		}
		$fileList = glob($this->key2filepath("*"));
		foreach ($fileList as $file_path){
			//xyz.txt
			$file_name = basename ($file_path);
			//xyz
			$file_key = basename($file_path,$this->cache_suffix);
			// Delete stale records
			$the_record = unserialize(file_get_contents($file_path));
			if($the_record->isStale()){
				unlink($file_path);
			}
		}
	}

	/**
	 * Implementation specific low level write operation
	 *
	 * @param Record $record
	 * @return boolean
	 */

	protected function doRegister($record){
		$key = $record->getKey();
		// Dump to file
		try{
			$file_path = $this->key2filepath($key);
			$fp = fopen($file_path,"w");
			fwrite($fp,serialize($record));
			fclose($fp);
		}catch (Exception $e){
			$this->log("Unable to write record to file: ".$key);
			return false;
		}
		return true;
	}

	/**
	 * Implementation specific low level read operation
	 *
	 * @param String $key
	 * @return Record type if file named as record's key is found
	 * @return null if we didnt find any file named as $key
	 */
	protected function doRetrieve($key){
		$fpath = $this->key2filepath($key);
		if(file_exists($fpath)){
			// It will be already a type Record
			return unserialize(file_get_contents($fpath));
		}
		return false;
	}
	
	protected function doDelete($key){
		$fn = $this->key2filepath($key);
		if(file_exists($fn)){
			unlink($fn);
			return true;
		}
		return false;
	}

	private function key2filepath($key){
		return $this->cache_path.$key.".".$this->cache_suffix;
	}
}