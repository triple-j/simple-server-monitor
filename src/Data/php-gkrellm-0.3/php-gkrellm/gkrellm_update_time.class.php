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

/**
 * @see GKRELLM_UPDATE_TIME
**/
class gkrellm_update_time extends gkrellm_update
{

  private $i_second;
  private $i_minute;
  private $i_hour;
  private $i_mday;
  private $i_month;
  private $i_year;
  private $i_wday;
  private $i_yday;
  private $b_isdst;

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
    
    $this->i_second = $a_line[0];
    $this->i_minute = $a_line[1];
    $this->i_hour = $a_line[2];
    $this->i_mday = $a_line[3];
    $this->i_month = $a_line[4];
    $this->i_year = $a_line[5];
    $this->i_wday = $a_line[6];
    $this->i_yday = $a_line[7];
    $this->b_isdst = $a_line[8];
  }
  
/**
 * @return int The 'second' portion of the server's current time
**/
  public function get_second()
  {
    return $this->i_second;
  }

/**
 * @return int The 'minute' portion of the server's current time
**/
  public function get_minute()
  {
    return $this->i_minute;
  }

/**
 * @return int The 'hour' portion of the server's current time
**/
  public function get_hour()
  {
    return $this->i_hour;
  }

/**
 * @return int The 'day of month' portion of the server's current time
**/
  public function get_mday()
  {
    return $this->i_mday;
  }

/**
 * @return int The 'month' portion of the server's current time
**/
  public function get_month()
  {
    return $this->i_month + 1;
  }

/**
 * @return int The 'year' portion of the server's current time
**/
  public function get_year()
  {
    return $this->i_year + 1900;
  }
  
/**
 * @return int The 'day of week' portion of the server's current time
**/
  public function get_wday()
  {
    return $this->i_wday;
  }

/**
 * @return int The 'day of year' portion of the server's current time
**/
  public function get_yday()
  {
    return $this->i_yday;
  }

/**
 * @return bool Is this daylight saving time?
**/
  public function isdst()
  {
    return $this->b_isdst;
  }

/**
 * @ignore
**/
  public function get_unix_timestamp()
  {
    return mktime($this->get_hour(), $this->get_minute(), $this->get_second(), $this->get_month(), 
      $this->get_mday(), $this->get_year(), $this->isdst());
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
    $this->process_line($s_line);
  }
}

?>
