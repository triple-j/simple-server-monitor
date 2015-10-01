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
 * The get_last_* functions all return the difference between the last and second to last updates
**/
class gkrellm_cpu
{

  private $i_cpu;
  private $i_nice;
  private $i_sys;
  private $i_user;
  private $i_idle;
  
  private $i_last_nice;
  private $i_last_sys;
  private $i_last_user;
  private $i_last_idle;

/**
 * @ignore
**/
  public function __construct($i_cpu, $i_user, $i_nice, $i_sys, $i_idle)
  {
    $this->i_cpu = $i_cpu;
    $this->i_nice = $i_nice;
    $this->i_sys = $i_sys;
    $this->i_user = $i_user;
    $this->i_idle = $i_idle;
  }
  
/**
 * @return int The total new ticks for this CPU
**/
  public function get_last_total()
  {
    return $this->i_last_user + $this->i_last_nice + $this->i_last_sys + $this->i_last_idle;
  }
  
/**
 * @return int The total new 'user' ticks reported in the last update for this CPU (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_user($b_percentage = false)
  {
    if($b_percentage)
    {
      if(!$this->get_last_total())
        return 0;
      else
        return (100 / $this->get_last_total()) * $this->i_last_user;
    }
    else
      return $this->i_last_user;
  }
  
/**
 * @return int The total new 'nice' ticks reported in the last update for this CPU (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_nice($b_percentage = false)
  {
    if($b_percentage)
    {
      if(!$this->get_last_total())
        return 0;
      else
        return (100 / $this->get_last_total()) * $this->i_last_nice;
    }
    else
      return $this->i_last_nice;
  }
  
/**
 * @return int The total new 'sys' ticks reported in the last update for this CPU (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_sys($b_percentage = false)
  {
    if($b_percentage)
    {
      if(!$this->get_last_total())
        return 0;
      else
        return (100 / $this->get_last_total()) * $this->i_last_sys;
    }
    else
      return $this->i_last_sys;
  }
  
/**
 * @return int The total new 'idle' ticks reported in the last update for this CPU (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_idle($b_percentage = false)
  {
    if($b_percentage)
    {
      if(!$this->get_last_total())
        return 0;
      else
        return (100 / $this->get_last_total()) * $this->i_last_idle;
    }
    else
      return $this->i_last_idle;
  }

/**
 * @return int The total cumulative ticks for this CPU
**/
  public function get_total()
  {
    return $this->i_user + $this->i_nice + $this->i_sys + $this->i_idle;
  }
  
/**
 * @return int The CPU number
**/
  public function get_cpu()
  {
    return $this->i_cpu;
  }
  
/**
 * @return int This CPU's cumulative 'user' ticks
**/
  public function get_user()
  {
    return $this->i_user;
  }
  
/**
 * @return int This CPU's cumulative 'idle' ticks
**/
  public function get_idle()
  {
    return $this->i_idle;
  }

/**
 * @return int This CPU's cumulative 'sys' ticks
**/
  public function get_sys()
  {
    return $this->i_sys;
  }

/**
 * @return int This CPU's cumulative 'nice' ticks
**/
  public function get_nice()
  {
    return $this->i_nice;
  }
  
/**
 * @ignore
**/
  public function set_user($i_arg)
  {
    $this->i_last_user = $i_arg - $this->i_user;
    $this->i_user = $i_arg;
  }
  
/**
 * @ignore
**/
  public function set_idle($i_arg)
  {
    $this->i_last_idle = $i_arg - $this->i_idle;
    $this->i_idle = $i_arg;
  }

/**
 * @ignore
**/
  public function set_nice($i_arg)
  {
    $this->i_last_nice = $i_arg - $this->i_nice;
    $this->i_nice = $i_arg;
  }

/**
 * @ignore
**/
  public function set_sys($i_arg)
  {
    $this->i_last_sys = $i_arg - $this->i_sys;
    $this->i_sys = $i_arg;
  }
}

