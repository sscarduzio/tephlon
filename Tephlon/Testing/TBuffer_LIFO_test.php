<?php
require_once('TBuffer_FIFO_test.php');
require_once '../DataStructures/TBuffers/TBuffer_LIFO.php';

abstract class TBuffer_LIFO_test extends TBuffer_FIFO_test{
	function testCreate(){
		$this->buf = new TBuffer_LIFO($this);
		$this->assertTrue($this->buf instanceof TBuffer_LIFO, "bad instance");
	}
	function testDummySuperclassMethodCaller(){
		$this->testAdd();
		$this->testSize();
		$this->testIsSortedGetAll();
		$this->testPopOnAdd();
	}
	function testWhichPopped(){
		$r = $this->buf->getAll();
		$this->assertTrue($r[0] == "fourth", "first element should contain fourth, $r[0] found");
		$this->assertTrue($r[2] == "fourth", "third element should contain fourth, $r[2] found");
	}
    function testDummySuperclassMethodCaller2(){
    	$this->testClear();
    	$this->testNextPops();
    }
	function testServe(){
		$this->assertTrue($this->r == "third", "next(): the one to pop should be 'third'");
		$arr = $this->buf->getAll();
		$this->assertTrue($arr[count($arr) - 1] == "second", "now we should have had 2 objects first and second");
	}
}