<?php
#ini_set('display_errors', 1);
#error_reporting(E_ALL);

$filename = basename(__FILE__);
$request_uri = preg_replace('/^(.*?'.preg_quote($filename).')/i', '', $_SERVER['REQUEST_URI']);
if (empty($request_uri)) { $request_uri = '/'; }

$_SERVER['REQUEST_URI'] = $request_uri;

chdir('web/');
include('index.php');
