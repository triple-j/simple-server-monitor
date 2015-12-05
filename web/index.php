<?php
// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use trejeraos\SparkTest\Domain;
use trejeraos\SparkTest\Configuration;

// Configure the dependency injection container
$injector = new \Auryn\Injector;
$configuration = new \Spark\Configuration\DefaultConfigurationSet;
$configuration->apply($injector);

// Configure middleware
$injector->alias(
    '\\Spark\\Middleware\\Collection',
    '\\Spark\\Middleware\\DefaultCollection'
);

// get global config values
$shared_config = new Configuration("config.ini", array(__DIR__.'/../', __DIR__.'/../src/'));
$injector->share($shared_config);

// Configure the router
$injector->prepare(
    '\\Spark\\Router',
    function(\Spark\Router $router) {
        // ...
        $router->get('/hello[/{name}]', Domain\Hello::class);
    }
);

// Bootstrap the application
$dispatcher = $injector->make('\\Relay\\Relay');
$dispatcher(
    $injector->make('Psr\\Http\\Message\\ServerRequestInterface'),
    $injector->make('Psr\\Http\\Message\\ResponseInterface')
);
