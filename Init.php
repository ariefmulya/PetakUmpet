<?php

namespace PetakUmpet;

define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS);

ini_set('display_errors', 'On');
ini_set('upload_tmp_dir', PU_DIR . 'web' . DS . 'uploads' . DS . 'tmp');

include(__DIR__ . DS . 'Loader.php');

class Init {

  public static function run() 
  {
    $loader = new Loader;
    $loader->register();

    $request = Singleton::acquire('\\PetakUmpet\\Request');
    $session = Singleton::acquire('\\PetakUmpet\\Session');
    $config  = Singleton::acquire('\\PetakUmpet\\Config');

    $request->setApplication($config->getApplication($request->getPathInfo()));
    $process  = new Process($request, $session, $config);
                            
    return $process->run() ;
  }
}



