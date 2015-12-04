<?php

namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\Config;

class Frontend implements DomainInterface
{
    public function __invoke(array $input)
    {
        $stuff = array(
            'template' => "default",
            'root'     => Config::getBase(),
            'title'    => "Simple System Info"
        );

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($stuff);
    }
}
