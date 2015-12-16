<?php
namespace trejeraos\SimpleServerMonitor\Domain\Test;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\Configuration;

class HelloPlates implements DomainInterface
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
                'template' => "hello",
                'hello' => $name,
                'my_name' => $this->config->getMyName()
            ]);
    }
}
