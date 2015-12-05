<?php
namespace trejeraos\SparkTest\Middleware;

class FooCollection extends \Spark\Middleware\Collection
{
    public function __construct(\Spark\Middleware\DefaultCollection $defaults)
    {
        $middlewares = array_merge($defaults->getArrayCopy(), [
            FooMiddleware::class,
            \Spark\Auth\AuthHandler::class
        ]);
        parent::__construct($middlewares);
    }
}
