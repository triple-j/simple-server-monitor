<?php
/*
    php-gkrellm
    Copyright (C) 2006 Mike Edwards

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('gkrellm_client_config.inc.php');
require_once('gkrellm_update.class.php');
require_once('gkrellm_update_cpu.class.php');
require_once('gkrellm_update_proc.class.php');
require_once('gkrellm_update_mem.class.php');
require_once('gkrellm_update_swap.class.php');
require_once('gkrellm_update_net.class.php');
require_once('gkrellm_update_inet.class.php');
require_once('gkrellm_update_disk.class.php');
require_once('gkrellm_update_fsmounts.class.php');
require_once('gkrellm_update_fsfstab.class.php');
require_once('gkrellm_update_time.class.php');
require_once('gkrellm_update_uptime.class.php');
require_once('gkrellm_update_sensors.class.php');
require_once('gkrellm_update_mail.class.php');
require_once('gkrellm_update_battery.class.php');

class gkrellm_client
{

  private $s_host;
  private $i_port = GKRELLM_DEFAULT_PORT;
  private $s_version_string = GKRELLM_DEFAULT_VERSTRING;
  private $f_timeout;
  private $s_last_error;
  private $r_socket;
  
  private $a_updates;
  
  private $s_last_line;
  
  private $s_server_version;
  private $s_hostname;
  private $s_sysname;
  private $i_io_timeout;
  private $i_reconnect_timeout;
  private $s_decimal_point;

/**
 * Returns a new gkrellm_client object (but doesn't connect to the specified host)
 * @param string $s_host The hostname of the server to connect to
 * @param int $i_port The port on which gkrellmd is running (defaults to 19150)
 * @param float $f_timeout Time after which a connection attempt should timeout
**/
  public function __construct($s_host = '', $i_port = 0, $f_timeout = 5)
  {
    if($i_port)
      $this->i_port = $i_port;
    $this->s_host = $s_host;
    $this->f_timeout = $f_timeout;
  }
  
/**
  * @ignore
**/
  public function __destruct()
  {
    $this->disconnect();
  }
  
/**
 * @return int gkrellmd's IO Timeout value
**/
  public function get_io_timeout()
  {
    return $this->i_io_timeout;
  }
  
/**
 * @return string gkrellmd's decimal point string
**/
  public function get_decimal_point()
  {
    return $this->s_decimal_point;
  }

/**
 * @return string
**/
  public function get_server_version()
  {
    return $this->s_server_version;
  }

/**
 * @return int gkrellmd's reconnect timeout value
**/
  public function get_reconnect_timeout()
  {
    return $this->i_reconnect_timeout;
  }

/**
 * @return gkrellm_update_cpu The latest data relating to the server's CPU(s)
**/  
  public function get_cpu_info()
  {
    return $this->get_update(GKRELLM_UPDATE_CPU);
  }
  
/**
 * Keeps on fetching updates until one of the desired type is found, or until the timeout value is reached
 * @return gkrellm_update
 * @param int $i_type The type of update required
 * @param int $i_timeout
 * @see gkrellm_update
**/
  public function get_next_update_of_type($i_type, $i_timeout = 5)
  {
    $i_start_time = time();
    $o_update = $this->get_next_update();
    while(!$o_update || $o_update->get_type() != $i_type)
    {
      if((time() - $i_start_time) >= $i_timeout)
      {
        $this->set_last_error('Operation timed out');
        return false;
      }
    
      $o_update = $this->get_next_update();
      
      if($o_update === false)
        return false;
    }
    
    return $o_update;
  }

/**
 * Fetches two updates spanning a period of time specified by $f_sample_time. Can be used for example
 * to calculate the average bandwidth used by a network interface over a period of seconds.
 * @return array Indexes 'start_time' and 'finish_time' contain timestamps marking the beginning and
 * end of the sample respectively. Indexes 'update1' and 'update2' contain the first and second updates
 * in the sample. Returns false if unsuccessful (if, for example, the operation times out).
 * @param int $i_update_type The type of update required
 * @param float $f_sample_time The period of time (in seconds) over which to take the sample
**/
  public function get_update_sample($i_update_type, $f_sample_time)
  {
    if(!$i_update_type)
      return false;
      
    if(!$this->get_update($i_update_type))
      return false;
      
    $o_first_update = clone $this->get_update($i_update_type);
    
    if(GKRELLM_USE_MICROTIME)
    {
      $f_current_time = $f_start_time = microtime(true);
    }
    else
      $f_current_time = $f_start_time = time();
      
    while($f_current_time - $f_start_time < $f_sample_time)
    {
      $o_update = $this->get_next_update_of_type($i_update_type);
      if($o_update === false)
      {
        //must have timed out waiting
        return false;
      }

      if(GKRELLM_USE_MICROTIME)
        $f_current_time = microtime(true);
      else
        $f_current_time = time();
    }
    
    $o_second_update = clone $o_update;
    
    if(GKRELLM_USE_MICROTIME)
      $f_finish_time = microtime(true);
    else
      $f_finish_time = time();
     
    $a_return = array('start_time' => $f_start_time, 'finish_time' => $f_finish_time, 
      'update1' => $o_first_update, 'update2' => $o_second_update);
      
    return $a_return;
  }

