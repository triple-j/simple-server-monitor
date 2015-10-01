<?php

require __DIR__ . '/../vendor/autoload.php';

$app = Spark\Application::boot();

$app->setMiddleware([
    'Relay\Middleware\ResponseSender',
    'Spark\Handler\ExceptionHandler',
    'Spark\Handler\RouteHandler',
    'Spark\Handler\ActionHandler',
]);

$app->addRoutes(function(Spark\Router $r) {
    $r->get('/hello[/{name}]', 'Spark\Project\Domain\Hello');

    $r->get('/info/cpu', 'Spark\Project\Domain\Cpu');
    $r->get('/info/memory', 'Spark\Project\Domain\Memory');
    $r->get('/info/swap', 'Spark\Project\Domain\Swap');
});

$app->run();
