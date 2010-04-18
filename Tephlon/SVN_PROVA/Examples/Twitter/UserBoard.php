<?php

require_once("User.php");

class UserBoard extends TephlonDT{
	private $users = array();

	public function __construct(){
		$this->users = $this->tephlonInit("UserBoard");
		if(!$this->users){
			$this->users = array();
		}
	}
	
	public function addUser($user){
		if($user instanceof User && in_array($user->getID(), $this->users)){
			die("User ID is already present in the system!");
		}
		return $this->writeUser($user);
	}
	
	public function updateUser($user){
		if($user instanceof User && in_array($user->getID,$this->users)){
			return $this->writeUser($user);
		}
		die("User not found / Invalid user");
	}

	private function writeUser($user){
		$this->users[$user->getID()] = $user;
		return $this->tephlonSave($this->users);
	}
	
	public function deleteUser($user){
		if(!($user instanceof User)){
			die("can't delete invalid user object");
		}
		if(!in_array($user->getID())){
			print("won't delete ".$user->getID().", user not found.");
			return;
		}
		unset($this->users[$user->getID()]);
		$this->tephlonSave($this->users);
		
	}
	public function getAll(){
		return $this->users;
	}
}