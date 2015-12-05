<?php
namespace trejeraos\SparkTest\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Spark\Auth\RequestFilterInterface;

/**
 * Determines whether a request should require authentication.
 */
class RequestFilter implements RequestFilterInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return boolean TRUE if the request should require authentication,
     *         FALSE otherwise
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $server_vars = $request->getServerParams();

        return ($server_vars['REQUEST_URI'] == "/auth");
    }
}
