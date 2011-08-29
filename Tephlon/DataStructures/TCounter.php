<?php
/**
 * This class demonstrates the simplest and most descriptive use of atomic
 * operations.
 * 
 * We have two core functions in tephlon that enable atomic operations:
 * atomicBegin($label) and atomicEnd($label).
 * They respectively catch and release a mutex on a record by referring to its 
 * label.
 * 
 * Even without explicit atomic calls, Tehplon will anyway support mutex writes.
 * But the only atomic thing would be the write, not the read for example.
 * 
 * This counter is completely synchronized. For additional protection from race conditions
 * the mutex object is not the same file that contains the data, but another temporary
 * file associated to real record's label.
 */

require_once("TephlonType.php");

class TCounter extends TephlonType {

	public function __construct($that){
		parent::__construct($that);
	}

	public function inc($n = null){
		if(is_null($n)){
			$n = 1;
		}
		if(!is_int($n)){
			self::$log->error("Can't increment of a non integer value");
			return false;
		}
		$x = $this->tr->retrieve("ctr", 0);

		$this->tr->atomicBegin("ctr");

		/* synchronized */
			$val = $this->tr->retrieve("ctr");
			if(!is_int($val)){
				$val = 0;
			}
			$this->tr->register($val+$n, "ctr");
		/* synchronized */
		
		$this->tr->atomicEnd("ctr");
	}
	public function reset(){
		$this->tr->atomicBegin("ctr");
        /* synchronized */
            $this->tr->register(0, "ctr");
        /* synchronized */
        $this->tr->atomicEnd("ctr");
	}
	public function getCtr(){
		return $this->tr->retrieve("ctr");
	}
}