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

class gkrellm_disk
{

  private $s_name;
  private $i_block_read;
  private $i_block_write;
  private $b_virtual;
  private $s_parent;
  
/**
 * @ignore
**/
  public function __construct($s_name, $i_block_read, $i_block_write, $b_virtual, $s_parent = null)
  {
    $this->s_name = $s_name;
    $this->i_block_read = $i_block_read;
    $this->i_block_write = $i_block_write;
    $this->b_virtual = $b_virtual;
    $this->s_parent = $s_parent;
  }
  
/**
 * @return string The disk's device name
**/
  public function get_name()
  {
    return $this->s_name;
  }

/**
 * @return int The number of blocks read
**/
  public function get_block_read()
  {
    return $this->i_block_read;
  }

/**
 * @return int The number of blocks written
**/
  public function get_block_write()
  {
    return $this->i_block_write;
  }

/**
 * @return bool Is this a virtual disk?
**/
  public function is_virtual()
  {
    return $this->b_virtual;
  }

/**
 * @return string The parent device (if any)
**/
  public function get_parent()
  {
    return $this->s_parent;
  }

}

/**
 * @see GKRELLM_UPDATE_DISK
**/
class gkrellm_update_disk extends gkrellm_update
{

  private $a_disks;
  private $a_disk_changes;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_DISK;
  }

/**
 * @ignore
**/
  public function __clone()
  {
    foreach($this->a_disks as $s_key => $o_disk)
    {
      $this->a_disks[$s_key] = clone $o_disk;
    }

    foreach($this->a_disk_changes as $s_key => $o_disk)
    {
      $this->a_disk_changes[$s_key] = clone $o_disk;
    }
  }

/**
 * @return int The total read bytes for all disks
**/
  public function get_total_read()
  {
    $i_total = 0;
    if($this->a_disks)
      foreach($this->a_disks as $o_disk)
        $i_total += $o_disk->get_block_read();
    return $i_total;
  }
  
/**
 * @return int The total written bytes for all disks
**/
  public function get_total_write()
  {
    $i_total = 0;
    if($this->a_disks)
      foreach($this->a_disks as $o_disk)
        $i_total += $o_disk->get_block_write();
    return $i_total;
  }

/**
 * @return array An array of gkrellm_disk objects (one for each disk in the server)
 * @see gkrellm_disk
**/
  public function get_disks()
  {
    return $this->a_disks;
  }
  
/**
 * @return gkrellm_disk
 * @param string $s_name The device name of the desired disk
**/
  public function get_disk($s_name)
  {
    return $this->a_disks[$s_name];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    $b_virtual = false;
    $s_parent = null;
    $s_name = $a_line[0];
    if(count($a_line) == 4)
    {
      if(strcmp($a_line[1], 'virtual') === 0)
        $b_virtual = true;
      else
        $s_parent = $a_line[1];
      $i_read = $a_line[2];
      $i_write = $a_line[3];
    }
    else
    {
      $i_read = $a_line[1];
      $i_write = $a_line[2];
    }
    
    $this->a_disks[$s_name] = new gkrellm_disk($s_name, $i_read, $i_write, $b_virtual, $s_parent);
    $this->a_disk_changes[] = $this->a_disks[$s_name];
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
    $this->a_disk_changes = array();
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
  }
}

?>
