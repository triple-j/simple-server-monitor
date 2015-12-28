<?php
// Include Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

use Spark\Action;
use trejeraos\SimpleServerMonitor\Domain;
use trejeraos\SimpleServerMonitor\Responder\TemplateResponder;


Spark\Application::build()
    ->setConfiguration([
        Spark\Configuration\AurynConfiguration::class,
        Spark\Configuration\DiactorosConfiguration::class,
        Spark\Configuration\NegotiationConfiguration::class,
        Spark\Configuration\PayloadConfiguration::class,
        Spark\Configuration\RelayConfiguration::class,
        trejeraos\SimpleServerMonitor\Configuration\FooConfiguration::class,
    ])
    ->setMiddleware([
        Relay\Middleware\ResponseSender::class,
        Spark\Handler\ExceptionHandler::class,
        Spark\Handler\DispatchHandler::class,
        Spark\Handler\JsonContentHandler::class,
        Spark\Handler\FormContentHandler::class,
        trejeraos\SimpleServerMonitor\Auth\FooHandler::class,
        Spark\Handler\ActionHandler::class,
    ])
    ->setRouting(function (Spark\Directory $directory) {
    
        return $directory
            // Authentication
            ->post('/auth', Domain\Login\Authenticate::class)
        
            //START: Simple Test Code (TODO: Delete before version 1.0)
            ->get('/test/hello[/{name}]', Domain\Test\Hello::class)
            ->get('/test/plates[/{name}]', new Action(Domain\Test\HelloPlates::class, TemplateResponder::class))
        
            // PUT
            ->put('/test/name', Domain\Test\Name::class)
            //END: Simple Test Code
        
            // JSON output
            ->get('/info/system', Domain\Monitor\System::class)
        
            ->get('/info/cpu',    Domain\Monitor\Cpu::class)
            ->get('/info/memory', Domain\Monitor\Memory::class)
            ->get('/info/swap',   Domain\Monitor\Swap::class)
        
            ->get('/info/network', Domain\Monitor\Network::class)
            ->get('/info/bandwidth/{interface}[/{seconds}]', Domain\Monitor\Bandwidth::class)
        
        
            // HTML output
            ->get('/', new Action(Domain\Frontend::class, TemplateResponder::class))
            
            ; // End of routing
    })
    ->run();
