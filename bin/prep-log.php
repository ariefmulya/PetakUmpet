<?php

define('DS', DIRECTORY_SEPARATOR);
define('TARGETDIR', __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS);

if (!isset($argv[1]) || $argv[1] == '') {
  echo "  init-project: Please provide application name\n";
  exit();
}


$resdir= TARGETDIR.DS.'app'.DS.$argv[1].DS.'res';
$logdir= TARGETDIR.DS.'app'.DS.$argv[1].DS.'res'.DS.'log';

if (!is_dir($resdir)) {
  mkdir($resdir);
}
if (!is_dir($logdir)) {
  mkdir($logdir);
}

touch($logdir.DS.'app.log');
chmod($logdir.DS.'app.log', 0666);

echo "Log for '" . $argv[1] . "' prepared.\n";

