<?php
require_once("../../Tephlon.php");


$bb = new BillBoard();
$l = new Line();
$l->time = time();
$l->text = "hello world<br>";
$bb->addLine($l);
$bb->addLine($l);
$bb->addLine($l);
$bb->addLine($l);
$x = $bb->getLines();

for($i = 0; $i < count($x); $i++){
	$line =  $i.") ".date("h:i:s",$x[$i]->time). " " . $x[$i]->text . "\n";
	echo $line;
}
//print_r($x);