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

class gkrellm_fstab
{

  private $s_dir;
  private $s_dev;
  private $s_type;

/**
 * @ignore
**/
  public function __construct($s_dir, $s_dev, $s_type)
  {
    $this->s_dir = $s_dir;
    $this->s_dev = $s_dev;
    $this->s_type = $s_type;
  }
  
/**
 * @return string The mount point for this filesystem
**/
  public function get_dir()
  {
    return $this->s_dir;
  }
  
/**
 * @return The device name of this filesystem
**/
  public function get_dev()
  {
    return $this->s_dev;
  }
  
/**
 * @return string The type of this filesystem
**/
  public function get_type()
  {
    return $this->s_type;
  }

}

/**
 * @see GKRELLM_UPDATE_FSFSTAB
**/
class gkrellm_update_fsfstab extends gkrellm_update
{

  private $a_fstab;
  private $a_fstab_changes;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_FSFSTAB;
  }

/**
 @return array An array of gkrellm_fstab objects (one for each of the server's filesystems)
 @see gkrellm_fstab
**/
  public function get_all_fs()
  {
    return $this->a_fstab;
  }
  
/**
 * @return gkrellm_fstab A gkrellm_fstab object corresponding to the filesystem specified by $s_dir
 * @param string $s_dir Specifies the mount point of the desired filesystem
**/
  public function get_fs($s_dir)
  {
    return $this->a_fstab[$s_dir];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    if(strcmp($s_line, '.clear') === 0)
    {
      $this->a_fstab = array();
    }
    else
    {
      $a_line = self::split_line($s_line);
      
      $this->a_fstab[$a_line[0]] = new gkrellm_fstab($a_line[0], $a_line[1], $a_line[2]);
      $this->a_fstab_changes[] = $this->a_fstab[$a_line[0]];
    }
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
    $this->a_fstab_changes = array();
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
  }
}

?>
