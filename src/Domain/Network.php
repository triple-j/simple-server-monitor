<?php

namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\SysInfo;

class Network implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $networkInfo = $system->get_network_info();

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($networkInfo);
    }
}
