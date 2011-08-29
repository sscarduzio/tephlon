<?php
/**
 * Test class for generic PersistenceEngine
 */
define('MY_BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
require_once('simpletest/autorun.php');
require_once(MY_BASE_PATH .'/../Tephlon.php');

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
	// To be called manually!!
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
	function __construct(){
		parent::__construct();
		$this->pe = Tephlon::getResource($this);
		$this->pe->clear();
	}
	function testLastModifiedCreationTime(){
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
		$createTime = time();
		sleep(2);
		$this->pe->register("BLABLABLA","label2",0);
		$editTime = $this->pe->getLastModified("label2");
		sleep(2);
		$r = $this->pe->getLastModified("label2");
		$isAttendable = true;
		$failedConditions = "";
		// 1. Edit time is bigger than creation time
		$isAttendable &= $createTime < $editTime;
		if(!$isAttendable){
			$failedConditions .= "1 ";
		}
		// 2. Edit time stays the same when I request it after 2 sec
		$isAttendable &= $editTime == $r;
		if(!$isAttendable){
			$failedConditions .= "2 ";
		}
		// 3. Lately requested Edit time is in the past.
		$isAttendable &= $r < time();
		if(!$isAttendable){
			$failedConditions .= "3 ";
		}
		$this->assertTrue($isAttendable, "Did not pass conditions: ".
		 "$failedConditions  - createTime: $createTime, editTime: $editTime, latelyReqEditTime (r): $r");
	}
}
class testIndex extends UnitTestCase{
	function __construct(){
		parent::__construct();
		$this->pe = Tephlon::getResource($this);
		$this->pe->clear();
	}
	private $pe = null;
	function testEmptyIndex(){
		$r = $this->pe->getIndex();
		$this->assertTrue(is_array($r), "Index was not an array!");
		$this->assertTrue(count($r) === 0, "Freshly created resource's index size was not INT 0. Found:".count($r));
	}
	function testSingleIndex(){
		$this->pe->register($this, "this_class");
		$r = $this->pe->getIndex();
		$this->assertTrue(is_array($r), "Index was not an array!");
		$this->assertTrue(count($r) === 1, "Registered one element, resource's index size was not INT 1. Found:".count($r));
		$this->assertEqual($r[0], "this_class","The 0th element of Index array should be my label 'this class', but I found:".$r[0]);
	}
	function testMultiIndex(){
		$this->pe->register($this, "this2");
		$this->pe->register($this, "this3");
		$this->pe->register($this, "this4");
		$r = $this->pe->getIndex();
		$this->assertTrue(is_array($r), "Index was not an array!");
		$this->assertTrue(count($r) === 4, "Registered 4 elements, resource's index size was not INT 4. Found:".count($r));
		$this->assertEqual($r[1], "this2","The 1st element of Index array should be my label 'this2', but I found:".$r[1]);
		$this->assertEqual($r[2], "this3","The 2nd element of Index array should be my label 'this3', but I found:".$r[2]);
	}
	// Not really a test
	function testCleanup(){
		$this->pe->clear();
	}

}