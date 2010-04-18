<?php

require_once("Flow.php");

class User {
	private $id;
	private $name;
	private $pass_hash;
	 
	public function __construct($id, $password){
		$this->id = $id;
		$this->pass_hash = md5($password);
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function auth($pass_hash){
		return ($pass_hash == $this->pass_hash);
	}
}