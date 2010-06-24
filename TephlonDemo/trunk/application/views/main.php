<?php
$the_errors='';
if(count($errors) > 0){
    foreach($errors as $e){
        $the_errors.= '<div class="error">'.$e.'</div>';
    }
}
if(!isset($codeWidth)){
	$codeWidth = null;
}
?>

<?php doctype('xhtml1-trans')?>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head profile="http://gmpg.org/xfn/11"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
<title><?php echo $title ?>  | Tephlon Demo</title> 
 
<?php echo link_tag('index.php/css/index/'.$codeWidth)?>

<!--[if lte IE 8]>
    <?php echo link_tag('index.php/css/ie')?>
<![endif]--> 
<?php echo js_asset('jquery.min.js')?>
<?php if(isset($assets)) echo $assets?>

</head>
<body>
<div id="wrap">
<div id="header" class="separator">
<h1>Tephlon Demo</h1>
</div>
<div id="top" class="separator">
<?php echo $top?>
</div>
<div id="code">

<p><?php echo $code?></p>
</div>
  
<div id="window">
    <div><?php echo $the_errors?></div>
	<?php echo $window?>
</div>
<div id="footer">
&nbsp;
</div>
</div>

</body>
</html>
