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
		if($this->setContext($context) === false){
			$this->context = false;
		}
	}

	/**
	 * Whenever we change context or create this engine, we need to
	 * assure the directory exists and contains no stale records
	 *
	 * @param $ctx
	 */
	protected function doSetContext($ctx){
		if(!is_string($this->cache_path) || $this->cache_path == ""){
			dlog("File cache dir must be non empty string. Please set FILE_CACHE_DIR in tephlon_conf.php", ERROR);
			return false;
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
		return true;
	}
	private function cleanStaleFiles($path){
		$fileList = glob($path."/*".$this->cache_suffix);
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

	protected function doClear(){
		dlog("clear all records, wiping whole cache dir: ".$this->getCachePath(), INFO);
		return $this->deleteDirTree($this->getCachePath());
	}

	private function getCachePath(){
		if(!is_null($this->context)){
			return $this->cache_path.$this->getContext()."/";
		}
		return $this->cache_path;
	}
	 
	private function deleteDirTree($dir, $delete_root=false) {
		$status = true;
		if(!file_exists($dir)){
			dlog("delete dir tree: dir not found $dir",INFO);
			return true;
		}
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
		{
			if ($file->isDir()) {
				$r = rmdir($file->getPathname());
			} else {
				$r = unlink($file->getPathname());
			}
			$status &= $r;
		}
		// We dont want to delete the main resource's dir.
		if($delete_root){
			return $status &= rmdir($dir);
		}
		return $status;
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

		$file_path = $this->key2filepath($key);
		// Added support for multi-dir scalable file storage
		if(!file_exists(dirname($file_path))){
			mkdir(dirname($file_path), 0777, true);
		}
		// Mutex lock for writes
		if(!file_put_contents($file_path, serialize($record), LOCK_EX)){
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
			// The last record to be deleted deletes also its subdir
			if(count(glob(dirname($fn)."/*ser")) == 0){
				$this->deleteDirTree(dirname($fn), true);
			}
			return true;
		}
		return false;
	}

	protected function doExists($key){
		$fn = $this->key2filepath($key);
		if(file_exists($fn)){
			return true;
		}
		return false;
	}

	private function key2filepath($key){
		$md5 = md5($key);
		$subdir = substr($md5, 30, 31);
		return $this->getCachePath().$subdir."/".$key.".".$this->cache_suffix;
	}

	private function filepath2key($fp){
		return basename($fp,".".$this->cache_suffix);
	}

	protected function doGetIndex(){
		$arr = glob($this->getCachePath()."/*/*.ser");
		$out = array();
		foreach ($arr as $fn){
			$key = $this->filepath2key($fn);
			$out[] = $key;
		}
		return $out;
	}
}