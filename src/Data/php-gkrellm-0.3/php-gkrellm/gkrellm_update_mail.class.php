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

class gkrellm_mailbox
{
  private $s_path;
  private $i_total;
  private $i_new;

  public function __construct($s_path, $i_total, $i_new)
  {
    $this->s_path = $s_path;
    $this->i_total = $i_total;
    $this->i_new = $i_new;
  }
  
  public function set_total($i_total)
  {
    $this->i_total = $i_total;
  }

  public function set_new($i_new)
  {
    $this->i_new = $i_new;
  }

}

/**
 * @see GKRELLM_UPDATE_MAIL
**/
class gkrellm_update_mail extends gkrellm_update
{

  private $a_mailboxes;

/**
 * @ignore
**/
  public function __construct()
  {
    $this->i_type = GKRELLM_UPDATE_MAIL;
  }

/**
 * @return array An array of gkrellm_mailbox objects (one per mailbox on the server)
 * @see gkrellm_mailbox
**/
  public function get_mailboxes()
  {
    return $this->a_mailboxes;
  }

/**
 * @return gkrellm_mailbox A gkrellm_mailbox object corresponding to the path specified by $s_path
 * @param string $s_path The path of the desired mailbox
**/
  public function get_mailbox($s_path)
  {
    return $this->a_mailboxes[$s_path];
  }

/**
 * @ignore
**/
  public function process_line($s_line, $s_setup_line = null)
  {
    $a_line = self::split_line($s_line);
    
    if($this->a_mailboxes[$a_line[0]])
    {
      $this->a_mailboxes[$a_line[0]]->set_total($a_line[1]);
      $this->a_mailboxes[$a_line[0]]->set_new($a_line[2]);
    }
    else
    {
      $this->a_mailboxes[$a_line[0]] = new gkrellm_mailbox($a_line[0], $a_line[1], $a_line[2]);
    }
  }
  
/**
 * @ignore
**/
  public function process_setup_line($s_line)
  {
  }
  
/**
 * @ignore
**/
  public function initialise_update()
  {
  }

}

?>
