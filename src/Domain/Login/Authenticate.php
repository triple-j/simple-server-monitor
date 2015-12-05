<?php
namespace trejeraos\SparkTest\Domain\Login;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SparkTest\Middleware\SimpleAuth;

class Authenticate implements DomainInterface
{
    protected $auth;

    public function __construct(SimpleAuth $auth)
    {
        $this->auth = $auth;
    }

    public function __invoke(array $input)
    {
        $payload = new Payload();

        $username = "unknown";

        $token = $this->auth->authenticate();

        $payload->withStatus(Payload::OK);
        return $payload->withOutput([
            'user' => $username,
            'token' => $token,
        ]);
    }
}
