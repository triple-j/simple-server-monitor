<?php
// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Spark\Auth\AuthHandler;
use Spark\Auth\AdapterInterface;
use Spark\Auth\Token\ExtractorInterface as TokenExtractorInterface;
use Spark\Auth\Token\QueryExtractor;
use Spark\Auth\Credentials\ExtractorInterface as CredentialsExtractorInterface;
use Spark\Auth\Credentials\JsonExtractor;
use trejeraos\SparkTest\Auth\FooHandler as FooAuthHandler;
use trejeraos\SparkTest\Domain;

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
$injector->alias(AdapterInterface::class, trejeraos\SparkTest\Auth\FooAdapter::class);
$injector->alias(AuthHandler::class, FooAuthHandler::class);
$injector->share(FooAuthHandler::class);

// get auth token
$injector->alias(TokenExtractorInterface::class, QueryExtractor::class);
$injector->define(QueryExtractor::class, [':parameter' => 'tok']);

// get auth credentials
$injector->alias(CredentialsExtractorInterface::class, JsonExtractor::class);
$injector->define(JsonExtractor::class, [':identifier' => 'user', ':password' => 'password']);

// share valid auth token class
$injector->share(\trejeraos\SparkTest\Auth\ValidTokens::class);
//END: auth

// share global config class
$injector->share(\trejeraos\SparkTest\Data\Configuration::class);

// Configure the router
$injector->prepare(
    '\\Spark\\Router',
    function(\Spark\Router $router) {
        // Authentication
        $router->post('/auth', Domain\Login\Authenticate::class);

        // ...
        $router->get('/hello[/{name}]', Domain\Hello::class);
        $router->get('/plates[/{name}]', Domain\HelloPlates::class)->setResponder(\trejeraos\SparkTest\Responder\TemplateResponder::class);;

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
