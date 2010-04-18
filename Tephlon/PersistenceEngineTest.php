<?php
/**
 * Test class for generic PersistenceEngine
 */

require_once('../simpletest/autorun.php');
require_once('PHPSerializationPersistenceEngine.php');

class PersistenceEngineTest extends UnitTestCase {
	private $pe = null;
	private $testString = "0123456789";
	private $testLabel = "test_label";
	
	function clear(){
		include("clear_cache.php");
	}
	function testInitialization(){
		$this->pe = PHPSerializationPersistenceEngine::getInstance();
		$this->assertIsA($this->pe,"PersistenceEngine");
		$this->assertIsA($this->pe,"PHPSerializationPersistenceEngine");	
	}
	function testRegisterRetrieveInSameContext(){
		$this->clear();
		// Cache miss
		$result = $this->pe->register($this->testString, $this->testLabel);
		$this->assertNotNull($result, "Result of register was null");
		echo "MISS: Trying to fetch (same context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertEqual($result, $this->testString, "\"$result\" was retrieved, expected $testString");
		
		// Cache hit
		echo "HIT: Trying to fetch (same context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertEqual($result, $this->testString, "\"$result\" was retrieved, expected $testString");
	}
	function testRetrieveInDifferentContext(){
		// The retrieve now is called from a different trace context
		echo "Trying to fetch (different context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertNull($result,"Not null was retrieved for previously registered label");
	}
	function testLifetimeOfRecords(){
		$this->clear();
		$key = $this->pe->register($this->testString, $this->testLabel,1);
		$this->assertNotNull($key, "Result of register was null (key expected)");
		sleep(2);
		echo "STALE: Trying to lately fetch ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertNull($result, "A stale record was retrieved: $key");
		
	}
}