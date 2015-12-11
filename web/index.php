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
use trejeraos\SimpleServerMonitor\Data\Config;

Config::parse("config.xml");

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
        $router->get('/plates[/{name}]', Domain\HelloPlates::class)->setResponder(\trejeraos\SimpleServerMonitor\Responder\TemplateResponder::class);

        // PUT
        $router->put('/name', Domain\Name::class);

        // ---

        // JSON output
        $router->get('/info/system', 'trejeraos\SimpleServerMonitor\Domain\Monitor\System');

        $router->get('/info/cpu', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Cpu');
        $router->get('/info/memory', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Memory');
        $router->get('/info/swap', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Swap');

        $router->get('/info/network', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Network');
        $router->get('/info/bandwidth/{interface}[/{seconds}]', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Bandwidth');


        // HTML output
        $router->get('/', 'trejeraos\SimpleServerMonitor\Domain\Frontend')->setResponder(\trejeraos\SimpleServerMonitor\Responder\TemplateResponder::class);
    }
);

// Bootstrap the application
$dispatcher = $injector->make('\\Relay\\Relay');
$dispatcher(
    $injector->make('Psr\\Http\\Message\\ServerRequestInterface'),
    $injector->make('Psr\\Http\\Message\\ResponseInterface')
);
