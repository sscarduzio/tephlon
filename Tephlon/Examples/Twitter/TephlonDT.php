<?php
/**
 * Tephlon data type
 * 
 * @author Simone
 *
 */
require_once("../../Tephlon.php");
class TephlonDT {
	protected $tephlon_label;
	protected $lifetime = 0;
	protected $tr;

	protected function tephlonInit($that, $label, $default = null){
		if(!$label){
			die("TephlonDT: need a label to init!");
		}
		$this->tr = Tephlon::getResource($that);
		$this->tephlon_label = $label;
		$obj = $this->tr->retrieve($label, $default);
		return $obj;
	}

	protected function tephlonSave($object){
		if(!$this->tephlon_label){
			die("TephlonDT data type uninitialized");
		}
		return $this->tr->register($object,$this->tephlon_label, $this->lifetime);
	}
}