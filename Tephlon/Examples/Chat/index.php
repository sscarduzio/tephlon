<?php
require_once("ChatStream.php");

//print_r($_REQUEST);


function getChat($lines){
	$chat ="";
	for($i = 0; $i < count($lines); $i++){
		$line =  '<span class="lineNum">'.($i+1)."</span> ".date("h:i:s",$lines[$i]->time). " ".
                         '<span class="nick">&lt;'.$lines[$i]->nick."&gt;</span> ". $lines[$i]->text . "\n";

		$chat.= '<div class="line">'.$line.'</div>';
	}

	$out ="";
	$out.='
<div id="window">
<div id="chat">'.$chat.'</div>';
	$out.='<div id="typein">
    <form  method="post" action="http://213.243.142.47'.$_SERVER['PHP_SELF'].'">';
	if(!getNick()){
		$out.= 'Choose nickname <input type="text" name="nick" />';
	}
	else{
		$out.= '<span class="nick">&lt;'.getNick().'&gt;</span>'.
		       ' <input class="typeform" type="text" autocomplete="off" name="line" /> ';
	}
	$out.= //'<input class="submit" type="submit" name="submit" value="send" />
   ' </form>
</div>
</div>';
	return $out;
}

function getNick(){
	// Try first to catch nick from GET or POST
	$nick = isset($_REQUEST['nick']) && strlen($_REQUEST['nick']) > 3 ? $_REQUEST['nick'] : false;
	if(!$nick){
		// If no result, search for nick in the cookie
		$nick = isset($_COOKIE['nick']) && strlen($_COOKIE['nick']) > 3 ? $_COOKIE['nick'] : false;
	}
	if($nick){
		// If you found the nick in a way or in the other, save it to cookie.
		setcookie('nick', $nick);
		return $nick;
	}
	else{
		if(isset($_REQUEST['nick'])){
			echo '<span class="error">Invalid nickname provided, minimum 4 chars long required.</span>';
		}
		// Could not find nick anywhere
		return false;
	}
}

$cs = new ChatStream();

// Handle submission
if( isset($_REQUEST['line']) && strlen($_REQUEST['line'])>0){
    echo "ENTERED";
	$x = $_REQUEST['line'];
	$l = new Line();
	$l->time = time();
	$l->text = $x;
	$l->nick = getNick();

	// Found a valid submission, push it to Tephlon TBuffer_FIFO
	$cs->addLine($l);
	// Redirect browser to the main page (avoids accidental resubmission on refresh)
	header("Location: ".$_SERVER['PHP_SELF']);
}

?>
<html>
<head>
<title>Tephlon Demo: Chat using TBuffer_FIFO</title>
<style type="text/css">
*{
    font-family: Palatino, Georgia, 'Times New Roman', serif;
}
h1, h2, h3 {
  padding: 0;
  margin:0.5em 0;
  line-height: 1.3em;
 font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
#header{
   margin-bottom:20px;
    color: #0a0;
    padding: 0.2em 0.5em 0.5 0em; 
    border-bottom: 1px solid #aba;
}
#header h1 {
    
}
#wrap {
    width: 960px;
    margin: 0 auto;
}

#description{
line-height: 1.5em;
    display: inline;
    float: left;
    width:380px;
}
#window {
    display:inline;
    float: right;
    padding:5px;
    width: 500px;
   
   
    border-top: 0px;
    bottom: 20px;
   
}
#typein, #chat{
    width: 90%;
    margin: 10px auto;
    padding: 0.3em;
    
}
.line{
  padding: 0.2em 0em  0.3em 0.2em;
  margin-top:0.2em;
  border-bottom: 1px solid #dedede;
  color: #555;
  font-size:80%;
}
.lineNum {
float: right;
    padding-left: 1em;
    width: 30px;
    color: #999;
    margin-right:-2.8em;
}
.nick {
    margin-left: 0.4em;
    color: #090;
}
#typein{
    border: 0;
    margin-bottom:0;
    padding-bottom:0;
}
#description h2{
color: #666;
border-left: 3 px solid #0a0 ;
padding-left: 0.5em;
}
#description {
    color: #777;
    font-size: 110%;
}
li{
list-style: square;
}
ul,ol {
margin:0; padding:0;
}
.typeform {
width: 300px;
}
form .nick{
margin:0;
padding: 0;
}
#footer {
    clear:both;
    height:100px;
    border-top: 1px solid #0a0;
}
input{
    float: right;
}
</style>
<!-- [if IE]-->
<style type="text/css">
#wrap {
    display:block;
    width:960px;
    margin:0 auto;
}
</style>
<!--endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(".typeform").focus();
});
</script>

</head>
<body>
<div id="wrap">
<div id="header">
<h1>Tephlon Demo</h1>
</div>
<div id="description">
<h2>A chat engine using the data structure TBuffer_FIFO</h2>
<p>TBuffer_FIFO is a data structure in which we can simply add objects (in this example
each text line is an object) until we reach a configured buffer size (in this case 15).
The system will automatically provide to eliminate the oldest objects when
<ul>
<li>Buffer is full and we added a new object (line)</li>
<li>We reduce the buffer size in any moment</li>
</ul>
</p>
<p>In addition to this, the TBuffer is able to provide a chronologically sorted 
non-associative array containing all the objects inside of it (method getAll()).</p>
</div>
<?php 
$lines = $cs->getLines();
$chat = getChat($lines);
echo $chat 
?>
<div id="footer">
&nbsp;
</div>
</div>

</body>
</html>

