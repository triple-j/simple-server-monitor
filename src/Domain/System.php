<?php

namespace trejeraos\SimpleServerMonitor\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use trejeraos\SimpleServerMonitor\Data\SysInfo;

class System implements DomainInterface
{
    public function __invoke(array $input)
    {
        $system = new SysInfo();  //TODO: try catch block (connection issues)

        $sysInfo = $system->get_system_info();

        // only works on current machine (REMOVE?)

        /*$unameInfo = array(
            "os"      => php_uname('s'),
            "host"    => php_uname('n'),
            "release" => php_uname('r'),
            "version" => php_uname('v'),
            "machine" => php_uname('m')
        );*/

        return (new Payload)
            ->withStatus(Payload::OK)
            ->withOutput($sysInfo);
    }
}
