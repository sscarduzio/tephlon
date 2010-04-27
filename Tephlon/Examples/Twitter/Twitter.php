<?php
require_once("UserBoard.php");

// Setup a new user
$u1 = new User("usr_".time(),"dummypass");
$u1->setName("Dummy user");

// Add it to the userboard
$ub = new UserBoard();
$ub->addUser($u1);

// Get the flow for the user, fill it with a status msg
$u1flow = new Flow($u1->getID());
$u1flow->addStatus("Wow it's so exciting ommayggod!");
sleep(1);
$u1flow->addStatus("Now I'm really bored");
sleep(1);
$u1flow->addStatus("Tomorrow I kill @the_sckr's dog.");

// Recall all the users from the userboard and iterate their statuses
$users = $ub->getAll();
date_default_timezone_set("EET");

foreach ($users as $u){
	$uflow = new Flow($u->getID());
	$ustatuses= $uflow->getAll();
	if(count($ustatuses) > 0){
		echo "Status flow of: ".$u->getID()." (".$u->getName().")\n";
		foreach($ustatuses as $time => $status){
			echo "\t".date("d/m/y @ H:i:s",$time).": $status\n";
		}
	}else echo "stale user!";
}