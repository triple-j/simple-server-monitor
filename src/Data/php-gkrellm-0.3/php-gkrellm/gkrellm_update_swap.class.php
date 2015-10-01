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
 * @see GKRELLM_UPDATE_SWAP
**/
class gkrellm_update_swap extends gkrellm_update
{

  private $i_total;
  private $i_used;
  private $i_in;
  private $i_out;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_SWAP;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    $this->i_total = $a_line[0];
    $this->i_used = $a_line[1];
    $this->i_in = $a_line[2];
    $this->i_out = $a_line[3];
  }
  
/**
 * @return int Total swap memory in bytes
**/
  public function get_total()
  {
    return $this->i_total;
  }

/**
 * @return int Total used swap memory in bytes
**/
  public function get_used()
  {
    return $this->i_used;
  }

/**
 * @return int Swap in
**/
  public function get_in()
  {
    return $this->i_in;
  }

/**
 * @return int Swap out
**/
  public function get_out()
  {
    return $this->i_out;
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
