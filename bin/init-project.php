<?php

define('DS', DIRECTORY_SEPARATOR);
define('SKELDIR', __DIR__ . DS . '..' . DS . 'Skeleton' . DS);
define('TARGETDIR', SKELDIR . '..' . DS . '..' . DS . '..' . DS);

function rcopy($src, $dst) {
  if (is_dir($src) && !is_dir($dst) && !is_file($dst)) {
    echo "  Initialize " . basename($src) . "\n";
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
echo "PetakUmpet Framework: Initialize project directories\n";
foreach ($dirs as $d) {
  if ($d != '.' && $d != '..') rcopy(SKELDIR . $d, TARGETDIR . $d) ;
}

echo "PetakUmpet Framework: fixing log mode\n";
chmod (TARGETDIR . 'res' . DS . 'log' . DS . 'app.log', '0666');

?>
