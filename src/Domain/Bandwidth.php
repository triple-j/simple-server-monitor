<?php

namespace Spark\Project\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use Spark\Project\Data\SysInfo;

class Bandwidth implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $interface = $input['interface'];
        $seconds   = !empty($input['seconds']) ? $input['seconds'] : 2;

        $bandwidthInfo = $system->get_bandwidth_use($interface, $seconds);

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($bandwidthInfo);
    }
}
