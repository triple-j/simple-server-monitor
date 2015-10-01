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

class gkrellm_sensor
{
  private $i_type;
  private $s_basename;
  private $i_iodev;
  private $i_inter;
  private $f_factor;
  private $f_offset;
  private $s_vref;
  private $s_default_label;
  private $i_group;
  private $f_raw_value;

/**
 * @ignore
**/
  public function __construct($i_type, $s_basename, $i_iodev, $i_inter, $f_factor, 
    $f_offset, $s_vref, $s_default_label, $i_group)
  {
    $this->i_type = $i_type;
    $this->s_basename = $s_basename;
    $this->i_iodev = $i_iodev;
    $this->i_inter = $i_inter;
    $this->f_factor = $f_factor;
    $this->f_offset = $f_offset;
    $this->s_vref = $s_vref;
    $this->s_default_label = $s_default_label;
    $this->i_group = $i_group;
  }
  
/**
 * @ignore
**/
  public function set_raw_value($f_raw_value)
  {
    $this->f_raw_value = $f_raw_value;
  }
  
/**
 * @return int
**/
  public function get_type()
  {
    return $this->i_type;
  }

/**
 * @return string
**/
  public function get_basename()
  {
    return $this->s_basename;
  }

/**
 * @return int
**/
  public function get_iodev()
  {
    return $this->i_iodev;
  }

/**
 * @return int
**/
  public function get_inter()
  {
    return $this->i_inter;
  }

/**
 * @return float
**/
  public function get_factor()
  {
    return $this->f_factor;
  }

/**
 * @return float
**/
  public function get_offset()
  {
    return $this->f_offset;
  }

/**
 * @return string
**/
  public function get_vref()
  {
    return $this->s_vref;
  }

/**
 * @return string
**/
  public function get_default_label()
  {
    return $this->s_default_label;
  }

/**
 * @return int
**/
  public function get_group()
  {
    return $this->i_group;
  }

}

/**
 * @see GKRELLM_UPDATE_SENSOR
**/
class gkrellm_update_sensors extends gkrellm_update
{

  private $a_sensors;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_SENSORS;
  }

/**
 * @return array An array of gkrellm_sensor objects (one for each sensor in the server)
 * @see gkrellm_sensor
**/
  public function get_sensors()
  {
    return $this->a_sensors;
  }
  
/**
 * @return gkrellm_sensor A gkrellm_sensor objects corresponding to the basename specified by $s_basename
 * @param string The basename of the desired sensor
 * @see gkrellm_sensor
**/
  public function get_sensor($s_basename)
  {
    return $this->a_sensors[$s_basename];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    if($this->a_sensors[$a_line[1]])
      $this->a_sensors[$a_line[1]]->set_raw_value($a_line[5]);
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
    $a_line = self::split_line($s_line);
    
    $this->a_sensors[$a_line[1]] = new gkrellm_sensor($a_line[0], $a_line[1], $a_line[2], $a_line[3], $a_line[4],
      $a_line[5], $a_line[6], $a_line[7], $a_line[8]);
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
  }

}

?>
