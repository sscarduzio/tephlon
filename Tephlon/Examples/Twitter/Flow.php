<?php
/**
 * This class represents the twitter status flow of a certain user.
 * A Flow contains N status strings.
 *
 * @author Simone
 *
 */

require_once("../../Tephlon.php");

class Flow {
	protected $owner_id;
	// Associative array  "timestamp" => "status string"
	protected $statuses;

	public function __construct($owner_id){
		// Set owner id BEFORE creating TMap
		// so we pass a different "$this" for
		// each owner_id!
		$this->owner_id = $owner_id;
		$this->statuses = new TMap($this);
		if(!$this->statuses){
			die("could not create TMap!");
		}
	}
	public function addStatus($status){
		// Status ID is a timestamp
		$statusID = time(); sleep(1);
		if(!$this->statuses->put($statusID, $status)){
			die("invalid status, must be string and not empty.");
		}
	}
	public function getAll(){
		return $this->statuses->getAll();
	}

	public function deleteStatus($statusID){
		if($this->statuses->containsKey($statusID)){
			die("statusID not valid / not found");
		}
		$this->statuses->remove($statusID);
	}
	public function count(){
		return $this->statuses->size();
	}
	public function clear(){
		$this->statuses->clear();
	}

}