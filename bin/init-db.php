<?php

define('DS', DIRECTORY_SEPARATOR); // quicker
define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS);

if (!isset($argv[1]) || $argv[1] == '') {
  echo "  init-db: Please provide application name\n";
  exit();
}

$app = $argv[1];
$mode = isset($argv[2]) ? $argv[2] : '--normal';
if ($mode=='') $mode='--normal';

echo "  init-db: Preparing DB for " . $app . "\n";

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

use PetakUmpet\Config as Config;

if ($mode == '--reset') {
  $config = new Config;
  $dbConfig = $config->getDbConfig(0);
  $db_host = $dbConfig[Config::DBHOST];
  $db_user = $dbConfig[Config::DBUSER];
  $db_cred = $dbConfig[Config::DBCRED];
  $db_name = $dbConfig[Config::DBNAME];

  $dbi = new PetakUmpet\Database(0, false);
  $dbi->Connect($db_host, null, $db_user, $db_cred);

  $dropDbSql = 'DROP DATABASE ' . $db_name . ';';
  $createDbSql = 'CREATE DATABASE ' . $db_name . ';';
  echo "  init-db: Dropping database $db_name...";
  petakumpet_exec($dbi, $dropDbSql);
  echo "\n";
  echo "  init-db: Creating database $db_name...";
  petakumpet_exec($dbi, $createDbSql);
  echo "\n";
  $dbi->Close();
}

$sql_dir = PU_DIR . 'app' . DS . $app . DS . 'res' . DS . 'sql';
$sql_files = scandir($sql_dir);
$db = new PetakUmpet\Database();

foreach ($sql_files as $f) {
  if ($f == '..' || $f == '.') continue;
  if (is_file($sql_dir . DS . $f)) {
    echo "  init-db: Executing query in $f ";
    if (petakumpet_exec($db, ($createSql=file_get_contents($sql_dir . DS . $f)))) {
      echo "...done\n";
    }
  }
}
echo "\n";
echo "  init-db: Initialize database done\n";
