<?php

namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Project\Data\Config;

class Frontend implements DomainInterface
{
    public function __invoke(array $input)
    {
        $stuff = array(
            'template' => "default",
            'root'     => Config::getBase(),
            'title'    => "Simple System Info | " . Config::$bob
        );

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($stuff);
    }
}
