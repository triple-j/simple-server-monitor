<?php

namespace Spark\Project\Data;

use \gkrellm_client;
use \Exception;

require_once(__DIR__."/php-gkrellm-0.3/php-gkrellm/php-gkrellm.inc.php");

class SysInfo
{
    private $gkrellm;

    public function __construct($host="127.0.0.1")
    {
        $this->gkrellm = new gkrellm_client($host);
        $this->connect();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    private function connect()
    {
        if(!$this->gkrellm->connect(true)) {
            throw new Exception($this->gkrellm->get_last_error());
        }
    }

    private function disconnect()
    {
        $this->gkrellm->disconnect();
    }

    public function get_cpu_info()
    {
        $this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_CPU);

        $cpuInfo = $this->gkrellm->get_cpu_info();

        $cpuData = array(
            "total" => array(
                "user"   => $cpuInfo->get_last_total_user(true),
                "nice"   => $cpuInfo->get_last_total_nice(true),
                "system" => $cpuInfo->get_last_total_sys(true),
                "idle"   => $cpuInfo->get_last_total_idle(true)
            ),
            "individual" => array()
        );

        $allCpus = $cpuInfo->get_cpus();
        foreach($allCpus as $cpu) {
            $individualCpuData = array(
                "cpu"    => $cpu->get_cpu(),
                "user"   => $cpu->get_last_user(true),
                "nice"   => $cpu->get_last_nice(true),
                "system" => $cpu->get_last_sys(true),
                "idle"   => $cpu->get_last_idle(true)
            );
            $cpuData['individual'] []= $individualCpuData;
        }

        return $cpuData;
    }

    public function get_mem_info()
    {
        $this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_MEM);

        $memInfo = $this->gkrellm->get_mem_info();

        $memData = array(
            "total"   => $memInfo->get_total(),
            "used"    => $memInfo->get_used(),
            "free"    => $memInfo->get_free(),
            "cached"  => $memInfo->get_cached(),
            "buffers" => $memInfo->get_buffers(),
            "shared"  => $memInfo->get_shared(),
        );

        $memData['percent'] = array(
            "used"    => ($memData['used'] / $memData['total']) * 100,
            "free"    => ($memData['free'] / $memData['total']) * 100,
            "cached"  => ($memData['cached'] / $memData['total']) * 100,
            "buffers" => ($memData['buffers'] / $memData['total']) * 100,
            "shared"  => ($memData['shared'] / $memData['total']) * 100
        );

        return $memData;
    }

    public function get_swap_info()
    {
        $this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_SWAP);

        $swapInfo = $this->gkrellm->get_swap_info();

        $swapData = array(
            "total"   => $swapInfo->get_total(),
            "used"    => $swapInfo->get_used(),
            "in"      => $swapInfo->get_in(),
            "out"     => $swapInfo->get_out(),
        );

        $swapData['free'] = $swapData['total'] - $swapData['used'];

        $swapData['percent'] = array(
            "used" => ($swapData['used'] / $swapData['total']) * 100,
            "free" => ($swapData['free'] / $swapData['total']) * 100
        );

        return $swapData;
    }

    public function get_system_info()
    {
        $this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_TIME);
        $timeInfo = $this->gkrellm->get_time_info();

        //$this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_UPTIME);
        $uptimeInfo = $this->gkrellm->get_uptime_info();

        $systemData = array(
            "version" => $this->gkrellm->get_server_version(),
            "host"    => $this->gkrellm->get_hostname(),
            "os"      => $this->gkrellm->get_sysname(),
            "time"    => date('r', $timeInfo->get_unix_timestamp()),
            "uptime"  => $uptimeInfo->get_formatted_uptime()
        );

        return $systemData;
    }

    public function get_network_info()
    {
        $this->gkrellm->get_next_update_of_type(GKRELLM_UPDATE_NET);
        $netInfo = $this->gkrellm->get_net_info();

        $interfaces = $netInfo->get_interfaces();

        $netData = array();
        foreach($interfaces as $interface) {
            $ifaceData = array(
                "interface"   => $interface->get_name(),
                "received"    => $interface->get_rx(),
                "transmitted" => $interface->get_tx(),
                "is_routed"   => (boolean)$interface->is_routed()
            );

            $netData []= $ifaceData;
        }

        return $netData;
    }

    public function get_bandwidth_use($interface, $seconds=2)
    {
        //TODO: recreate `get_interface_bandwidth_use` here and separate transferred and received

        $ifaceData = array(
            "usage" => $this->gkrellm->get_interface_bandwidth_use($interface, $seconds)
        );

        if (!$ifaceData['usage']) {
            throw new Exception($this->gkrellm->get_last_error());
        }

        return $ifaceData;
    }
}
