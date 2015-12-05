<?php
namespace trejeraos\SparkTest\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FooMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        //var_dump($response);

        $response = $next($request, $response);
        return $response;
    }
}
