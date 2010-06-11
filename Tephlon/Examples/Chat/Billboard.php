<?php
require_once("../../Tephlon.php");
require_once("Line.php");

class BillBoard {
	private $buf = null;
	public function __construct(){
		$this->buf = new TBuffer_FIFO($this);
		// We will keep just the 30 newest lines
		$this->buf->setTbufferSize(30);
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
		// Infinite add, TBuffer will trim the oldest away
		$this->buf->add($line);
	}
	public function getLines(){
		return $this->buf->getAll();
	}
}