/**
 * @return int The average number of bytes written to/read from disk per second over a specified period of time
 * @param string $s_disk Specifies the device to query. If empty, the total for all disks will be returned.
 * @param float $f_sample_time The period of time (in seconds) over which to take the sample
**/
  public function get_disk_activity($s_disk, $f_sample_time)
  {
    $a_sample = $this->get_update_sample(GKRELLM_UPDATE_DISK, $f_sample_time);

    if(!$a_sample)
      return false;

    $o_first_update = $a_sample['update1'];
    $o_second_update = $a_sample['update2'];
    $f_finish_time = $a_sample['finish_time'];
    $f_start_time = $a_sample['start_time'];

    if($s_disk)
    {
      if(!($o_first_disk = $o_first_update->get_disk($s_disk)) || !($o_second_disk = $o_second_update->get_disk($s_disk)))
      {
        $this->set_last_error('No information available for the specified disk');
        return false;
      }

      $i_total = ($o_second_disk->get_block_read() + $o_second_disk->get_block_write())
        - ($o_first_disk->get_block_read() + $o_first_disk->get_block_write());
    }
    else
    {
      $i_total = ($o_second_update->get_total_read() + $o_second_update->get_total_write())
        - ($o_first_update->get_total_read() + $o_first_update->get_total_write());
    }
    
    return round($i_total / ($f_finish_time - $f_start_time));
  }

/**
 * @return int The average number of bytes per second transferred/received by a particular network interface
 * @param string $s_interface Specifies the network interface to query
 * @param float $f_sample_time The period of time (in seconds) over which to take the sample
**/
  public function get_interface_bandwidth_use($s_interface, $f_sample_time)
  {
    $a_sample = $this->get_update_sample(GKRELLM_UPDATE_NET, $f_sample_time);
    
    if(!$a_sample)
      return false;
      
    $o_first_update = $a_sample['update1'];
    $o_second_update = $a_sample['update2'];
    $f_finish_time = $a_sample['finish_time'];
    $f_start_time = $a_sample['start_time'];
    
    if(!$o_interface = $o_first_update->get_interface($s_interface))
    {
      $this->set_last_error('No information available for the specified interface');
      return false;
    }
    
    $i_start_rx = $o_interface->get_rx();
    $i_start_tx = $o_interface->get_tx();

    $o_interface = $o_second_update->get_interface($s_interface);
    if(!$o_interface)
      return false;
    
    $i_finish_rx = $o_interface->get_rx();
    $i_finish_tx = $o_interface->get_tx();

    $i_total_transfer = ($i_finish_rx - $i_start_rx) + ($i_finish_tx - $i_start_tx);
    return round($i_total_transfer / ($f_finish_time - $f_start_time));
  }  
  
/**
 * Returns a string representing the specified number of bytes, formatted according to the amount of data.
 * e.g. format_bytes(1024) will return "1 KB"
 * @return string The formatted string
 * @param int $i_bytes
**/
  public static function format_bytes($i_bytes)
  {
    if($i_bytes >= pow(1024, 4))
      return round($i_bytes / 1024.0 / 1024.0 / 1024.0 / 1024.0, 2).' TB';
    else
    if($i_bytes >= pow(1024, 3))
      return round($i_bytes / 1024.0 / 1024.0 / 1024.0, 2).' GB';
    else
    if($i_bytes >= pow(1024, 2))
      return round($i_bytes / 1024.0 / 1024.0, 2).' MB';
    else
    if($i_bytes >= 1024)
      return round($i_bytes / 1024.0, 2).' KB';
    else
    return $i_bytes.' bytes';
  }

