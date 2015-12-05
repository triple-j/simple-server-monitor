<?php
namespace trejeraos\SparkTest\Domain\Login;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Auth\Credentials;
use trejeraos\SparkTest\Auth\Authenticator;

class Authenticate implements DomainInterface
{
    protected $auth;

    public function __construct(Authenticator $auth)
    {
        $this->auth = $auth;
    }

    public function __invoke(array $input)
    {
        $payload = new Payload();

        /*if (empty($input['user']) || empty($input['password'])) {
            $payload->withStatus(Payload::ERROR);
            return $payload->withInput(['error' => 'Please include both username and password fields.']);
        }*/

        $username = "bob"; #$input['user'];
        $password = "teapot"; #$input['password'];

        $token = $this->auth->validateCredentials(new Credentials($username, $password));

        // Password validation failed
        /*if (!$token) {
            $payload->withStatus(Payload::ERROR);
            return $payload->withInput(['error' => 'No user with that password.']);
        }*/

        $payload->withStatus(Payload::OK);
        return $payload->withOutput([
            'user' => $username,
            'token' => $token,
        ]);
    }
}
