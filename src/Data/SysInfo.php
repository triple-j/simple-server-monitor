<?php

namespace Spark\Project\Data;

use \gkrellm_client;

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

        return $memData;
    }
}
