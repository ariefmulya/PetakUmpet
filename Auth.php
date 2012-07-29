<?php

namespace PetakUmpet;

class Auth {

  private $backend;
  
  public function __construct($backend='\\PetakUmpet\\User')
  {
    $this->backend = new $backend;

  }

  public function __call($name, $args)
  {
    if (substr($name, 0, 5) == 'proxy') {
      $func_name = lcfirst(substr, $name, 5);
      return $this->backend->$func_name($args);
    }
  }
  
}