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

class gkrellm_mount
{

  private $s_dir;
  private $s_dev;
  private $s_type;
  private $i_blocks;
  private $i_blocks_available;
  private $i_blocks_free;
  private $i_block_size;

/**
 * @ignore
**/
  public function __construct($s_dir, $s_dev, $s_type, $i_blocks, $i_blocks_available, $i_blocks_free, $i_block_size)
  {
    $this->s_dir = $s_dir;
    $this->s_dev = $s_dev;
    $this->s_type = $s_type;
    $this->i_blocks = $i_blocks;
    $this->i_blocks_available = $i_blocks_available;
    $this->i_blocks_free = $i_blocks_free;
    $this->i_block_size = $i_block_size;
  }
  
/**
 * @return string The mount point of this mounted filesystem
**/
  public function get_dir()
  {
    return $this->s_dir;
  }

/**
 * @return string The device name of this mounted filesystem
**/
  public function get_dev()
  {
    return $this->s_dev;
  }

/**
 * @return string The filesystem type of this mount
**/
  public function get_type()
  {
    return $this->s_type;
  }

/**
 * @return int The total number of blocks on this mounted filesystem
**/
  public function get_blocks()
  {
    return $this->i_blocks;
  }

/**
 * @return int The number of blocks available on this mounted filesystem
**/
  public function get_blocks_available()
  {
    return $this->i_blocks_available;
  }

/**
 * @return int The number of free blocks on this mounted filesystem
**/
  public function get_blocks_free()
  {
    return $this->i_blocks_free;
  }

/**
 * @return int The filesystem's block size
**/
  public function get_block_size()
  {
    return $this->i_block_size;
  }
  
/**
 * @return int The number of bytes free on this filesystem
**/
  public function get_bytes_free()
  {
    return $this->get_blocks_available() * $this->get_block_size();
  }

}

/**
 * @see GKRELLM_UPDATE_FSFSMOUNT
**/
class gkrellm_update_fsmounts extends gkrellm_update
{

  private $a_mounts;
  private $a_mount_changes;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_FSMOUNTS;
  }

/**
 * @return array An array of gkrellm_mount objects (one for each mounted filesystem on the server)
 * @see gkrellm_mount
**/
  public function get_mounts()
  {
    return $this->a_mounts;
  }
  
/**
 * @return gkrellm_mount A gkrellm_mount object representing the mounted filesystem specified by $s_dir
 * @param string $s_dir Specified the mount point of the desired filesystem
**/
  public function get_mount($s_dir)
  {
    return $this->a_mounts[$s_dir];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    if(strcmp($s_line, '.clear') === 0)
    {
      $this->a_mounts = array();
    }
    else
    {
      $a_line = self::split_line($s_line);
      
      $this->a_mounts[$a_line[0]] = new gkrellm_mount($a_line[0], $a_line[1], $a_line[2], 
        $a_line[3], $a_line[4], $a_line[5], $a_line[6]);
      $this->a_mount_changes[] = $this->a_mounts[$a_line[0]];
    }
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
    $this->a_mount_changes = array();
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
  }
}

?>
