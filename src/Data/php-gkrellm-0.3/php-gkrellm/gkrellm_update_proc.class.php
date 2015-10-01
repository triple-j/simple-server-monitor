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
 * @see GKRELLM_UPDATE_PROC
**/
class gkrellm_update_proc extends gkrellm_update
{

  private $i_processes;
  private $i_running;
  private $i_forks;
  private $i_load;
  private $i_users;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_PROC;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    $this->i_processes = $a_line[0];
    $this->i_running = $a_line[1];
    $this->i_forks = $a_line[2];
    $this->i_load = $a_line[3];
    $this->i_users = $a_line[4];
  }
  
/**
 * @return int The current load
**/
  public function get_load()
  {
    return $this->i_load;
  }

/**
 * @return int The number of processes currently running
**/
  public function get_processes()
  {
    return $this->i_processes;
  }

/**
 * @return int The current number of users
**/
  public function get_users()
  {
    return $this->i_users;
  }

/**
 * @return int The current number of forks
**/
  public function get_forks()
  {
    return $this->i_forks;
  }

/**
 * @return int ?
**/
  public function get_running()
  {
    return $this->i_running;
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
  }
}

?>
