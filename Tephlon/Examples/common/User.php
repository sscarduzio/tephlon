<?php

class User {
	private $id;
	private $name;
	private $pass_hash;
	 
	public function __construct($id, $plain_password){
		$this->id = $id;
		$this->pass_hash = md5($plain_password);
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
	
	public function auth($plain_password){
		return ($pass_hash == md5($this->pass_hash));
	}
	
}