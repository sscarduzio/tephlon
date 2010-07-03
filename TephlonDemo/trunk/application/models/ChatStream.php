<?php
/**
 * Chat demo engine.
 */

class ChatStream extends Model{
	
    private $buf = null;
    
    public function __construct(){
    	parent::Model();
    	// Some unique name as a resource label..
    	$resourceLabel = 'CI_'.get_class();
    	// Create tephlon resource (FIFO Buffer)
        $this->buf = new TBuffer_FIFO($resourceLabel);
        // Buffer will keep always the first 25 lines
        $this->buf->setTbufferSize(25);
    }
    
    // Validate and insert the new line in the buffer
    public function addLine($line){
        if(!$this->validateLineObject($line)){
            return false;
        }
        // Infinite add, TBuffer will trim the oldest away
        $this->buf->add($line);
    }
    
    // Return sorted by time non-assoc. array 
    public function getLines(){
        return $this->buf->getAll();
    }

    private function validateLineObject($line){
        if($line instanceof Line){
            return true;
        }
        return false;
    }
    public function validateLine($str){
       if(strlen($str) > 0){
       	return true;
       }
       return false;
    }
    public function validateNick($str){
    	$len = strlen($str);
        if( $len > 3 && $len < 10){
            return true;
        }
        return false;
    }
}