/**
 * @return gkrellm_update_disk The latest data relating to the server's disks
**/  
  public function get_disk_info()
  {
    return $this->get_update(GKRELLM_UPDATE_DISK);
  }
  
/**
 * @return gkrellm_update_fsfstab The latest data relating to the server's filesystems
**/  
  public function get_fstab_info()
  {
    return $this->get_update(GKRELLM_UPDATE_FSFSTAB);
  }
  
/**
 * @return gkrellm_update_fsfsmount The latest data relating to the server's mounted filesystems
**/  
  public function get_fsmounts_info()
  {
    return $this->get_update(GKRELLM_UPDATE_FSMOUNTS);
  }

/**
 * @return gkrellm_update_inet The latest data relating to the server's network connections
**/  
  public function get_inet_info()
  {
    return $this->get_update(GKRELLM_UPDATE_INET);
  }
  
/**
 * @return gkrellm_update_mem The latest data relating to the server's memory
**/  
  public function get_mem_info()
  {
    return $this->get_update(GKRELLM_UPDATE_MEM);
  }
  
/**
 * @return gkrellm_update_net The latest data relating to the server's network interfaces
**/  
  public function get_net_info()
  {
    return $this->get_update(GKRELLM_UPDATE_NET);
  }

/**
 * @return gkrellm_update_proc The latest data relating to the server's processes and users
**/  
  public function get_proc_info()
  {
    return $this->get_update(GKRELLM_UPDATE_PROC);
  }

/**
 * @return gkrellm_update_sensor The latest data relating to the server's sensors
**/  
  public function get_sensors_info()
  {
    return $this->get_update(GKRELLM_UPDATE_SENSORS);
  }

/**
 * @return gkrellm_update_swap The latest data relating to the server's swap
**/  
  public function get_swap_info()
  {
    return $this->get_update(GKRELLM_UPDATE_SWAP);
  }
  
/**
 * @return gkrellm_update_cpu The latest data relating to the server's clock
**/  
  public function get_time_info()
  {
    return $this->get_update(GKRELLM_UPDATE_TIME);
  }

/**
 * @return gkrellm_update_cpu The latest data relating to the server's uptime
**/  
  public function get_uptime_info()
  {
    return $this->get_update(GKRELLM_UPDATE_UPTIME);
  }

/**
 * @return string The server's hostname as reported by gkrellmd
**/  
  public function get_hostname()
  {
    return $this->s_hostname;
  }
  
/**
 * @return string The server's sysname as reported by gkrellmd
**/  
  public function get_sysname()
  {
    return $this->s_sysname;
  }

/**
 * @param int $i_type The type of update to get
 * @return gkrellm_update The latest update of the type specified by $i_type (see gkrellm update classes for appropriate constants).
 * @see gkrellm_update
**/  
  public function get_update($i_type)
  {
    return $this->a_updates[$i_type];
  }
  
  private function add_update($o_update)
  {
    if($o_update)
    {
      $this->a_updates[$o_update->get_type()] = $o_update;
    }
  }
  
/**
 * @param string $s_host 
**/
  public function set_host($s_host)
  {
    $this->s_host = $s_host;
  }
  
/**
 * @param int $i_port
**/
  public function set_port($i_port)
  {
    $this->i_port = $i_port;
  }
  
