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

class gkrellm_battery
{

  private $b_present;
  private $b_online;
  private $b_charging;
  private $i_percent;
  private $i_time_left;
  private $i_number;

/**
 * @ignore
**/
  public function __construct($b_present, $b_online, $b_charging, $i_percent, $i_time_left, $i_number)
  {
    $this->b_present = $b_present;
    $this->b_online = $b_online;
    $this->b_charging = $b_charging;
    $this->i_percent = $i_percent;
    $this->i_time_left = $i_time_left;
    $this->i_number = $i_number;
  }
  
/**
 * @return bool Is this battery present?
**/
  public function present()
  {
    return $this->b_present;
  }

/**
 * @return bool Is this battery online?
**/
  public function online()
  {
    return $this->b_online;
  }

/**
 * @return bool Is this battery charging?
**/
  public function charging()
  {
    return $this->b_charging;
  }

/**
 * @return int
**/
  public function get_percent()
  {
    return $this->i_percent;
  }

/**
 * @return int
**/
  public function get_time_left()
  {
    return $this->i_time_left;
  }

/**
 * @return int
**/
  public function get_number()
  {
    return $this->i_number;
  }
}

/**
 * @see GKRELLM_UPDATE_BATTERY
**/
class gkrellm_update_battery extends gkrellm_update
{

  private $a_batteries;
  private $b_battery_available;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_TIME;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    $this->a_batteries[$a_line[5]] = new gkrellm_battery($a_line[0], $a_line[1], $a_line[2],
      $a_line[3], $a_line[4], $a_line[5]);
  }
  
/**
 * @return int
**/
  public function battery_available()
  {
    return $this->b_battery_available;
  }
  
/**
 * @return array An array of gkrellm_battery objects (on for each battery in the server)
 * @see gkrellm_battery
**/
  public function get_batteries()
  {
    return $this->a_batteries;
  }
  
/**
 * @return gkrellm_battery A gkrellm_battery object corresponding to the battery specified by $i_num
 * @param int $i_num The desired battery's number
**/
  public function get_battery($i_num)
  {
    return $this->a_batteries[$i_num];
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
    $a_line = self::split_line($s_line);

    if(strcmp($a_line[0], 'apm_available') === 0 || strcmp($a_line[0], 'battery_available') === 0)
    {
      $this->b_battery_available = true;
    }
  }
}

?>
