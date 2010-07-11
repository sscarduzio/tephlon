<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'../lib/Mutex/Mutex.class.php';
$m = new Mutex('allo');
echo $m->getMutexType();
$fiction = true;

if($fiction === false){
	$x = new Mutex('allo');
	$x->lock();
	echo ("Yay");
	$x->unlock();
}
$m->lock();
echo "locked";
$m->unlock(); 
echo "unlocked";
unset($m);
