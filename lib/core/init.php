<?php

ini_set('display_errors', 'On');

define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS);

include(__DIR__ . DS . 'config.php');

function load_main_classes($name)
{
  global $config_class_dirs;

  foreach($config_class_dirs as $d) {
    if (file_exists(PU_DIR . $d . DS . $name . '.php')) {
      include_once(PU_DIR . $d . DS . $name . '.php');
      break;
    }
  }
}

spl_autoload_register('load_main_classes');


$Routing  = new puRouting(new puRequest, new puSession, new puSecurity);

$Routing->handle();
