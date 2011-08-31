<?php

/**
 * Database connector for SQLite
 * @author Simone
 *
 */
class SQLiteConnector extends DBConnector{

    public function __construct($ctx, $db){
      parent::__construct($ctx, $db);
    }
    
   // Customizations' hook from superclass
   function overrideStatements(){
   	    // Inject pre-creation code to default to InnoDB engine
        $this->stm['before_create'] = 
        "SET SESSION storage_engine = INNODB; ";
   }
	
}