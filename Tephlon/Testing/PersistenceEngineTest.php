<?php
/**
 * Test class for generic PersistenceEngine
 */

require_once('simpletest/autorun.php');
require_once('../Tephlon.php');

class PersistenceEngineBasicTest extends UnitTestCase {
	public $pe = null;
	public $testString = "0123456789";
	public $testLabel = "test_label";


	function testInitialization(){
		$this->pe = Tephlon::getResource($this);
		$this->pe->clear();
		// Get rid of test records pretty soon, please
		$this->pe->setLifetime(10);
		$this->assertIsA($this->pe,"PersistenceEngine");
	}
	function testGlobalContextInitialization(){
		$this->pe = Tephlon::getResource();
		// Get rid of test records pretty soon, please
		$this->pe->setLifetime(20);
		$this->assertIsA($this->pe,"PersistenceEngine");
	}
	function testRegisterRetrieveInSameContext(){
		//$this->clear();
		// Cache miss
		$result = $this->pe->register($this->testString, $this->testLabel);
		$this->assertNotNull($result, "Result of register was null");
		echo "MISS: Trying to fetch (same context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertEqual($result, $this->testString, "\"$result\" was retrieved, expected $this->testString");

		// Cache hit
		echo "HIT: Trying to fetch (same context) ".$this->testLabel."\n";
		$result = $this->pe->retrieve($this->testLabel);
		$this->assertEqual($result, $this->testString, "\"$result\" was retrieved, expected $this->testString");
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
		$this->assertNull($result,"Not null ($result) was retrieved for previously registered label $this->testLabel (now is:".time().").");
		$x = "lOL";
	}
}
class PersistenceEngineCRUDTest extends UnitTestCase{
	private $pe = null;
	private $testString = "A0123456789";
	private $newTestString = "ABCDEFGHILM";
	private $testLabel = "test_label";

	function runTests($pe = null){
		$this->pe = $pe;
		$this->testCreate();
		$this->testUpdate();
		$this->testDelete();
	}
	function testCreate(){
		if(is_null($this->pe)){
			$this->pe = Tephlon::getResource();
		}
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
		$this->assertNotNull($obj, "Unable to retrieve a record which was registered in another method");
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
class invalidLabelsTest extends UnitTestCase{
	private $str201 = null;
	private $pe = null;

	function __construct(){
		$a="";
		for($i = 0; $i < 201; $i++){
			$a.="a";
		}
		$this->str201 = $a;
	}
	function testLongResourceLabel(){
		$this->pe = Tephlon::getResource($this->str201);
		$this->assertFalse($this->pe,"Too long resource label should return false");
	}
	function testWrongCharResourceLabel(){
		$this->pe = Tephlon::getResource("?test");
		$this->assertFalse($this->pe,"Invalid character in resource label, should return false");
	}
	function testLegalResourceLabel(){
		$this->validRecLabelExpect = array(
          "register"  => "some_label",
          "retrieve"  => "some_content",
          "exists"    =>  true,
          "delete"    =>  true
		);

		$this->pe = Tephlon::getResource($this);
		$this->assertTrue($this->pe instanceof PersistenceEngine,
		  "legal object resource label should have returned instance of PersistenceEngine!");
		$this->pe->clear();
		$r = $this->bulkTestRecordLabelMethods("some_label", $this->validRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing with label = OBJECT");

		$this->pe = Tephlon::getResource("my_nice_perfect_label");
		$this->assertTrue($this->pe instanceof PersistenceEngine,
		  "legal string resource label should have returned instance of PersistenceEngine!");
		$this->pe->clear();
		$r = $this->bulkTestRecordLabelMethods("some_label", $this->validRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing with label = STRING");

		$this->pe = Tephlon::getResource(0);
		$this->assertTrue($this->pe instanceof PersistenceEngine,
		  "legal Numeric resource label should have returned instance of PersistenceEngine!");
		$this->pe->clear();
		$r = $this->bulkTestRecordLabelMethods("some_label", $this->validRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing with label = INT (0)");

		$this->pe = Tephlon::getResource(1);
		$this->assertTrue($this->pe instanceof PersistenceEngine,
		  "legal Numeric resource label should have returned instance of PersistenceEngine!");
		$this->pe->clear();
		$r = $this->bulkTestRecordLabelMethods("some_label", $this->validRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing with label = INT (1)");

	}
	function bulkTestRecordLabelMethods($labelToTest, $expected_return){
		$r = array();
		$failed = 0;
		$r['register'] = $this->pe->register("some_content", $labelToTest);
		$r['retrieve'] = $this->pe->retrieve($labelToTest);
		$r['exists'] = $this->pe->exists($labelToTest);
		$r['delete'] = $this->pe->delete($labelToTest);
		foreach($r as $method => $res){
			if(!($expected_return[$method] === $res)){
				dlog("Fail: $method returned $res instead of ".
				$expected_return[$method], ERROR);
				$failed++;
			}
		}
		return $failed;
	}
	function testLongRecordLabel(){
		$this->pe = Tephlon::getResource($this);

		$this->invalidRecLabelExpect = array(
		  "register"  => null,
		  "retrieve"  => null,
		  "exists"    =>  null,
		  "delete"    =>  null
		);
		$r = $this->bulkTestRecordLabelMethods($this->str201, $this->invalidRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing long record labels");
	}
	function testWrongCharRecordLabel(){
		$r = $this->bulkTestRecordLabelMethods("/wrongChar", $this->invalidRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing wrong char record labels");
		$r = $this->bulkTestRecordLabelMethods("\wrongChar", $this->invalidRecLabelExpect);
		$this->assertTrue($r == 0, "$r issues found testing wrong char record labels");
	}
}

class testGetLastModified extends UnitTestCase{
	function testLastModifiedCreationTime(){
		$this->pe = Tephlon::getResource($this);
		$this->pe->register("BLABLA","label",0);
		$cdate = time();
		sleep(2);
		$r = $this->pe->getLastModified("label");
		$isAttendable = ($r - $cdate) < 2 ? true : false;
		$this->assertTrue($isAttendable, "Problem w/ getLastModified".
		": record created at $cdate, function returned $r ");
	}
	function testLastModifiedEditTime(){
		$this->pe = Tephlon::getResource($this);
		$this->pe->register("BLABLA","label2",0);
		$ctime = time();
		sleep(2);
		$this->pe->register("BLABLABLA","label2",0);
        $etime = $this->pe->getLastModified("label2");
		sleep(2);
		$r = $this->pe->getLastModified("label2");
		$isAttendable = true;
		$failedConditions = "";
		// 1. Edit time is bigger than creation time
		$isAttendable &= $ctime < $etime;
		if(!$isAttendable){
			$failedConditions .= "1 ";
		}
		// 2. Edit time stays the same when I request it after 2 sec
		$isAttendable &= $etime == $r;
		if(!$isAttendable){
			$failedConditions .= "2 ";
		}
		// 3. Lately requested Edit time is in the past.
		$isAttendable &= $r < time();
		if(!$isAttendable){
			$failedConditions .= "3 ";
		}
		$this->assertTrue($isAttendable, "Did not pass conditions: ".
		 "$failedConditions  - ctime: $ctime, etime: $etime, latelyReqEditTime (r): $r");
	}
}