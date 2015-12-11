<?php
namespace trejeraos\SparkTest\Middleware;

class FooCollection extends \Spark\Middleware\Collection
{
    public function __construct(\Spark\Middleware\DefaultCollection $defaults)
    {
        $middlewares = array_merge([
            \trejeraos\SparkTest\Auth\FooHandler::class
        ], $defaults->getArrayCopy(), [
            FooMiddleware::class
        ]);
        parent::__construct($middlewares);
    }
}
