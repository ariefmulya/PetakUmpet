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
  if (is_dir($src) && ( (!is_dir($dst) && !is_file($dst)) || $mode == '--reset')  {
    echo "    - Initialize " . basename($src) . "\n";
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file) {
      if ($file != "." && $file != "..") rcopy("$src/$file", "$dst/$file");
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

echo "  init-project: Setting up Application... $app\n"
rename(TARGETDIR . DS . 'app' . DS . 'AppName', TARGETDIR . DS . 'app' . DS . $app);

echo "  init-project: Fixing log mode\n";
chmod (TARGETDIR . 'res' . DS . 'log' . DS . 'app.log', '0666');

echo "\n  init-project: Finished.\n"
