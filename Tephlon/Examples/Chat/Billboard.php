<?php

class BillBoard extends TephlonType {
    private $bboard = "BillBoard";
    public function __construct(){
        parent::__construct($this);
    }
    private function validateLine($line){
        if($line instanceof Line){
            return true;
        }
        return false;
    }
    public function addLine($line){
        if(!$this->validateLine($line)){
            return false;
        }
        // Will create a record "BillBoard" if doesnt exist
        $this->tr->retrieve($this->bboard, array());
        
        $lock = $this->tr->atomicBegin($this->bboard);
        if(!$lock){
            dlog("Failed to get lock for ".$this->bboard, ERROR);
            return false;
        }

        /* SYNCHRONIZED */
        $msgs = $this->tr->retrieve($this->bboard, array());
        // Fifo buffer enter element
        if(is_array($msgs) && count($msgs > 0)){
            array_unshift($msgs, $line);
        }
        else{
            $msgs[]=$line;
        }
        // Fifo buffer remove oldest element
        while(count($msgs) > 20){
            array_pop($msgs);
        }
        if(is_array($msgs)){
            $this->tr->register($msgs, $this->bboard, $this->tephlon_lifetime);
        }
        else{
            dlog("Invalid not-array, ".print_r($msgs), ERROR);
        }
        // Release mutex
        $unlocked = $this->tr->atomicEnd($this->bboard);

        /* END SYNCHRONIZED */
        if(!$unlocked){
            dlog("Failed to release lock for ".$this->bboard, INFO);
        }
        return $msgs;
    }
    public function getLines(){
        return $this->tr->retrieve($this->bboard);
    }
}