<?php

define('DS', DIRECTORY_SEPARATOR); // quicker
define('PU_DIR',  __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS);

include(__DIR__ . DS . '..' . DS . 'Configuration.php');
include(__DIR__ . DS . '..' . DS . 'Loader.php');

$loader = new PetakUmpet\Loader;
$loader->register();

$dbConfigIndex = 0;

use PetakUmpet\Configuration;
$db_host = Configuration::Database($dbConfigIndex, Configuration::DBHOST);
$db_user = Configuration::Database($dbConfigIndex, Configuration::DBUSER);
$db_cred = Configuration::Database($dbConfigIndex, Configuration::DBCRED);
$db_name = Configuration::Database($dbConfigIndex, Configuration::DBNAME);

$dbi = new PetakUmpet\Database(0, false);
$dbi->Connect($db_host, null, $db_user, $db_cred);

$dropDbSql = 'DROP DATABASE ' . $db_name . ';';
$createDbSql = 'CREATE DATABASE ' . $db_name . ';';
$createSql = file_get_contents(PU_DIR . 'res' . DS . 'sql' . DS . 'schema.sql');

function petakumpet_exec($db, $query)
{
  if (!($db->getDbo()->exec($query))) {
    $ret = $db->getDbo()->errorInfo();
    echo $ret[2];
    return false;
  }  
  return true;
}

echo "Dropping database $db_name...";
petakumpet_exec($dbi, $dropDbSql);
echo "\n";
echo "Creating database $db_name...";
petakumpet_exec($dbi, $createDbSql);
echo "\n";
$dbi->Close();

$db = new PetakUmpet\Database();
if (petakumpet_exec($db, $createSql)) {
    echo "Initialize database done\n";
}