/**
 * @see GKRELLM_UPDATE_CPU
**/
class gkrellm_update_cpu extends gkrellm_update
{

  private $a_cpus;
  private $i_cpu_count;
  private $b_nice_time_unsupported = false;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_CPU;
  }

/**
 * @return gkrellm_cpu
 * @param int $i_cpu The index (0, 1, 2...) of the desired CPU
**/
  public function get_cpu($i_cpu)
  {
    return $this->a_cpus[$i_cpu];
  }

/**
 * @return array An array of gkrellm_cpu objects (one for each CPU in the server)
 * @see gkrellm_cpu
**/
  public function get_cpus()
  {
    return $this->a_cpus;
  }

/**
 * @return int The number of CPUs in the server
**/
  public function get_num_cpus()
  {
    return $this->i_cpu_count;
  }

/**
 * @return int The total new ticks reported in the last update for all CPUS
**/
  public function get_last_total_ticks()
  {
    return $this->get_last_total_user() + $this->get_last_total_sys() + 
      $this->get_last_total_idle() + $this->get_last_total_nice();
  }

/**
 * @return mixed The total number of new 'user' ticks for all CPUs (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_total_user($b_percentage = false)
  {
    if($a_cpus = $this->get_cpus())
      foreach($a_cpus as $o_cpu)
      {
        $i_total += $o_cpu->get_last_user();
      }
    else
      return 0;

    if($b_percentage)
      return (100 / $this->get_last_total_ticks()) * $i_total;
    else
      return $i_total;
  }

/**
 * @return mixed The total number of new 'sys' ticks for all CPUs (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_total_sys($b_percentage = false)
  {
    if($a_cpus = $this->get_cpus())
      foreach($a_cpus as $o_cpu)
      {
        $i_total += $o_cpu->get_last_sys();
      }
    else
      return 0;

    if($b_percentage)
      return (100 / $this->get_last_total_ticks()) * $i_total;
    else
      return $i_total;
  }

/**
 * @return mixed The total number of new 'nice' ticks for all CPUs (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_total_nice($b_percentage = false)
  {
    if($a_cpus = $this->get_cpus())
      foreach($a_cpus as $o_cpu)
      {
        $i_total += $o_cpu->get_last_nice();
      }
    else
      return 0;

    if($b_percentage)
      return (100 / $this->get_last_total_ticks()) * $i_total;
    else
      return $i_total;
  }

/**
 * @return mixed The total number of new 'idle' ticks for all CPUs (or, if $b_percentage is true, the percentage of total ticks this represents)
 * @param bool $b_percentage If true, returns the percentage of total ticks this represents
**/
  public function get_last_total_idle($b_percentage = false)
  {
    if($a_cpus = $this->get_cpus())
      foreach($a_cpus as $o_cpu)
      {
        $i_total += $o_cpu->get_last_idle();
      }
    else
      return 0;


    if($b_percentage)
      return (100 / $this->get_last_total_ticks()) * $i_total;
    else
      return $i_total;
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    if(!$this->a_cpus[$a_line[0]])
    {
      $this->a_cpus[$a_line[0]] = new gkrellm_cpu($a_line[0], $a_line[1], $a_line[2], $a_line[3], $a_line[4]);
    }
    else
    {
      $this->a_cpus[$a_line[0]]->set_user($a_line[1]);
      $this->a_cpus[$a_line[0]]->set_nice($a_line[2]);
      $this->a_cpus[$a_line[0]]->set_sys($a_line[3]);
      $this->a_cpus[$a_line[0]]->set_idle($a_line[4]);
    }
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
    $a_line = self::split_line($s_line);
    switch($a_line[0])
    {
      case  'n_cpus':
        $this->i_cpu_count = (int)$a_line[1];
        break;
      case 'nice_time_unsupported':
        $this->b_nice_time_unsupported = true;
        break;
      case 'cpu_instance':
        //FIXME
        break;
    }
  }

/**
 * @ignore
**/
  public function initialise_update()
  {
  }

}

?>
