<?php
/**
 * Test class for generic PersistenceEngine
 */

require_once('simpletest/autorun.php');
require_once('../Tephlon.php');

class PersistenceEngineBasicTest extends UnitTestCase {
	private $pe = null;
	private $testString = "0123456789";
	private $testLabel = "test_label";

	function clear(){
		include("clear_cache.php");
	}
	function testInitialization(){
		$this->pe = Tephlon::getResource($this);
		// Get rid of test records pretty soon, please
		$this->pe->setStaleAge(10);
		$this->assertIsA($this->pe,"PersistenceEngine");
		$this->assertIsA($this->pe,"PHPSerializationPersistenceEngine");
	}
	function testRegisterRetrieveInSameContext(){
		//$this->clear();
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

	function testLifetimeOfRecords(){
		//$this->clear();
		$key = $this->pe->register($this->testString, $this->testLabel, 1);
		$this->assertNotNull($key, "Result of register was null (key expected)");
		sleep(2);
		echo "STALE: Trying to lately fetch ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertNull($result, "A stale record was retrieved: $key");

	}
}

class PersistenceEngineKeyCollisionTest extends UnitTestCase{
	private $pe = null;
	private $testString = "0123456789";
	private $testLabel = "test_label";
	
	function testRetrieveInDifferentContext(){
		$this->pe = Tephlon::getResource();
		// The retrieve now is called from a different trace context
		echo "Trying to fetch (different context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertNull($result,"Not null was retrieved for previously registered label");
	}
}
class PersistenceEngineCRUDTest extends UnitTestCase{
	private $pe = null;
	private $testString = "A0123456789";
	private $newTestString = "ABCDEFGHILM";
	private $testLabel = "test_label";
	
	function testCreate(){
		$this->pe = Tephlon::getResource();
		$result = $this->pe->retrieve($this->testLabel.time());
		$this->assertNull($result,"Freshly created record is not null");
		// Test the retrieve default value
		$result = $this->pe->retrieve($this->testLabel,$this->testString);
		$this->assertEqual($this->testString,$result, "Default parameter of retrieve() does not work");
	} 	
	function testRead(){
		// Already tested
	}
	function testUpdate(){ 
		$obj = $this->pe->retrieve($this->testLabel);
		$this->assertNotNull($obj, "Could not retrieve record set in another method");
		$obj = $this->newTestString;
		
		$this->pe->register($obj , $this->testLabel);
		// Now result contains the label
		$updated = $this->pe->retrieve($this->testLabel);
		$this->assertEqual($updated, $obj, "Update failed");
	}
	function testDelete(){
		$this->pe->delete($this->testLabel);
	}
}