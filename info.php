<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

define('APP_SCRIPT', basename(__FILE__));

$request_uri = preg_replace('/^(.*?'.preg_quote(APP_SCRIPT).')/i', '', $_SERVER['REQUEST_URI']);
if (empty($request_uri)) { $request_uri = '/'; }

$_SERVER['REQUEST_URI'] = $request_uri;

chdir('web/');
include('index.php');
