<?php

namespace trejeraos\SimpleServerMonitor\Domain\Monitor;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\SysInfo;

class Swap implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $information = $system->get_swap_info();

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($information);
    }
}
