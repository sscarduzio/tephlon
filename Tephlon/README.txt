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


== Direct Access Usage  Example==

// include somewhere Tephlon script appropriately.
<?php
require_once("/Tephlon/Tephlon.php");

// 1. Get a Tephlon persistent resource
$t = Tephlon::getResource();

// 2. Decide a label for your precious object and invoke it with retrieve
$my_precious = $t->retrieve("my_precious_label");

/*
    If there was a cache record for "my_precious_label", now the variable $my_precious will
    have its content inside. If no such record was found, it's being created with value of null,
    and null is returned.

*/

// You can also specify a fallback defaul value if the record is not found (optional)
// $my_precious = $t->retrieve("my_precious_label", array());

/*
Doing as above is the same of doing:

$my_precious = $t->retrieve("my_precious_label");
if($myprecious != null){
    $my_precious = array();
}
*/

// 3. Now you are free to modify your "precious" variable and make it become any type, from boolean to
// any class object. Once you want to save it to Tephlon persistence layer, simply do:

$my_precious = functionToCalculateImportantData($x, $y);
// Other business logic...

// 4. Saving to persistence!
$t->register($my_precious, "my_precious_label"); 

// Now the new value of my precious will be saved, so that next script execution (read: page visit) will have that
// content.

// You can also specify a lifetime (in seconds) for this record. For example:
// For example here, the content will be available for the next 24h
$t->register($my_precious, "my_precious_label", 24*60*60);

== Using TMap Data Structure ==
Note: refer to Twitter example and TMapTest.php for complete reference

TMap is a wrapper class against the core I/O API of Tephlon's persistence engine.
It works as Java's Map interface, implementing almost all methods: (get(key), put(key, value), clear(), 
values(), keys(), etc).
but also a new one, which makes sense in PHP: getAll() which returns all the content of the map as
 an associative array.
 
<?php
// 1. include somewhere Tephlon script appropriately.
require_once("/Tephlon/Tephlon.php");

// 2.a Initialize the TMap into global namespace.
$map = new TMap();
// Now if you already executed the script, the map will already contain keys and values!!!

// 2.b Initialize TMap specifying a namespace (some examples)
$map = new TMap("myUsers");
$map = new TMap("posts_from_user_" . $userID);
$map = new TMap("recent_comments");
// Now if you already executed the script, the map will already contain keys and values!!!

/*
    NOTE: a namespace can set also passing an object, though this practice is quite risky
    since it can make your content unreachable if you do it wrong.
*/

// 3. Put some  value (some examples)
$map->put("key1", "some_string");
$map->put(time(), new Post($userID, $content);


// 4. Modifying content of records
$map->put("key1", "this_string_is_better");

// 5. Retrieve it
$var = $map->get("key1");

$theUser = $map->get($requestedUser);
$theUser->setEmail("new@email.com");
$map->put($requestedUser, $theUser);


Enjoy!


Simone