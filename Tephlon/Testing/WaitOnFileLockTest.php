<?php
//
///**
// * Test class to verify mutex on file lock (may vary with OSes)
// */
//ob_implicit_flush(true);
//require_once('../Tephlon.php');
//$fpath = "./lock";
//$ltime = time();
//$fp = fopen($fpath,"w");
//$atime = 0;
//if(isset($_REQUEST['read'])){
//	flock($fp, LOCK_SH);
//	$x = time()-$ltime;
//	sleep(13);
//	die("read in ".$x);
//}
//if(flock($fp, LOCK_EX)){
//	// If i managed to take the lock i'm first request
//	$x =time()-$ltime;
//	echo "Access to write in ".$x;
//	sleep(13);
//}
//else{
//	dlog("it was locked :(",ERROR);
//}
//
//require_once('../../SimpleTest/simpletest/autorun.php');
//
//class WaitOnFileLockTest extends UnitTestCase{
//	private $fpath = "./lock";
//
//	function atestObtainLock(){
//		$fp = fopen($this->fpath,"w");
//		if(flock($fp, LOCK_EX | LOCK_NB)){
//			// If i managed to take the lock i'm first request
//			$i = (int) file_get_contents($this->fpath);
//			echo "first request! n=".$i;
//			file_put_contents($this->fpath, $i+1);
//			echo "I wrote ".file_get_contents($this->fpath);
//			sleep(10);
//			fclose($fp);
//		}
//		else{
//			// Could not get lock, I'm second request.
//			flock($fp, LOCK_EX);
//			$i = (int) fread($fp,1);
//			echo "second request! n=".$i;
//			fwrite($fp,1+$i."",1);
//			fclose($fp);
//		}
//	}
//}
