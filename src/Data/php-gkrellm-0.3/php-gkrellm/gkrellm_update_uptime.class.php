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

class gkrellm_update_uptime extends gkrellm_update
{

  private $i_minute;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_UPTIME;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    $this->i_minute = $a_line[0];
  }
  
/**
 * @return int The server's uptime, in minutes
**/
  public function get_uptime()
  {
    return $this->i_minute;
  }
  
/**
 * @return string The server's uptime formatted (e.g. "2 days, 1 hour, 3 minutes")
**/
  public function get_formatted_uptime()
  {
    $i_uptime = $this->get_uptime();

    $i_minutes = $i_uptime % 60;
    $i_hours = ($i_uptime / 60.0) % 24;
    $i_days = floor($i_uptime / 60 / 24);

    $s_uptime = $i_minutes.' minute'.($i_minutes > 1 ? 's' : '');
    if($i_hours)
    {
      $s_uptime = $i_hours.' hour'.($i_hours > 1 ? 's' : '').', '.$s_uptime;

      if($i_days)
      {
        $s_uptime = $i_days.' day'.($i_days > 1 ? 's' : '').', '.$s_uptime;
      }
    }

    return $s_uptime;
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
