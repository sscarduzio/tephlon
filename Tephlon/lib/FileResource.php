<?php
/**
 * FileResource class
 *
 * Store the data structures to file using the internal PHP
 * Serialization/Unserialization mechanism.
 *
 * @author Simone Scarduzio
 */

require_once ("PersistenceEngine.php");

class FileResource extends PersistenceEngine {

	private $cache_path = FILE_CACHE_DIR;
	private $cache_suffix = "ser";

	public function __construct($context){
		$this->setContext($context);
	}

	/**
	 * Whenever we change context or create this engine, we need to
	 * assure the directory exists and contains no stale records
	 *
	 * @param $ctx
	 */
	protected function doSetContext($ctx){
        if(!$this->cache_path || $this->cache_path == ""){
        	dlog("File cache dir can't be empty. Please set FILE_CACHE_DIR in tephlon_conf.php", ERROR);
        	die();
        }
		if(!file_exists($this->cache_path)){
			mkdir($this->cache_path, 0777, true);
		}
		if(!file_exists($this->getCachePath())){
			mkdir($this->getCachePath(), 0777, true);
		}

		$file = glob($this->getCachePath()."*");
		foreach($file as $dir){
			if(is_dir($dir)){
				$this->cleanStaleFiles($dir);
			}
		}

	}
	private function cleanStaleFiles($path){
		$fileList = glob($path."/*");
		foreach ($fileList as $file_path){
			//xyz.txt
			$file_name = basename ($file_path);
			// Delete stale records
			$the_record = unserialize(file_get_contents($file_path));
			if(!$the_record){
				dlog("ERROR: cleanStaleFiles($path): unable to read $file_path",DEBUG);
			}
			if($the_record->isStale()){
				dlog("Self Maintainance: Removing stale".realpath($file_path), DEBUG);
				unlink($file_path);
			}
		}
	}

	private function getCachePath(){
		if(!is_null($this->context)){
			return $this->cache_path.$this->getContext()."/";
		}
		return $this->cache_path;
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
			// Added support for multi-dir scalable file storage
			if(!file_exists(dirname($file_path))){
				mkdir(dirname($file_path), 0777, true);
			}
			$fp = fopen($file_path, "w");
			fwrite($fp,serialize($record));
			fclose($fp);
		}catch (Exception $e){
			dlog("Unable to write record to file: ".$key, ERROR);
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
		$len = strlen($key);
		$subdir = substr($key, $len-1, $len);
		return $this->getCachePath().$subdir."/".$key.".".$this->cache_suffix;
	}
}