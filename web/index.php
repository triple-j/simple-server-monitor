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
    $r->get('/info/system', 'trejeraos\SimpleServerMonitor\Domain\System');

    $r->get('/info/cpu', 'trejeraos\SimpleServerMonitor\Domain\Cpu');
    $r->get('/info/memory', 'trejeraos\SimpleServerMonitor\Domain\Memory');
    $r->get('/info/swap', 'trejeraos\SimpleServerMonitor\Domain\Swap');

    $r->get('/info/network', 'trejeraos\SimpleServerMonitor\Domain\Network');
    $r->get('/info/bandwidth/{interface}[/{seconds}]', 'trejeraos\SimpleServerMonitor\Domain\Bandwidth');


    // HTML output
    $r->setDefaultResponder('trejeraos\SimpleServerMonitor\Responder\TemplateResponder');
    $r->get('/', 'trejeraos\SimpleServerMonitor\Domain\Frontend');
});

$app->run();
