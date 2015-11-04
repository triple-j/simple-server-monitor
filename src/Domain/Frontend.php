<?php

namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;

class Frontend implements DomainInterface
{
    public function __invoke(array $input)
    {
        $stuff = array(
            'template'=>"default",
            'root'=>"info.php/",
            'title'=>"Simple System Info"
        );

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($stuff);
    }
}
