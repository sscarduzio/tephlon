:: Tephlon PHP Persistence library ::

For recent updates, documentation, examples, issue tracking and discussion please always refer to the following site:

http://tephlon.googlecode.com/

Feel free to contact me if you are interested in improving this software.

== Install==
Typically this is the directory layout you want to keep in your Tephlon project:
	
	Your_php_project_>
	                 |_> index.php
	                 |_> css/
	                 |_> img/
	                 |_> Tephlon


== Direct Access Usage ==
Make sure you include somewhere this file appropriately.

require_once("/Tephlon/Tephlon.php");

* Get the instance of the persistence engine singleton:
$t = Tephlon::getResource();

* Decide a label for your precious object and invoke it with retrieve

$my_precious = $t->retrieve("my_precious_label");

If there was a cache record for "my_precious_label", now the variable $my_precious will
have its content inside. If no such record was found, it's being created with value of null,
and null is returned.

IMPORTANT: LABEL IS UNIQUE INSIDE THE CURRENT CLASS.
For example: the label "my_precious_label" refers to a different cache record 
if retrieved from class "Flow" or "Users". You'll refer to yet another cache record
if you retrieve the same label from global scope (out of any class), if for example you are ont
planning to use object oriented PHP (procedural).

You can also specify a fallback defaul value if the record is not found (optional)

$my_precious = $t->retrieve("my_precious_label", array());

Doing this is the same of doing:

$my_precious = $t->retrieve("my_precious_label");
if($myprecious != null){
	$my_precious = array();
}
Now you are free to modify your "precious" variable and make it become any type, from boolean to
any class object. Once you want to save it to Tephlon persistence layer, simply do:

$my_precious = calculateImportantData($x, $y);
...
// Saving to persistence!
$t->register($my_precious, "my_precious_label"); 

and its value will be saved, so that next script execution (read: page visit) will have that
content.

You can also specify a lifetime (in seconds) for this record. For example:
// The content will be available for the next 24h
$t->register($my_precious, "my_precious_label", 24*60*60);

== Superclass Approach ==
A more practical way to leverage persistence is to extend TephlonDT class
For simple example of how to use it take in consideration the class Flow or UserBoard
that you find in basic "Twitter" example.
Basically extending TephlonDT gives you access to two protected methods:

$this->tephlonInit($my_data, "my_label", <$default>); 
$this->tephlonSave($my_data); 

Enjoy!


Simone