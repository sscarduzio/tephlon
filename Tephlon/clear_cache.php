<?php

$files = glob("cache/PSPE/*");
foreach($files as $f){
	echo "CC: deleting $f\n";
	unlink($f);
}
//echo "Clearing cache directory...";