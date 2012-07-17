<?php

namespace PetakUmpet;

abstract class Singleton {

  public static function acquire($class, $arg=null)
  {
    static $instances = array();

    if (!array_key_exists($class, $instances)) {
      $instances[$class] = new $class($arg);
    }

    return $instances[$class];
  }
}