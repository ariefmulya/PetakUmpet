<?php

namespace PetakUmpet;

abstract class Singleton {

  public static function acquire($class, $args=array())
  {
    static $instances = array();

    if (!array_key_exists($class, $instances)) {
      $instances[$class] = new $class();
    }

    return $instances[$class];
  }
}