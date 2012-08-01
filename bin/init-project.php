<?php

define('DS', DIRECTORY_SEPARATOR);
define('SKELDIR', __DIR__ . DS . '..' . DS . 'Skeleton' . DS);
define('TARGETDIR', SKELDIR . '..' . DS . '..' . DS . '..' . DS);


if (!isset($argv[1]) || $argv[1] == '') {
  echo "  init-project: Please provide application name\n";
  exit();
}

$app = $argv[1];
$mode = isset($argv[2]) ? $argv[2] : '--normal';
if ($mode=='') $mode='--normal';

function rcopy($src, $dst, $mode) {
  if (is_dir($src) && ( (!is_dir($dst) && !is_file($dst)) || $mode == '--reset'))  {
    echo "    - Initialize " . basename($src) . "\n";
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file) {
      if ($file != "." && $file != "..") rcopy("$src/$file", "$dst/$file", $mode);
    }
  }
  if (is_file($src) && !file_exists($dst)) copy($src, $dst);
}


// copying skeleton directories
// assuming project dir will be up two parents above here

$dirs = scandir(SKELDIR);
echo "  init-project: Initialize project directories\n";
foreach ($dirs as $d) {
  if ($d != '.' && $d != '..') rcopy(SKELDIR . $d, TARGETDIR . $d, $mode) ;
}

// rename to the supplied application name

echo "  init-project: Setting up Application... $app\n" ;
rename(TARGETDIR . DS . 'app' . DS . 'AppName', TARGETDIR . DS . 'app' . DS . $app);

// updating AppName to provided application name


// in config file
$cfgfile = TARGETDIR . DS . 'config' . DS . 'Config.php';
file_put_contents($cfgfile,str_replace('AppName', $app, file_get_contents($cfgfile)));

// in form login file
$frmfile = TARGETDIR . DS . 'app' . DS . $app . DS . 'Form' . DS . 'LoginForm.php';
file_put_contents($cfgfile,str_replace('AppName', $app, file_get_contents($frmfile)));


// in application files
$appfiles = scandir (TARGETDIR . DS . 'app' . DS . $app . DS);
foreach ($appfiles as $f) {
  if ($f == '.' || $f == '..') continue;
  if (is_file(TARGETDIR . DS . 'app' . DS . $app . DS . $f) && strstr($f, 'Application') !== false) {
    file_put_contents(TARGETDIR . DS . 'app' . DS . $app . DS . $f, 
      str_replace('AppName', $app, 
        file_get_contents(TARGETDIR . DS . 'app' . DS . $app . DS . $f)));
  }
}

echo "  init-project: Fixing log mode\n";
chmod (TARGETDIR . DS . 'app' . DS . $app . DS . 'res' . DS . 'log' . DS . 'app.log', '0666');

echo "\n  init-project: Finished.\n";
