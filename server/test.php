<?php
/*
$_POST['action'] = 'plugin_information';
$_POST['request'] = serialize(array(
    'plugin' => 'wmg-admin',
    'slug' => 'wmg-admin',
    'version' => '1.0'
));

$_SERVER['REQUEST_URI'] = '/';
*/
$_POST['action'] = 'plugin_update';
$_POST['request'] = serialize(array(
    'plugin' => 'wmg-admin',
    'slug' => 'wmg-admin',
    'version' => '1.0'
));

$_SERVER['HTTP_USER_AGENT'] = 'WordPress/4.1; http://domain.com/';

$_SERVER['REQUEST_URI'] = '/';

require_once 'index.php';