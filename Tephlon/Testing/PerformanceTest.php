<?php
require_once('simpletest/autorun.php');
require_once('../Tephlon.php');

class PerformanceTest extends UnitTestCase {
	private $t;
	private $testString;
	private $testLabel = "test_label";
	private $records;
	private $strSize;
	
	function __construct(){
		$this->t = Tephlon::getResource($this);
	}
	function writeManyStringRecords(){
		// Building tring
		$this->testString = null;
		for($i = 0; $i < $this->strSize ; $i++){
			$this->testString = $this->testString."x";
		}
		dlog("String written",true);
		$time_start = microtime(1);
		for($i = 0; $i < $this->records ; $i++){
			$res = $this->t->register($this->testString, $this->testLabel.$i, 1);
		}
		$time_end = microtime(1);

		$time_elapsed = $time_end - $time_start;
		echo printf(":\t\tWRITE:\t%d Records\t(strlen: %d)\tin in %f seconds\t[%f RPS]\n",
		 $this->records, strlen($this->testString), $time_elapsed, $this->records/$time_elapsed);
	}

	function cleanManyStringRecords(){
		$time_start = microtime(1);
		$this->t->clean();
		$time_end = microtime(1);
		$time_elapsed = $time_end - $time_start;
		// echo printf(":\t\tDELETE:\t%d Records\t(strlen: %d)\tin in %f seconds\t[%f RPS]\n",
		// $this->records, strlen($this->testString), $time_elapsed, $this->records/$time_elapsed);
	}
	function test_1Kstrl_1M_records(){
		$this->strSize = 10;
		$this->records = 100;
		$this->writeManyStringRecords();
		$this->cleanManyStringRecords();
	}
	// Skipped with "a" in front of method name..
	function atest_1Mstrl_1K_records(){
		$this->strSize = 100;
		$this->records = 1000;
		$this->writeManyStringRecords();
		$this->cleanManyStringRecords();

	}

}