<?php
require_once '../DataStructures/TMap.php';
require_once('simpletest/autorun.php');


class TMapTest extends UnitTestCase {
	private $m;
	private $dimension = 20;
	function __construct(){
		$this->m = new TMap($this);
	}
	function testPut($k = null, $v = null){
		if(is_null($k)){
			$r = $this->m->put('pippo','franco');
			$this->assertTrue($r,"put returned not true");
		}
		else{
			$r = $this->m->put($k, $v);
			$this->assertTrue($r,"put returned not true");
		}
	}
	function testGet(){
		$r = $this->m->get('pippo');
		$this->assertEqual($r,'franco',"failed to get: $r");
	}
	function testClear(){
		$r = $this->m->clear();
		$this->assertTrue($r, "clear returned non true: $r");
	}
	function testSize(){
		$this->testClear();
		$sz = $this->dimension;
		for($i = 0; $i < $sz; $i++){
			$this->m->put("k$i", "v$i");
		}
		$r = $this->m->size();
		$this->assertEqual($r,$sz,"inserted items($sz) != measured size($r)");
	}
	function testValues(){
		$r = $this->m->values();
		$this->assertTrue(is_array($r),"values returns not an array: $r");
		$ct = count($r);
		$this->assertEqual($ct,$this->dimension,"values count ($ct) != inserted values $this->dimension");
	}
	function testGetAll(){
		$r = $this->m->getAll();
		$this->assertTrue(is_array($r),"getAll() returns not an array: $r");
		$ct = count($r);
		$this->assertEqual($ct,$this->dimension,"getAll() count ($ct) != inserted values $this->dimension");
		$this->assertTrue($this->is_assoc($r), "getAll() gives a non-associative array.");
	}
	function testContainsKey(){
		$r = $this->m->containsKey('k0');
		$this->assertTrue($r, "containsKey() did not found key 'k0', returned $r");
		$r = $this->m->containsKey('WONT_FIND_THIS');
		$this->assertFalse($r, "containsKey() found key unexisting key, returned $r");
	}
	function testIsEmpty(){
		$r = $this->m->isEmpty();
		$this->assertFalse($r, "isEmpty should have returned false, ret: $r");
		$this->m->clear();
		$r = $this->m->isEmpty();
        $this->assertTrue($r, "isEmpty should have returned true, ret: $r");
	}
	function testRemove(){
		$this->testPut('testremove', 'content');
		$r = $this->m->size();
		$this->assertEqual($r, 1, "size() now should be 1");
		$r = $this->m->remove('testremove');
		$this->assertTrue($r, "remove() returned non true: $r");
		$r = $this->m->size();
        $this->assertEqual($r, 0, "size() now should be 0");
        $r = $this->m->isEmpty();
        $this->assertTrue($r,"should be empty");
	}
	
	function is_assoc($_array) {
		if ( !is_array($_array) || empty($_array) ) {
			return false;
		}
		foreach (array_keys($_array) as $k => $v) {
			if ($k !== $v) {
				return true;
			}
		}
		return false;
	}
}