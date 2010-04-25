<?php
require_once('simpletest/autorun.php');
require_once('../Tephlon.php');

class PerformanceTest extends UnitTestCase {
	private $t;
	private $testString;
	private $testLabel = "test_label";
	private $records;
	private $strSize;

	function writeManyStringRecords(){
		// Building tring
		$this->testString = null;
		for($i = 0; $i < $this->strSize ; $i++){
			$this->testString = $this->testString."x";
		}
		dlog("String written",true);
		$this->t = Tephlon::getResource();

		$time_start = microtime(1);
		for($i = 0; $i < $this->records ; $i++){
			$res = $this->t->register($this->testString, $this->testLabel.$i, 1);
		}
		$time_end = microtime(1);

		$time_elapsed = $time_end - $time_start;
		echo printf("String (%d ch):\t\tRecords: %d written\t\tin %f seconds\n",
		strlen($this->testString), $this->records, $time_elapsed);
	}

	function cleanManyStringRecords(){
		$time_start = microtime(1);
		$this->t->clean();
		$time_end = microtime(1);
		$time_elapsed = $time_end - $time_start;
		echo printf("String (%d ch):\t\tRecords: %d cleaned\t\tin %f seconds\n",
		strlen($this->testString), $this->records, $time_elapsed);
	}
	function test_1Kstrl_1M_records(){
		$this->strSize = 10*1000;
		$this->records = 1000;
		$this->writeManyStringRecords();
		$this->cleanManyStringRecords();
	}
	function test_1Mstrl_1K_records(){
		$this->strSize = 1000;
		$this->records = 10*1000;
		$this->writeManyStringRecords();
		$this->cleanManyStringRecords();

	}

}