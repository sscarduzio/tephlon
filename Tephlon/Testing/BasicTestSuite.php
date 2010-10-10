<?php
require_once 'simpletest/autorun.php';

class BasicTestSuite extends TestSuite {
	
	function __construct(){
		parent::TestSuite("All Generic Tephlon Tests");
		$tFiles = glob("*Test.php");
		foreach ( $tFiles as $f ){
			echo "adding $f";
			parent::addFile($f);
			parent::run(new TextReporter());
		}
	}
}