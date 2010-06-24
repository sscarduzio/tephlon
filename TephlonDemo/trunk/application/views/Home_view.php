<?php
// Fill the data array

$data['title'] = $title;
//$data['assets'] = $assets;
$data['errors'] = $errors;
$data['codeWidth'] = 800;
$data['top'] = "";
// Full width top 'description' div
$data['code'] = <<< EOF
<div class="description">
<h2>Tephlon: persistent storage library in PHP for PHP.</h2>
<strong>Tephlon</strong> is a library intended to <strong>simplify </strong>and <strong>speed up</strong> development of modern web applications in PHP.
The advantages of preferring Tephlon to SQL based external RDBMS have much bigger extent than simply reduce the amount of code in your web application.
As we'll see, <strong>mainainance </strong>of code and the <strong>backup </strong>of whole production data will become also way easier.
<h2>Why will I code websites faster?</h2>
With Tephlon business logic and data will speak the same language. Thus, all Object-Relational mapping (ORM) code becomes totally unnecessary.

<h3>Traditional architecture (PHP + MySQL or other RDBMS)</h3>
<strong>Business logic</strong> (PHP objects, classes, methods..) will need <strong>ORM logic </strong>(other PHP code) to typically translate classes to tables, objects to tuples and actions (methods) to queries).
Once the data and the actions on them "will speak SQL", <strong> RDBMS</strong> can finally store data into its static schema with its rigid data types.
<h3>Architecture with external NoSQL architecture (PHP+Redis or similar)</h3>
Business logic will be now able to store an approximate version of the untranslated objects to the external NoSQL dynamic schema database. Unfortunately the DB won't save a lot of information about objects: field was private, protected or public? Who's it's superclass? And where are all the methods? All lost.

<strong>Architecture with Tephlon</strong>

Business logic will extend or simply instantiate and use a Tephlon class to obtain a Resource, give it the object/variable/array which needs to be stored together with its label. Next time logic will retrieve the object it will be exactly identical. Methods, fields, class information included.
<blockquote>That's so simple, Tephlon just gets rid of any overhead between PHP business logic and the data.</blockquote>
Tephlon is fully aware of what PHP's objects, arrays, or dynamically typed variables are and
makes them persistently stored. That's because Tephlon is a piece of PHP code itself.
<h2>No tables or columns? Then what?</h2>
Tephlon is built on top of two alternative - way more flexible - main concepts:
<ul>
    <li><strong>Resource</strong>:
A Resource is a container of Records and it's identified by a "Resource Label".</li>
    <li><strong>Record</strong>:
A Record is the representation of a piece of data of any shape (object, variable, boolean).
Each record belongs to a Resource, and it's identified by a "Record Label".</li>
</ul>
</div>
EOF;
// Left block, 'code' div
//$data['code'] ='code';

   
// Right block, 'window' div
$data['window'] = 'window';

// Invoke main view
$this->load->view('main', $data);