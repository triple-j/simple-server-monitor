<?php
// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Spark\Auth\AuthHandler;
use Spark\Auth\AdapterInterface;
use Spark\Auth\Token\ExtractorInterface as TokenExtractorInterface;
use Spark\Auth\Token\QueryExtractor;
use Spark\Auth\Credentials\ExtractorInterface as CredentialsExtractorInterface;
use Spark\Auth\Credentials\JsonExtractor;
use trejeraos\SimpleServerMonitor\Auth\FooHandler as FooAuthHandler;
use trejeraos\SimpleServerMonitor\Domain;

// Configure the dependency injection container
$injector = new \Auryn\Injector;
$configuration = new \Spark\Configuration\DefaultConfigurationSet;
$configuration->apply($injector);

// Configure middleware
$injector->alias(
    '\\Spark\\Middleware\\Collection',
    '\\trejeraos\\SimpleServerMonitor\\Middleware\\FooCollection'
);

//START: auth
$injector->alias(AdapterInterface::class, trejeraos\SimpleServerMonitor\Auth\FooAdapter::class);
$injector->alias(AuthHandler::class, FooAuthHandler::class);
$injector->share(FooAuthHandler::class);

// get auth token
$injector->alias(TokenExtractorInterface::class, QueryExtractor::class);
$injector->define(QueryExtractor::class, [':parameter' => 'tok']);

// get auth credentials
$injector->alias(CredentialsExtractorInterface::class, JsonExtractor::class);
$injector->define(JsonExtractor::class, [':identifier' => 'user', ':password' => 'password']);

// share valid auth token class
$injector->share(\trejeraos\SimpleServerMonitor\Auth\ValidTokens::class);
//END: auth

// share global config class
$injector->share(\trejeraos\SimpleServerMonitor\Data\Configuration::class);

// Configure the router
$injector->prepare(
    '\\Spark\\Router',
    function(\Spark\Router $router) {
        // Authentication
        $router->post('/auth', Domain\Login\Authenticate::class);

        // ...
        $router->get('/test/hello[/{name}]', Domain\Test\Hello::class);
        $router->get('/test/plates[/{name}]', Domain\Test\HelloPlates::class)->setResponder(\trejeraos\SimpleServerMonitor\Responder\TemplateResponder::class);

        // PUT
        $router->put('/test/name', Domain\Test\Name::class);

        // ---

        // JSON output
        $router->get('/info/system', Domain\Monitor\System::class);

        $router->get('/info/cpu',    Domain\Monitor\Cpu::class);
        $router->get('/info/memory', Domain\Monitor\Memory::class);
        $router->get('/info/swap',   Domain\Monitor\Swap::class);

        $router->get('/info/network', Domain\Monitor\Network::class);
        $router->get('/info/bandwidth/{interface}[/{seconds}]', Domain\Monitor\Bandwidth::class);


        // HTML output
        $router->get('/', Domain\Frontend::class)->setResponder(\trejeraos\SimpleServerMonitor\Responder\TemplateResponder::class);
    }
);

// Bootstrap the application
$dispatcher = $injector->make('\\Relay\\Relay');
$dispatcher(
    $injector->make('Psr\\Http\\Message\\ServerRequestInterface'),
    $injector->make('Psr\\Http\\Message\\ResponseInterface')
);
