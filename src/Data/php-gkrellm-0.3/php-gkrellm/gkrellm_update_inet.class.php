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

class gkrellm_inet_connection
{

  private $s_remote_ip;
  private $i_local_port;
  private $i_remote_port;
  private $b_added;
  
/**
 * @ignore
**/
  public function __construct($s_ip, $i_local_port, $i_remote_port, $b_added = true)
  {
    $this->s_remote_ip = $s_ip;
    $this->i_local_port = $i_local_port;
    $this->i_remote_port = $i_remote_port;
    $this->b_added = $b_added;
  }

/**
 * @return string The connection's remote IP address
**/
  public function get_remote_ip()
  {
    return $this->s_remote_ip;
  }
  
/**
 * @return int The connection's local port
**/
  public function get_local_port()
  {
    return $this->i_local_port;
  }
  
/**
 * @return int The connection's remote port
**/
  public function get_remote_port()
  {
    return $this->i_remote_port;
  }
  
/**
 * @return bool If false, this connection has just been closed (only applicable when in the list of connection changes)
**/
  public function added()
  {
    return $this->b_added;
  }

}

/**
 * @see GKRELLM_UPDATE_INET
**/
class gkrellm_update_inet extends gkrellm_update
{

  private $a_connections;
  private $a_connection_changes;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_INET;
  }

/**
 * @return array An array of gkrellm_inet_connection objects corresponding to the current list of active connections as reported by gkrellmd
 * @see gkrellm_inet_connection
**/
  public function get_connections()
  {
    return $this->a_connections;
  }
  
/**
 * @return array An array of gkrellm_inet_connection objects corresponding to the connections reported as being opened/closed in the last update from gkrellmd
 * @see gkrellm_inet_connection
**/
  public function get_connection_changes()
  {
    return $this->a_connection_changes;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    //format +/-0 localport ip:remoteport
    if($a_line[0]{1} == '0')
    {
      $a_remote = split(':', $a_line[2]);
      
      $i_local_port = hexdec($a_line[1]);
      $s_ip = $a_remote[0];
      $i_remote_port = hexdec($a_remote[1]);
    
      if($a_line[0]{0} == '+')
      {
        //new connection
        $o_connection = new gkrellm_inet_connection($s_ip, $i_local_port, $i_remote_port);
        $this->a_connections[] = $o_connection;
        $this->a_connection_changes[] = $o_connection;
      }
      else
      {
        //closed connection
        $o_connection = new gkrellm_inet_connection($s_ip, $i_local_port, $i_remote_port, false);
        $this->a_connection_changes[] = $o_connection;
        foreach($this->get_connections() as $i_key => $o_conn)
        {
          if($o_conn->get_remote_port() == $i_remote_port && $o_conn->get_local_port() == $i_local_port
          && $o_conn->get_remote_ip() == $s_ip)
            unset($this->a_connections[$i_key]);
        }
      }
    }
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
    $this->a_connection_changes = array();
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
  }
}

?>
