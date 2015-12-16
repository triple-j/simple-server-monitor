<?php
namespace trejeraos\SparkTest\Domain\Test;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SparkTest\Data\Configuration;
use trejeraos\SparkTest\Auth\FooHandler as FooAuthHandler;
use trejeraos\SparkTest\Domain\Restricted;

class Name extends Restricted
{
    protected $config;

    public function __construct(Configuration $config, FooAuthHandler $auth)
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
