<?php
require_once '../../Tephlon.php';

$c = new TCounter('myCtr');
$c->inc();
echo "You are the visitor number: ". $c->getCtr(); 