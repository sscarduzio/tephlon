<?php

require_once("../../Tephlon.php");
require_once("User.php");

class UserBoard {
	private $users = array();

	public function __construct(){
		$this->users = new TMap($this);
	}

	public function addUser($user){
		if(!($user instanceof User)){
			die("Not an user object");
		}
		if( $this->users->containsKey($user->getID()) ){
			die("User ID is already present in the system!");
		}
		return $this->users->put($user->getID(), $user);
	}

	public function updateUser($user){
		if(!($user instanceof User)){
			die("Not an user object");
		}
		if($this->users->containsKey($user->getID()) ){
			return $this->writeUser($user);
		}
		die("User ".$this->getID()." not found");
	}

	public function deleteUser($user){
		if(!($user instanceof User)){
			die("can't delete invalid user object");
		}
		if(!$this->users->containsKey($user->getID())){
			print("won't delete ".$user->getID().", user not found.");
			return;
		}
	}
	public function getAll(){
		return $this->users->values();
	}
}