<?php

namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Project\Data\SysInfo;

class Memory implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $information = $system->get_mem_info();

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($information);
    }
}
