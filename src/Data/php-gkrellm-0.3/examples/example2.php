<?php
/*
  quick script to demonstrate making a persistent connection to gkrellmd, reading the number of bytes received
  by the interface eth0 over 3 seconds and then calculating the average transfer rate, before disconnecting
  
  Note that this example is just to demonstrate using a persistent connection. If you actually want to get the average
  bandwidth use of a network interface, a much simpler way is to use gkrellm_client::get_interface_bandwidth_use()
*/

  require('../php-gkrellm/php-gkrellm.inc.php');
  $o_gkrellm = new gkrellm_client('localhost');

  if(!$o_gkrellm->connect(true))
  {
    echo $o_gkrellm->get_last_error();
  }
  
  define('SAMPLE_SECONDS', 3);
  
  $i_start_time = time();
  while(time() - $i_start_time < SAMPLE_SECONDS)
  {
    $o_update = $o_gkrellm->get_next_update();
    
    if($o_update && $o_update->get_type() == GKRELLM_UPDATE_NET)
    {
      if(!$i_start_bytes)
        $i_start_bytes = $o_gkrellm->get_net_info()->get_interface('eth0')->get_rx();
    }
  }
  $i_end_bytes = $o_gkrellm->get_net_info()->get_interface('eth0')->get_rx();
  $i_end_time = time();
  
  echo 'Avg: '.(($i_end_bytes - $i_start_bytes) / ($i_end_time - $i_start_time)).' bytes per second';
  
  $o_gkrellm->disconnect();
?>
