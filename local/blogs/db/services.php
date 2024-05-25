<?php
$functions = array(
    
    'local_blogs_blogdetails' => array(
        'classname' => 'local_blogs_external',
        'methodname' => 'blogdetails',
        'classpath' => 'local/blogs/externallib.php',
        'description' => 'This function is used to fetch blog details',
        'type' => 'write',
        'ajax' => true, // Disabling ajax since this will be no ajax call.
        'loginrequired' => false,
    ),
       
);
$services = array(
    
    'Blog APIs' => array(
        'functions' => array_keys($functions),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'blogapi',
        'uploadfiles' => true,
    )
);