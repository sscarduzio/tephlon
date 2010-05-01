<?php
/**
 * This class represents the twitter status flow of a certain user.
 * A Flow contains N status strings. 
 * 
 * @author Simone
 *
 */

require_once("TephlonDT.php");

class Flow extends TephlonDT{
	protected $owner_id;
	// Associative array  "timestamp" => "status string"
	protected $statuses;

	public function __construct($owner_id){
		$this->statuses = $this->tephlonInit($this, $owner_id);
		$this->owner_id = $owner_id;
		if(!$this->statuses){
			$this->statuses = array();
		}
	}
	public function addStatus($status){
		if(!is_string($status) || strlen($status) < 1){
			die("invalid status, must be string and not empty.");
		}
		$this->statuses[time().uniqid("_")] = $status;
		$this->tephlonSave($this->statuses);
	}
	public function getAll(){
		return $this->statuses;
	}

	public function deleteStatus($which){
		if($which == "all"){
			$this->statuses = array();
			$this->tephlonSave(array());
		}
		if(!$which || in_array($which, $this->statuses)){
			die("delete what status?");
		}
		unset($this->statuses[$which]);
		$this->tephlonSave($this->statuses);
	}

}