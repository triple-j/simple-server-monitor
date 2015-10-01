<?php
/*
  Quick script to demonstrate making one-off connection to gkrellmd running on localhost and displaying some data
*/

  require('../php-gkrellm/php-gkrellm.inc.php');
  $o_gkrellm = new gkrellm_client('localhost');
  
  if(!$o_gkrellm->connect(true))
  {
    echo $o_gkrellm->get_last_error();
  }
  
  $o_gkrellm->get_next_update_of_type(GKRELLM_UPDATE_CPU);
  
  $a_cpus = $o_gkrellm->get_cpu_info()->get_cpus();
  ?>
  <b>CPU</b><br />
  <?php
  foreach($a_cpus as $o_cpu)
  {
  ?>
  CPU <?=$o_cpu->get_cpu()?><br />
  User: <?=$o_cpu->get_last_user(true)?>%<br />
  Nice: <?=$o_cpu->get_last_nice(true)?>%<br />
  Sys: <?=$o_cpu->get_last_sys(true)?>%<br />
  Idle: <?=$o_cpu->get_last_idle(true)?>%<br />
  <?php
  }
  ?>
  <b>Total for all CPUs:</b><br />
  User: <?=$o_gkrellm->get_cpu_info()->get_last_total_user(true)?>%<br />
  Nice: <?=$o_gkrellm->get_cpu_info()->get_last_total_nice(true)?>%<br />
  Sys: <?=$o_gkrellm->get_cpu_info()->get_last_total_sys(true)?>%<br />
  Idle: <?=$o_gkrellm->get_cpu_info()->get_last_total_idle(true)?>%<br />
<br />
<?php
  $a_disks = $o_gkrellm->get_disk_info()->get_disks();
?>
  <b>DISK</b><br/>
  Total disk activity: <?=gkrellm_client::format_bytes($o_gkrellm->get_disk_activity(null, 2))?><br />
<?php
  foreach($a_disks as $o_disk)
  {
   ?>
   <?=$o_disk->get_name()?> Read: <?=$o_disk->get_block_read()?> Write: <?=$o_disk->get_block_write()?> 
   Parent: <?=$o_disk->get_parent()?> Virtual?: <?=$o_disk->is_virtual() ? 'y' : 'n'?><br />
   <?php
  }
?>
<br />
<b>FSTAB</b><br />
<?php
  if($o_gkrellm->get_fstab_info())
    $a_fs = $o_gkrellm->get_fstab_info()->get_all_fs();
  if($a_fs)
  foreach($a_fs as $o_fs)
  {
  ?>
  <?=$o_fs->get_dir()?> <?=$o_fs->get_dev()?> <?=$o_fs->get_type()?><br />
  <?php
  }
  
  $a_mounts = $o_gkrellm->get_fsmounts_info()->get_mounts();
?>
<br />
<b>FSMOUNTS</b><br />
<?php
  foreach($a_mounts as $o_mount)
  {
  ?>
  <?=$o_mount->get_dir()?> <?=$o_mount->get_dev()?> <?=$o_mount->get_type()?> Blocks: <?=$o_mount->get_blocks()?>
  Blocks available: <?=$o_mount->get_blocks_available()?>
  Blocks free: <?=$o_mount->get_blocks_free()?>
  Block size: <?=$o_mount->get_block_size()?>
  Bytes free: <?=$o_mount->get_bytes_free() / 1024 / 1024?><br />
  <?php
  }
  
  if($o_gkrellm->get_inet_info())
    $a_connections = $o_gkrellm->get_inet_info()->get_connections();
?>
<b>INET</b><br />
<?php
  if($a_connections)
  foreach($a_connections as $o_connection)
  {
  ?>
  <?=$o_connection->get_remote_ip()?> Local port: <?=$o_connection->get_local_port()?> 
  Remote port: <?=$o_connection->get_remote_port()?><br />
  <?php
  }
  
  $o_mem = $o_gkrellm->get_mem_info();
?>
<b>MEM</b><br/>
Total: <?=$o_mem->get_total()?> 
Free: <?=$o_mem->get_free()?> 
Used: <?=$o_mem->get_used()?> 
Cached: <?=$o_mem->get_cached()?> 
Buffers: <?=$o_mem->get_buffers()?> 
Shared: <?=$o_mem->get_shared()?> <br />
<b>NET</b><br />
<?php
  $a_interfaces = $o_gkrellm->get_net_info()->get_interfaces();
  
  foreach($a_interfaces as $o_interface)
  {
  ?>
  <?=$o_interface->get_name()?> RX: <?=$o_interface->get_rx()?> TX: <?=$o_interface->get_tx()?> 
  Routed?: <?=$o_interface->is_routed() ? 'y' : 'n'?><br />
  T/fer per second: <?=gkrellm_client::format_bytes($o_gkrellm->get_interface_bandwidth_use($o_interface->get_name(), 2))?><br />
  <?php
  }
?>
<b>PROC</b><br>
<?php
  $o_proc = $o_gkrellm->get_proc_info();
?>
Processes: <?=$o_proc->get_processes()?> 
Running: <?=$o_proc->get_running()?> 
Forks: <?=$o_proc->get_forks()?> 
Load: <?=$o_proc->get_load()?> 
Users: <?=$o_proc->get_users()?> <br/>
<b>SENSORS</b><br>
<?php
  $o_sensors = $o_gkrellm->get_sensors_info();
  $a_sensors = $o_sensors->get_sensors();
 
  foreach($a_sensors as $o_sensor)
  {
  ?>
  Type: <?=$o_sensor->get_type()?> 
  Basename: <?=$o_sensor->get_basename()?> 
  iodev: <?=$o_sensor->get_iodev()?> 
  inter: <?=$o_sensor->get_inter()?> 
  factor: <?=$o_sensor->get_factor()?> 
  offset: <?=$o_sensor->get_offset()?> 
  vref: <?=$o_sensor->get_vref()?> 
  default label: <?=$o_sensor->get_default_label()?> 
  group: <?=$o_sensor->get_group()?> <br />
  <?php
  }
  
  $o_swap = $o_gkrellm->get_swap_info();
  $o_time = $o_gkrellm->get_time_info();
  $o_uptime = $o_gkrellm->get_uptime_info();
?>
<br />
<b>SWAP</b><br />
Total: <?=$o_swap->get_total()?> 
Used: <?=$o_swap->get_used()?> 
In: <?=$o_swap->get_in()?> 
Out: <?=$o_swap->get_out()?> 
<br>
<b>TIME</b><br>
<?=$o_time->get_hour()?>:<?=$o_time->get_minute()?>:<?=$o_time->get_second()?><br>
Mday: <?=$o_time->get_mday()?> Month: <?=$o_time->get_month()?> Year: <?=$o_time->get_year()?><br />
Wday: <?=$o_time->get_wday()?> Yday: <?=$o_time->get_yday()?><br>
isdst?: <?=$o_time->isdst() ? 'y' : 'n'?> <br >
<?=date('r', $o_time->get_unix_timestamp())?><br />
<br>
<b>UPTIME</b><br>
<?=$o_uptime->get_formatted_uptime()?>
<?php
  $o_gkrellm->disconnect();
?>
