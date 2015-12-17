<?php
namespace trejeraos\SimpleServerMonitor\Domain\Login;

use Spark\Payload;
use trejeraos\SimpleServerMonitor\Domain\Restricted;

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
