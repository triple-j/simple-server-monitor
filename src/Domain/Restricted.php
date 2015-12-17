<?php
namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use trejeraos\SimpleServerMonitor\Auth\FooHandler as FooAuthHandler;

class Restricted implements DomainInterface
{
    protected $token;

    public function __construct(FooAuthHandler $auth)
    {
        $this->token = $auth->authenticate();
    }

    public function __invoke(array $input)
    {
        // ...
    }
}
