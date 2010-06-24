<html> 
<head> 
   <title>TCounterTest</title> 
</head> 
 
<body> 
<?php 
/**
 * Please view this page from your browser. 
 */
if(isset($_REQUEST['inc'])){
	require_once '../Tephlon.php';
	$c = new TCounter('ctrTest'); 
	$c->inc();
	echo $c->getCtr();
	die();
}
echo "<h1>Atomic counter demo. No same numbers should appear.</h1>";
$url = $_SERVER['PHP_SELF']."?inc=1";
for($i=0; $i<20; $i++){
echo    '<iframe style="width:4em; height:4em; overflow:hidden;" src="'.
        $url.
        '"></iframe>
        ';
}?>
  
</body> 