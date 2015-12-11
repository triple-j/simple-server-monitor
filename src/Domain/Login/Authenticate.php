<?php
namespace trejeraos\SparkTest\Domain\Login;

use Spark\Payload;
use trejeraos\SparkTest\Middleware\SimpleAuth;
use trejeraos\SparkTest\Domain\Restricted;

class Authenticate extends Restricted
{
    public function __invoke(array $input)
    {
        $payload = new Payload();

        $username = "unknown";

        $token = $this->token;

        $payload->withStatus(Payload::OK);
        return $payload->withOutput([
            'user'  => $token->getMetadata('username'),
            'token' => $token->getToken(),
        ]);
    }
}
