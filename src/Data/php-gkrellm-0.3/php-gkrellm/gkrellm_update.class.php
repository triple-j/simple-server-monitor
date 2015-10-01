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

define('GKRELLM_UPDATE_CPU', 1);
define('GKRELLM_UPDATE_PROC', 2);
define('GKRELLM_UPDATE_MEM', 3);
define('GKRELLM_UPDATE_SWAP', 4);
define('GKRELLM_UPDATE_NET', 5);
define('GKRELLM_UPDATE_INET', 6);
define('GKRELLM_UPDATE_DISK', 7);
define('GKRELLM_UPDATE_FSMOUNTS', 8);
define('GKRELLM_UPDATE_FSFSTAB', 9);
define('GKRELLM_UPDATE_TIME', 10);
define('GKRELLM_UPDATE_UPTIME', 11);
define('GKRELLM_UPDATE_SENSORS', 12);
define('GKRELLM_UPDATE_MAIL', 13);
define('GKRELLM_UPDATE_BATTERY', 14);

abstract class gkrellm_update
{

/**
 * @ignore
**/
  protected $i_type;

/**
 * @ignore
**/
  public static function get_update_type($s_update_type)
  {
    switch($s_update_type)
    {
      case '<cpu_setup>':
      case '<cpu>':
        return GKRELLM_UPDATE_CPU;
        break;
      case '<proc>':
        return GKRELLM_UPDATE_PROC;
        break;
      case '<mem>':
        return GKRELLM_UPDATE_MEM;
        break;
      case '<swap>':
        return GKRELLM_UPDATE_SWAP;
        break;
      case '<net_setup>':
      case '<net>':
      case '<net_routed>':
        return GKRELLM_UPDATE_NET;
        break;
      case '<inet>':
        return GKRELLM_UPDATE_INET;
        break;
      case '<disk>':
        return GKRELLM_UPDATE_DISK;
        break;
      case '<fs_mounts>':
        return GKRELLM_UPDATE_FSMOUNTS;
        break;
      case '<fs_fstab>':
        return GKRELLM_UPDATE_FSFSTAB;
        break;
      case '<time>':
        return GKRELLM_UPDATE_TIME;
        break;
      case '<uptime>':
        return GKRELLM_UPDATE_UPTIME;
        break;
      case '<sensors_setup>':
      case '<sensors>':
        return GKRELLM_UPDATE_SENSORS;
        break;
      case '<mail>':
      case '<mail_setup>':
        return GKRELLM_UPDATE_MAIL;
        break;
      case '<battery_setup>':
      case '<battery>':
      case '<apm_setup>':
      case '<apm>':
        return GKRELLM_UPDATE_BATTERY;
        break;
      default:
        return null;
    }
  }
  
/**
 * @return int This update's type
**/
  public function get_type()
  {
    return $this->i_type;
  }
  
/**
 * @ignore
**/
  public static function new_update($s_update_type)
  {
    switch(self::get_update_type($s_update_type))
    {
      case GKRELLM_UPDATE_CPU:
        return new gkrellm_update_cpu();
        break;
      case GKRELLM_UPDATE_PROC:
        return new gkrellm_update_proc();
        break;
      case GKRELLM_UPDATE_MEM:
        return new gkrellm_update_mem();
        break;
      case GKRELLM_UPDATE_SWAP:
        return new gkrellm_update_swap();
        break;
      case GKRELLM_UPDATE_NET:
        return new gkrellm_update_net();
        break;
      case GKRELLM_UPDATE_INET:
        return new gkrellm_update_inet();
        break;
      case GKRELLM_UPDATE_DISK:
        return new gkrellm_update_disk();
        break;
      case GKRELLM_UPDATE_FSMOUNTS:
        return new gkrellm_update_fsmounts();
        break;
      case GKRELLM_UPDATE_FSFSTAB:
        return new gkrellm_update_fsfstab();
        break;
      case GKRELLM_UPDATE_TIME:
        return new gkrellm_update_time();
        break;
      case GKRELLM_UPDATE_UPTIME:
        return new gkrellm_update_uptime();
        break;
      case GKRELLM_UPDATE_SENSORS:
        return new gkrellm_update_sensors();
        break;
      case GKRELLM_UPDATE_MAIL:
        return new gkrellm_update_mail();
        break;
      case GKRELLM_UPDATE_BATTERY:
        return new gkrellm_update_battery();
        break;
    }
  }
  
/**
 * @ignore
**/
  protected static function split_line($s_line)
  {
    $a_line = explode(' ', $s_line);
    
    for($i = 0; $i < count($a_line); $i++)
    {
      if($a_line[$i]{0} == '"')
      {
        $s_temp = '';
        for($j = $i; $a_line[$j]{strlen($a_line[$j]) - 1} != '"'; $j++)
        {
          $s_temp .= ' '.$a_line[$j];
          $i++;
        }
        $s_temp .= ' '.$a_line[$j];
        
        $a_return[] = substr(trim($s_temp), 1, strlen($s_temp) - 3);
      }
      else
      {
        $a_return[] = $a_line[$i];
      }
    }

    return $a_return;
  }
  
/**
 * @ignore
**/
  abstract public function process_line($s_line, $s_update_type = null);
/**
 * @ignore
**/
  abstract public function process_setup_line($s_line);
/**
 * @ignore
**/
  abstract public function initialise_update();

}

?>
