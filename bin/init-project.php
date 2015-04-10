<?php

define('DS', DIRECTORY_SEPARATOR);
define('SKELDIR', __DIR__ . DS . '..' . DS . 'Skeleton' . DS . 'Project' . DS);
define('TARGETDIR', SKELDIR . '..' . DS . '..' . DS . '..' . DS . '..' . DS );

if (!isset($argv[1]) || $argv[1] == '') {
  echo "  init-project: Please provide application name\n";
  exit();
}

$app = $argv[1];
$mode = isset($argv[2]) ? $argv[2] : '--normal';
if ($mode=='') $mode='--normal';

function rcopy($src, $dst, $mode="--normal") {
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


//////////////////////////////////////////////////////////////////////////////////////////
//
// updating AppName to provided application name
//

// in config stub file
// we are separating config stub with app-used config so that 
// people can have localized config file in version control
//
$cfgfile = TARGETDIR . DS . 'config' . DS . 'Config.php.dist';
file_put_contents($cfgfile,str_replace('AppName', $app, file_get_contents($cfgfile)));

// Copying config stub to Config.php - which will be used by app
//
$cfgfile_target = TARGETDIR . DS . 'config' . DS . 'Config.php';
rcopy($cfgfile, $cfgfile_target);

//
//////////////////////////////////////////////////////////////////////////////////////////


// in form login file
$frmfile = TARGETDIR . DS . 'app' . DS . $app . DS . 'Form' . DS . 'LoginForm.php';
file_put_contents($frmfile,str_replace('AppName', $app, file_get_contents($frmfile)));


// in application files
$appfiles = scandir (TARGETDIR . DS . 'app' . DS . $app . DS);
foreach ($appfiles as $f) {
  if ($f == '.' || $f == '..') continue;
  if (is_file(TARGETDIR . DS . 'app' . DS . $app . DS . $f)) {
    file_put_contents(TARGETDIR . DS . 'app' . DS . $app . DS . $f, 
      str_replace('AppName', $app, 
        file_get_contents(TARGETDIR . DS . 'app' . DS . $app . DS . $f)));
  }
}

echo "  init-project: Fixing log mode\n";
chmod (TARGETDIR . DS . 'app' . DS . $app . DS . 'res' . DS . 'log' . DS . 'app.log', 0666);

echo "\n  init-project: Finished.\n";

echo "\n";
echo "        Make sure to setup the project with following steps:\n";
echo "------------------------------------------------------------------------\n";
echo "  1. Create the database\n";
echo "  2. Update and execute SQL files in app\\". $app."\\res\\sql\\ folder\n";
echo "     - Can use init-db.php to create database and execute the sql files\n";
echo "       command: php lib\PetakUmpet\bin\init-db.php APPNAME --reset\n";
echo "\n";
echo "Have fun!\n";


