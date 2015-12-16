<?php
namespace trejeraos\SimpleServerMonitor\Domain\Test;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\Configuration;
use trejeraos\SimpleServerMonitor\Auth\FooHandler as FooAuthHandler;
use trejeraos\SimpleServerMonitor\Domain\Restricted;

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
