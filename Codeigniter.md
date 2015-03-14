<h1> Introduction </h1>
In this tutorial you'll learn how to integrate Tephlon with CodeIgniter as a library.
<h2> Install Codeigniter </h2>
Assumed you have already Apache and PHP configured, you'll also have document root, let's state for example:
```
DocumentRoot = /var/www/
```
Now download latest CI tarball from it's site http://codeigniter.com/, which at the moment is version 1.7.2.
Uncompress it into your document root.
Now download latest Tephlon tarball from Downloads page and place the whole "Tephlon" directory under system/application/libraries, so that you'll have this directory:
```
/var/www/Code_Igniter_X.Y.Z/system/application/libraries/Tephlon/
```
Bring yourself now here:
```
/var/www/Code_Igniter_X.Y.Z/system/application/config/
```
<h2> Install Tephlon </h2>
Set you DocumentRoot path into config.php, and edit also autoload.php. Here find the $autoload['libraries'] array and make it look like this:
```
$autoload['libraries'] = array('Tephlon/tephlon');
```
Notice the capital T for the directory and the small t after the slash. This will auto include the main Tephlon.php file which you just placed under libraries.
<h2> Using Tephlon from CodeIgniter </h2>
From this moment you'll be free to use Tephlon absolutely as usual ([Usage](Usage.md)). Use Tephlon from whatever controller, view or model.
You don't even have to call it using _$this->tephlon->_, since Tephlon resources are accessed by static methods.
Here's for example using the default Welcome controller that comes as an example in CodeIgniter.
```
<?php

class Welcome extends Controller {

    function Welcome(){
        parent::Controller();
    }

    function index(){
        // Normally instantiate a core resource
        $r = Tephlon::getResource("ci_resource");
        
        // Register and retrieve some text
        $r->register("helloworld", "label");
        echo $r->retrieve("label");

        
        // Normally instantiate a TMap and use it as usual.
        $map = new TMap("testmap");
        $map->put("key1", "hello, map!");
        echo $map->get("key1");
        $map->clear();
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
```
That's all folks. As easy as this!