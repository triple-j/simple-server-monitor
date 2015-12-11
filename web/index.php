<?php
use trejeraos\SimpleServerMonitor\Data\Config;

require __DIR__ . '/../vendor/autoload.php';

$app = Spark\Application::boot();

$app->setMiddleware([
    'Relay\Middleware\ResponseSender',
    'Spark\Handler\ExceptionHandler',
    'Spark\Handler\RouteHandler',
    'Spark\Handler\ActionHandler',
]);

Config::parse("config.xml");

$app->addRoutes(function(Spark\Router $r) {
    // JSON output
    $r->get('/info/system', 'trejeraos\SimpleServerMonitor\Domain\Monitor\System');

    $r->get('/info/cpu', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Cpu');
    $r->get('/info/memory', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Memory');
    $r->get('/info/swap', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Swap');

    $r->get('/info/network', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Network');
    $r->get('/info/bandwidth/{interface}[/{seconds}]', 'trejeraos\SimpleServerMonitor\Domain\Monitor\Bandwidth');


    // HTML output
    $r->setDefaultResponder('trejeraos\SimpleServerMonitor\Responder\TemplateResponder');
    $r->get('/', 'trejeraos\SimpleServerMonitor\Domain\Frontend');
});

$app->run();
