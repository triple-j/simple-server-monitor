<?php
namespace trejeraos\SparkTest\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SparkTest\Configuration;
use trejeraos\SparkTest\Middleware\SimpleAuth;

class Name extends Restricted
{
    protected $config;

    public function __construct(Configuration $config, SimpleAuth $auth)
    {
        parent::__construct($auth);

        $this->config = $config;
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
