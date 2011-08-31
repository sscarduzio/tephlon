<?php

require_once ("PersistenceEngine.php");
require_once ("DBCFactory.php");
/**
 * Generic SQL driver
 * @author Simone
 *
 */
class SQLResource extends PersistenceEngine {
	private $dbc;

	public function __construct($ctx, $connectionString){
		$this->dbc = DBCFactory::getConnector($ctx,$connectionString);
		parent::__construct($ctx);
		$this->dbc->createTable($ctx);
	}

	protected function doClear(){
		return $this->dbc->truncate();
	}

	protected function doDelete($key){
		return $this->dbc->delete($key);
	}

	protected function doExists($key){
		return  is_array($this->dbc->select($key));
	}
	static function arrToaVal($a){
		return $a[0];
	}
	protected function doGetIndex(){
		$r = $this->dbc->index();
		foreach($r as $n => $val){
			$r[$n]=$val[0];
		}
		return $r;
	}
	 
	protected function doGetLastAccessed($key){
			// Not implemented
	}

	/**
	 * Implement the select-delete-insert
	 * This is in SQLResource because it's not generic, file resource
	 * will just overwrite existing record, but we can't insert if the
	 * row is present
	 * @param Record $record
	 */
	protected function doRegister($record){
		$data = $record->toAssoc();
		$data['lastModified'] = time();
		$data['content'] = serialize($data['content']); 
		// This already deletes the stale records from DB
		$r = $this->doRetrieve($record->getKey());
		if($r instanceof Record){
			$this->doDelete($record->getKey());
		}
		$k =  $this->dbc->insert($data);
		if($k !== false){
			return $record->getKey();
		}
		return false;
	}

	/**
	 * Just get the record from DB, check for stale is handled in super
	 * @param String $key
	 */
	protected function doRetrieve($key){
		$data = $this->dbc->select($key);
		if(!is_array($data)){
			return null;
		}
		// Calculate the time to live
		$ttl = $data['willExpireAt']-time();
		return new Record($data['key'], unserialize($data['content']), $ttl);
	}

	protected function doSetContext($ctx){
		// Flush stale records with one SQL statement
		$this->dbc->purge();
	}
	
    protected function doGetLastModified($key){
        $data = $this->dbc->select($key);
        if(is_array($data)){
            return $data['lastModified'];
        }
        return null;
    }
}
