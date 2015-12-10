<?php
namespace trejeraos\SparkTest\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SparkTest\Configuration;
use trejeraos\SparkTest\Middleware\SimpleAuth;

class Name implements DomainInterface
{
    protected $config;
    protected $auth;

    public function __construct(Configuration $config, SimpleAuth $auth)
    {
        $this->config = $config;
        $this->auth = $auth;
        $this->auth->authenticate();
    }

    public function __invoke(array $input)
    {

        $name = $this->config->getMyName();

        if (!empty($input['name'])) {
            $name = $input['name'];

            $this->config->setMyName($name);
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'my_name' => $name
            ]);
    }
}
