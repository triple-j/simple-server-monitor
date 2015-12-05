<?php
namespace trejeraos\SparkTest\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SparkTest\Configuration;

class Hello implements DomainInterface
{
    protected $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public function __invoke(array $input)
    {
        $name = 'world';

        if (!empty($input['name'])) {
            $name = $input['name'];
        }

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput([
                'hello' => $name,
                'my_name' => $this->config->my_name
            ]);
    }
}
