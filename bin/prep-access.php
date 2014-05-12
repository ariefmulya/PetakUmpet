<?php

define('DS', DIRECTORY_SEPARATOR);
define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS);
define('SKELDIR', __DIR__ . DS . '..' . DS . 'Skeleton' . DS);
define('TARGETDIR', SKELDIR . '..' . DS . '..' . DS . '..' . DS);

if (!isset($argv[1]) || $argv[1] == '') {
  echo "  prep-access: Please provide application name\n";
  exit();
}

$app = $argv[1];
$target_table = isset($argv[2]) ? $argv[2] : 'accessdata';
$mode = isset($argv[3]) ? $argv[3] : '--standard';

$src = TARGETDIR . 'app' . DS . $app;

echo "  prep-access: Acquiring access data for " . $app . "\n";

$files = scandir($src);
$accessnames = array();
foreach ($files as $file) {
  if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
    $methods = preg_grep("/function.*Action/", file($src.DS.$file));
    $cname = pathinfo($file, PATHINFO_FILENAME);
    $cname = str_replace('Application', '', $cname);
    foreach ($methods as $m) {
      $name = preg_replace("/^.*function[ ]+/", "", $m);
      $name = preg_replace("/Action.*$/", "", $name);
      $accessnames[] =  $cname . '/' .  trim($name);
    }
  }
}

include(__DIR__ . DS . '..' . DS . 'Loader.php');
$loader = new PetakUmpet\Loader;
$loader->register();

function petakumpet_exec($db, $query)
{
  if (!($db->getDbo()->exec($query))) {
    $ret = $db->getDbo()->errorInfo();
    echo $ret[2];
    return false;
  }  
  return true;
}
$db = new PetakUmpet\Database();

echo "  Adding actions to $target_table...\n";

$t = $db->escapeInput($target_table);

if ($mode == "--reset") {
  echo "   ** RESET MODE ** \n";
  petakumpet_exec($db, "TRUNCATE TABLE $t RESTART IDENTITY CASCADE;");
}
$n=0;
foreach ($accessnames as $a) {
  $a = $db->escapeInput($a);
  $query = "INSERT INTO $t (name) VALUES ('$a');";
  $n++;
  if ($mode == "--print") {
    echo $query ."\n";
    continue;
  }
  petakumpet_exec($db, $query);
}

echo "  --- Finished: $n access added --- ";
echo "\n";
echo "\n";
