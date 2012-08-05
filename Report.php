<?php

namespace PetakUmpet;

class Report {

  private $title;
  private $header;
  private $columns;
  private $data;
  private $footer;

  private $formatter;

  public function __construct($title, $formatter='BaseFormatter')
  { 
    $this->title = $title;
    $this->header = array();
    $this->columns = array();
    $this->data = array();
    $this->footer = array();
    $this->formatter = $formatter;
  }

  public function __toString()
  {
    $cname = '\\PetakUmpet\\Report\\' . $this->formatter;
    $formatter = new $cname($this);
    return (string) $formatter;
  }

  function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') 
      return $this->get(lcfirst(substr($name, 3)));
    if (substr($name, 0,3) == 'set') 
      return $this->set(lcfirst(substr($name, 3)), $args[0]);
  }

  public function get($name, $default=null)
  {
    if (isset($this->$name))
      return $this->$name;
    return $default;
  }

  public function set($name, $value)
  {
    $this->$name = $value;
  }
}