/**
 * @param string $s_string The version string to send to gkrellmd (default set in php-gkrellm config file)
 * @see gkrellm_client_config.inc.php
**/
  public function set_version_string($s_string)
  {
    $this->s_version_string = $s_string;
  }
  
  private function get_gkrellmd_setup()
  {
    //expects <gkrellmd_setup> as the first line
    $s_line = $this->get_next_line();
    if(strcmp('<gkrellmd_setup>', $s_line))
    {
      return false;
    }
    
    $s_line = $this->get_next_line();
    while($s_line !== false && strcmp($this->s_last_line, '</gkrellmd_setup>'))
    {
      if($this->get_next_setup() === false)
        return false;
    }
    
    return($s_line !== false);
  }

  private function get_next_line()
  {
    $m_return = fgets($this->r_socket);
    if($m_return === false)
    {
      $this->disconnect();
      return false;
    }
    else
      return trim($m_return);
  }

  private function get_initial_update()
  {
    $s_line = $this->get_next_line();
    
    while($s_line !== false && !$s_line)
    {
      $s_line = $this->get_next_line();
    }
    
    //line should be <initial_update>
    if(strcmp('<initial_update>', $s_line))
      return false;
      
    //get lines until </initial_update> is reached

    while($o_update !== false && strcmp($this->s_last_line, '</initial_update>'))
    {
      $this->get_next_update();
    }
    
    return($s_line !== false);
  }
  
  private function set_last_line($s_line)
  {
    $this->s_last_line = $s_line;
  }
  
  private function get_next_setup()
  {
    //$last_line_read indicates the setup type
    
    //dependent on update type, read lines until new setup type is found
    $i_setup_type = gkrellm_update::get_update_type($this->s_last_line);
    
    if(!($o_update = $this->get_update($i_setup_type)))
    {
      $o_update = gkrellm_update::new_update($this->s_last_line);
      
      if($o_update === false)
        return false;
    }
        
    $s_line = $this->get_next_line();

    if($o_update)
      $o_update->initialise_update();
      
    while($s_line !== false && (!$s_line || $s_line{0} != '<'))
    {
      if($o_update)
      {
        $o_update->process_setup_line($s_line);
      }
      else
      {
        switch($this->s_last_line)
        {
          case '<version>':
            $this->s_server_version = $s_line;
            break;
          case '<decimal_point>':
            $this->s_decimal_point = $s_line;
            break;
          case '<hostname>':
            $this->s_hostname = $s_line;
            break;
          case '<sysname>':
            $this->s_sysname = $s_line;
            break;
          case '<io_timeout>':
            $this->i_io_timeout = (int)$s_line;
            break;
          case '<reconnect_timeout>':
            $this->i_reconnect_timeout = (int)$s_line;
            break;
        }
      }
      $s_line = $this->get_next_line();
    }
    
    if($s_line === false)
    {
      return false;
    }
    else
    {
      $this->set_last_line($s_line);
      $this->add_update($o_update);
      return $o_update;
    }
  }

/**
 * @return bool true if connected to the server
**/
  public function connected()
  {
    return $this->r_socket != false;
  }

/**
 * @return gkrellm_update
**/
  public function get_next_update()
  {
    //$last_line_read indicates the type of update

    //dependent on update type, read lines until new update type is found

    if(!$this->connected())
      return false;

    if(!($o_update = $this->get_update(gkrellm_update::get_update_type($this->s_last_line))))
      $o_update = gkrellm_update::new_update($this->s_last_line);

    $s_line = $this->get_next_line();

    if($o_update)
      $o_update->initialise_update();

    while($s_line !== false && (!$s_line || $s_line{0} != '<'))
    {
      if(!$this->connected())
      {
        $this->set_last_error('Disconnected from the server');
        return false;
      }

      if($o_update)
        $o_update->process_line($s_line, $this->s_last_line);
      $s_line = $this->get_next_line();
    }

    if($s_line === false)
    {
      return false;
    }
    else
    {
      $this->set_last_line($s_line);
      $this->add_update($o_update);

      return $o_update;
    }
  }
  
/**
 * Disconnect from the server.
**/
  public function disconnect()
  {
    if($this->connected())
    {
      @fclose($this->r_socket);
      $this->r_socket = null;
    }
  }
  
/**
 * Establish a connection to the server. This connection is automatically terminated once
 * the initial update has been retrieved from the server, unless $b_remain_connected is true.
 * @return bool false on connection/data error, true otherwise
 * @param bool $b_remain_connected Specifies whether or not to disconnect once the initial update has been retrieved
**/
  public function connect($b_remain_connected = false)
  {
    $this->r_socket = @fsockopen($this->s_host, $this->i_port, $i_errno, $s_errstring, $this->f_timeout);
    if($this->r_socket !== false)
    {
      fwrite($this->r_socket, $this->s_version_string);
      
      if(!$this->get_gkrellmd_setup())
      {
        $this->set_last_error('Error getting gkrellmd setup');
        return false;
      }
      
      if(!$this->get_initial_update())
      {
        $this->set_last_error('Error getting intial update');
        return false;
      }
      
      if(!$b_remain_connected)
        $this->disconnect();
      return true;
    }
    else
    {
      $this->set_last_error('Unable to connect socket: '.$s_errstring);
      return false;
    }
  }
  
  private function set_last_error($s_error)
  {
    $this->s_last_error = $s_error;
  }
  
/**
 * @return string A textual description of the last error that occurred
**/
  public function get_last_error()
  {
    return $this->s_last_error;
  }
  
}

?>
