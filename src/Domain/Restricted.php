<?php
namespace trejeraos\SparkTest\Domain;

use Spark\Adr\DomainInterface;
use trejeraos\SparkTest\Middleware\SimpleAuth;

class Restricted implements DomainInterface
{
    protected $token;

    public function __construct(SimpleAuth $auth)
    {
        $this->token = $auth->authenticate();
    }

    public function __invoke(array $input)
    {
        // ...
    }
}
