<?php

namespace PetakUmpet;

// Autoloader class that has support for namespaces

class Loader {
  protected $namespaces;


  public function __construct()
  {
    $this->namespaces = array('\\');

  }

  public function register($prepend=false)
  {
    spl_autoload_register(array($this, 'load'), true, $prepend);
  }

  public function find($name)
  {
    $namespace = '\\';
    $ns_marker = strrpos($name, '\\');

    if ($ns_marker !== false) {
      $namespace = substr($name, 0, $ns_marker);
      if (! in_array($namespace, $this->namespaces)) {
        $this->namespaces[] = $namespace;
      }
      $name = substr($name, $ns_marker+1);
    }  

    $name = str_replace('_', DS, $name);

    $libdir = PU_DIR . 'lib';

    $file = $libdir . DS . str_replace('\\', DS, $namespace) . DS . $name . '.php';
    if (is_file($file)) {
      return $file;
    }
    
    // try again with existing namespaces
    foreach ($this->namespaces as $ns) {
      $file = $libdir . DS . str_replace('\\', DS, $ns) . DS . $name . '.php';
      if (is_file($file)) {
        return $file;
      }
    }

    return null;
  }

  public function load($name) 
  {
    if (null !== ($f = $this->find($name))) {
      require_once ($f);
    }
  }
}