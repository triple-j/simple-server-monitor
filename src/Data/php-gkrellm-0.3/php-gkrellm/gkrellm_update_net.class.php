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

class gkrellm_net_interface
{

  private $s_name;
  private $i_rx;
  private $i_tx;
  private $b_routed;

/**
 * @ignore
**/
  public function __construct($s_name)
  {
    $this->s_name = $s_name;
    $this->i_rx = $i_rx;
    $this->i_tx = $i_tx;
  }

/**
 * @return string This interface's name
**/
  public function get_name()
  {
    return $this->s_name;
  }

/**
 * @ignore
**/
  public function set_rx($i_rx)
  {
    $this->i_rx = $i_rx;
  }

/**
 * @ignore
**/
  public function set_tx($i_tx)
  {
    $this->i_tx = $i_tx;
  }
  
/**
 * @return int The number of received bytes
**/
  public function get_rx()
  {
    return $this->i_rx;
  }

/**
 * @return int The number of transmitted bytes
**/
  public function get_tx()
  {
    return $this->i_tx;
  }
  
/**
 * @return bool Is this interface routed?
**/
  public function is_routed()
  {
    return $this->b_routed;
  }
  
/**
 @ignore
**/
  public function set_routed($b_routed)
  {
    $this->b_routed = $b_routed;
  }
}

/**
 * @see GKRELLM_UPDATE_NET
**/
class gkrellm_update_net extends gkrellm_update
{

  private $a_interfaces;
  private $b_net_use_routed;
  private $s_net_timer_name;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_NET;
  }

/**
 * @ignore
**/
  public function __clone()
  {
    foreach($this->a_interfaces as $s_key => $o_interface)
    {
      $this->a_interfaces[$s_key] = clone $o_interface;
    }
  }
  
/**
 * @return array An array of net interface objects (one for each net interface in the server)
 * @see gkrellm_net_interface
**/
  public function get_interfaces()
  {
    return $this->a_interfaces;
  }

/**
 * @return bool
**/
  public function get_net_use_routed()
  {
    return $this->b_net_use_routed;
  }

/**
 * @return string
**/
  public function get_net_timer_name()
  {
    return $this->s_net_timer_name;
  }

/**
 * @return gkrellm_net_interface The network interface specified by $s_name
 * @param string $s_name The name of the desired interface
**/
  public function get_interface($s_name)
  {
    return $this->a_interfaces[$s_name];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    if(!$o_interface = $this->get_interface($a_line[0]))
    {
      $o_interface = new gkrellm_net_interface($a_line[0]);
    }
    
    if($s_setup_line == '<net_routed>')
    {
      $o_interface->set_routed($a_line[1]);
    }
    else
    {
      $o_interface->set_rx($a_line[1]);
      $o_interface->set_tx($a_line[2]);
    }
    
    $this->a_interfaces[$a_line[0]] = $o_interface;
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
    if(strcmp($s_line, 'net_use_routed') === 0)
    {
      $this->b_net_use_routed = true;
    }
    else
    {
      $a_line = self::split_line($s_line);
      if(strcmp($a_line[0], 'net_timer') === 0)
      {
        $this->s_net_timer_name = $a_line[1];
      }
    }
  }
}

?>
