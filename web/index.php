<?php
use Spark\Project\Data\Config;

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
    $r->get('/hello[/{name}]', 'Spark\Project\Domain\Hello');

    $r->get('/info/system', 'Spark\Project\Domain\System');

    $r->get('/info/cpu', 'Spark\Project\Domain\Cpu');
    $r->get('/info/memory', 'Spark\Project\Domain\Memory');
    $r->get('/info/swap', 'Spark\Project\Domain\Swap');

    $r->get('/info/network', 'Spark\Project\Domain\Network');
    $r->get('/info/bandwidth/{interface}[/{seconds}]', 'Spark\Project\Domain\Bandwidth');


    // HTML output
    $r->setDefaultResponder('Spark\Project\Responder\TemplateResponder');
    $r->get('/', 'Spark\Project\Domain\Frontend');
});

$app->run();
