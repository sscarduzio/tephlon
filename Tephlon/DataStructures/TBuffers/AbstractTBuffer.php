<?php

/**
 * Abstract TBuffer
 * Adding an object with add() method will result in the deletion
 * of the oldest (FIFO) or newest (LIFO) record if the size of the buffer is exceeded.
 * Regular deletion and edit is intentionally not supported.
 * User can pop out (serve) objects from the buffer with next() function.
 */

require_once(dirname(__FILE__).'/../TephlonType.php');

abstract class AbstractTBuffer extends TephlonType{
    private $tbufferSize = 0;

    public function __construct($that){
        parent::__construct($that);
    }

    /**
     * Change at any time the size of the buffer, default is unlimited.
     *
     * @param int $n the new size of the buffer.
     */
    public function setTbufferSize($n){
        if(is_int($n) && $n >= 0){
            $this->tbufferSize = $n;
            return true;
        }
        return false;
    }
    /**
     * Add an object to the buffer
     * @param $obj
     */
    public function add($obj){
        $r = $this->tr->register($obj, microtime(true),$this->tephlon_lifetime);
        if(!$r){
            return false;
        }
        if(
        $this->tbufferSize != 0 &&
        count($this->tr->getIndex()) > $this->tbufferSize
        ){
            // Buffer was full, drop an object from the buffer
            $this->next();
        }
        return true;
    }
    public function clear(){
        return $this->tr->clear();
    }

    /**
     * Gets all the elements as a NON-ASSOCIATIVE array.
     */
    public function getAll(){
        $idx = $this->tr->getIndex();
        sort($idx);
        $v = array();
        foreach($idx as $key){
            $v[] = $this->tr->retrieve($key);
        }
        return $v;
    }
    public function size(){
        return count($this->tr->getIndex());
    }
    
    /**
     * Extract from the buffer
     */
    public function next(){
        $idx = $this->tr->getIndex();
        sort($idx);
        $label = $this->nextLabel($idx);
        $value = $this->tr->retrieve($label);
        $this->tr->delete($label);
        return $value;
    }
    
    abstract protected function nextLabel($idx);
}