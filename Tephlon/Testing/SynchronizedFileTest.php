<?php

require_once("../lib/StatisticsSynchronizedFile.php");
require_once('../../SimpleTest/simpletest/autorun.php');

class SynchronizedFileTest extends UnitTestCase{
    private $fpath = "./lock";
    private $sf; 
    function __construct(){
    	$this->sf = new StatisticsSynchronizedFile($this->fpath);
    }
    function testWrite(){
        $w = $this->sf->write("simpletest");
        $this->assertTrue($w, "write op didnt return true.");
        $r = $this->sf->read();
        $this->assertEqual($r, "simpletest", "read didnt match what I wrote.");
    }
}
