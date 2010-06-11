<?php
require_once '../DataStructures/TBuffers/TBuffer_FIFO.php';
require_once('simpletest/autorun.php');

class TBuffer_FIFO_test extends UnitTestCase{
	protected $buf = null;
	function testCreate(){
		$this->buf = new TBuffer_FIFO($this);
		$this->assertTrue($this->buf instanceof TBuffer_FIFO, "bad instance");
	}
	function testAdd(){
		$this->buf->setTbufferSize(3);
		$this->buf->setDefaultStaleAge(0);
		$this->assertTrue($this->buf->add("first"), "add should return true");
		$this->buf->add("second");
		$this->buf->add("third");
	}
	function testSize(){
		$r = $this->buf->size();
		$this->assertTrue($r == 3, "added 3 items, size was $r. BufSize was 3");
	}
	function testIsSortedGetAll(){
		$r = $this->buf->getAll();
		$this->assertTrue(is_array($r), "getAll should return array");
		$this->assertTrue(count($r) == 3, "getAll should return 3 items");
		$this->assertTrue($r[0] == "first", "first element should contain first, $r[0] found");
		$this->assertTrue($r[1] == "second", "second element should contain second, $r[1] found");
		$this->assertTrue($r[2] == "third", "third element should contain third, $r[2] found");
	}

	function testPopOnAdd(){
		$this->assertTrue($this->buf->add("fourth"), "add should return true");
		$r = $this->buf->size();
		$this->assertTrue($r == 3, "added 4 items, size was $r. BufSize was 3");
		$r = $this->buf->getAll();
		$this->assertTrue(is_array($r), "getAll should return array");
		$this->assertTrue(count($r) == 3, "getAll should return 3 items");
	}
	// FIFO SPECIFIC
	function testWhichPopped(){
		$r = $this->buf->getAll();
		$this->assertTrue($r[0] == "second", "first element should contain second, $r[0] found");
		$this->assertTrue($r[2] == "fourth", "third element should contain fourth, $r[2] found");
	}
	function testClear(){
		$r = $this->buf->size();
		$this->assertTrue($r > 0 , "size should be bigger than zero before clear");
		$this->assertTrue($this->buf->clear(), "clear should return true");
		$r = $this->buf->size();
		$this->assertTrue($r == 0 , "size should be zero after clear");
	}
	function testNextPops(){
		$this->testAdd(); // now should have 3 objects in buffer
		$this->testSize(); // Check again we got 3 objects
		$this->r = $this->buf->next();
		$size = $this->buf->size();
		$this->assertTrue($size == 2, "next(): should have eliminated an object, size was $size, expected 2");
	}
	// FIFO SPECIFIC
	function testServe(){
		$this->assertTrue($this->r == "first", "next(): the one to pop should be 'first'");
		$arr = $this->buf->getAll();
		$this->assertTrue($arr[count($arr) - 1] == "third", "now we should have had 2 objects first and second");
	}
}
