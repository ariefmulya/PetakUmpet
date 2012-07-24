<?php

namespace PetakUmpet;

ini_set('display_errors', 'On');

define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS);

include(__DIR__ . DS . 'Loader.php');

$loader = new Loader;
$loader->register();

$process  = new Process(Singleton::acquire('\\PetakUmpet\\Request'), Singleton::acquire('\\PetakUmpet\\Session'));
$process->run();