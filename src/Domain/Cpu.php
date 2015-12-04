<?php

namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\SysInfo;

class Cpu implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $cpuInfo = $system->get_cpu_info();

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($cpuInfo);
    }
}
