<?php

/**
 * Test class for Logger
 */

require_once('simpletest/autorun.php');
require_once('../Tephlon.php');

class LoggerTest extends UnitTestCase{
	function trySetVerbosity($val, $shouldbe){
		$l = Logger::getInstance();
		if(!$shouldbe){
			$this->assertFalse($l->setVerbosity($val), "Verbosity $val should be invalid!");
		}
		else{
		  $this->assertTrue($l->setVerbosity($val), "Verbosity $val should be valid!");
		}
	}
	function testSetVerbosity(){
		$this->trySetVerbosity(0,false);
		$this->trySetVerbosity(4,false);
		$this->trySetVerbosity("2",false);
		$this->trySetVerbosity("a",false);
		$this->trySetVerbosity(1,true);
		$this->trySetVerbosity(2,true);
		$this->trySetVerbosity(3,true);
	}
	function testConstants(){
		$this->assertTrue(is_int(DEBUG),"DBG as a constant should refer to int.");
		$this->assertTrue(is_int(INFO),"INFO as a constant should refer to int.");
		$this->assertTrue(is_int(ERROR),"ERR as a constant should refer to int.");
	}
}

/** Test if we print right message in the right moment **/
class LoggerDebugTest extends UnitTestCase {
	private $ll = DEBUG;
	protected $logger = null;
	function __construct(){
		$this->logger = Logger::getInstance();
	}

	function captureLog($str, $ll){
		ob_start();
		dlog($str, $ll);
		$x = ob_get_contents();
		ob_end_clean();
		if(strlen($x) == 0){
			return null;
		}
		return $x;
	}

	function testDebug(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Debug", DEBUG);
		$this->assertNotNull($statement, " Loglevel $this->ll Should print DEBUG statement");
	}
	function testInfo(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Info", INFO);
		$this->assertNotNull($statement, " Loglevel $this->ll Should print INFO statement");
	}
	function testErr(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Err", ERROR);
		$this->assertNotNull($statement, " Loglevel $this->ll Should print ERROR statement");
	}
}


/*****************Test*Info****************************/

class LoggerInfoTest extends LoggerDebugTest {
	private $ll = INFO;

	function testDebug(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Debug", DEBUG);
		$this->assertNull($statement, " Loglevel $this->ll Should NOT print DEBUG statement");
	}
}

/*****************Test*Error****************************/

class LoggerErrorTest extends LoggerDebugTest {
	private $ll = ERROR;

	function testDebug(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Debug", DEBUG);
		$this->assertNull($statement, " Loglevel $this->ll Should NOT print DEBUG statement");
	}
	function testInfo(){
		$this->logger->setVerbosity($this->ll);
		$statement = $this->captureLog("test Info", INFO);
		$this->assertNull($statement, " Loglevel $this->ll Should NOT print INFO statement");
	}
}