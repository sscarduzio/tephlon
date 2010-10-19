<?php

require_once ("PersistenceEngine.php");
require_once ("DBConnector.php");
/**
 * Generic SQL driver
 * @author Simone
 *
 */
class SQLResource extends PersistenceEngine {
	private $dbc;
	
	public function __construct($ctx, $connectionString){
		parent::__construct($ctx);
        $this->dbc = DBConnector::getConnector($ctx,$connectionString);
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
			
	}

	protected function doGetLastModified($key){
			
	}

	protected function doRegister($record){
			$k =  $this->dbc->insert($record);
			if($k !== false){
				return $record->getKey();
			}
			return false;
	}

	protected function doRetrieve($key){
		  $data = $this->dbc->select($key);
		  if(is_array($data)){
			return new Record($data['key'], $data['payload'],time());
		  }
		  return false;
	}

	protected function doSetContext($ctx){
			
	}
}
