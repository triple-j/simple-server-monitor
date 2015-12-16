<?php

namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\Configuration;

class Frontend implements DomainInterface
{
    protected $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }
    
    public function __invoke(array $input)
    {
        $stuff = array(
            'template' => "default",
            'root'     => $this->config->getBase(),
            'title'    => "Simple System Info"
        );

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($stuff);
    }
}
