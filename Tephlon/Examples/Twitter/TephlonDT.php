<?php
/**
 * Tephlon data type
 * 
 * @author Simone
 *
 */
require_once("../../PHPSerializationPersistenceEngine.php");
class TephlonDT {
	protected $tephlon_label;
	protected $lifetime = 0;

	protected function tephlonInit($label){
		if(!$label){
			die("TephlonDT: need a label to init!");
		}
		$pe = PHPSerializationPersistenceEngine::getInstance();
		$this->tephlon_label = $label;
		return $pe->retrieve($label);
	}

	protected function tephlonSave($object){
		$pe = PHPSerializationPersistenceEngine::getInstance();
		if(!$this->tephlon_label){
			die("TephlonDT data type uninitialized");
		}
		return $pe->register($object,$this->tephlon_label, $this->lifetime);
	}
}