<?php
// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Spark\Auth\AuthHandler;
use Spark\Auth\AdapterInterface;
use Spark\Auth\Token\ExtractorInterface as TokenExtractorInterface;
use Spark\Auth\Token\QueryExtractor;
use Spark\Auth\Credentials\ExtractorInterface as CredentialsExtractorInterface;
use Spark\Auth\Credentials\JsonExtractor;
use trejeraos\SparkTest\Domain;
use trejeraos\SparkTest\Configuration;

// Configure the dependency injection container
$injector = new \Auryn\Injector;
$configuration = new \Spark\Configuration\DefaultConfigurationSet;
$configuration->apply($injector);

// Configure middleware
$injector->alias(
    '\\Spark\\Middleware\\Collection',
    '\\trejeraos\\SparkTest\\Middleware\\FooCollection'
);

//START: auth
$injector->share(trejeraos\SparkTest\Middleware\SimpleAuth::class);

// get auth token
$injector->alias(
    TokenExtractorInterface::class,
    QueryExtractor::class
);
$injector->define(
    QueryExtractor::class,
    [':parameter' => 'tok']
);

// get auth credentials
$injector->alias(
    CredentialsExtractorInterface::class,
    JsonExtractor::class
);
$injector->define(
    JsonExtractor::class,
    [':identifier' => 'user', ':password' => 'password']
);


$injector->alias(
    AdapterInterface::class,
    trejeraos\SparkTest\Auth\Authenticator::class
);

$injector->alias(
    AuthHandler::class,
    trejeraos\SparkTest\Middleware\SimpleAuth::class
);
//END: auth



// get global config values
$injector->share(Configuration::class);


$injector->share(\trejeraos\SparkTest\Auth\ValidTokens::class);


// Configure the router
$injector->prepare(
    '\\Spark\\Router',
    function(\Spark\Router $router) {
        // Authentication
        $router->post('/auth', Domain\Login\Authenticate::class);

        // ...
        $router->get('/hello[/{name}]', Domain\Hello::class);

        // PUT
        $router->put('/name', Domain\Name::class);
    }
);

// Bootstrap the application
$dispatcher = $injector->make('\\Relay\\Relay');
$dispatcher(
    $injector->make('Psr\\Http\\Message\\ServerRequestInterface'),
    $injector->make('Psr\\Http\\Message\\ResponseInterface')
);
