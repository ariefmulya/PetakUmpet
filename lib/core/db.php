<?php

// config
define('PU_DBTYPE', 'pgsql');
define('PU_DBHOST', 'localhost');
define('PU_DBNAME', 'petak_umpet');
define('PU_DBUSER', 'dbsys');
define('PU_DBCRED', '1');

// include the corresponding db functions
require_once(dirname(__FILE__) . '/'. PU_DBTYPE . '.php');

function db_init()
{
  $db = db_connect(PU_DBTYPE, PU_DBHOST, PU_DBNAME, PU_DBUSER, PU_DBCRED);
  return $db;
}

function db_connect($dbtype, $host, $dbname, $user, $cred, $extra=null)
{
  try {
    $db = new PDO(db_generate_dsn($dbtype, $host, $dbname, $extra), $user, $cred);
  } catch (Exception $e) {
    echo 'Have you setup the database and update db config file (slib/db.php) ?';
    log_debug('db_connect(): ' .$e);
    return false;
  }
  return $db;
}

function db_query($db, $query, $trans=false)
{
  log_debug('db_query() query: ' . $query);

  if ($trans) {
    try {
      $db->beginTransaction();
      $db->exec($query);
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      log_debug('db_query() Query FAIL: ' . $query);

      echo "Query Failed: " . $e->getMessage();
      return false; 
    }
  } 

  return $db->query($query, PDO::FETCH_ASSOC);
}

function db_prepared_query($db, $query, $params=array())
{
  $st = $db->prepare($query);
  $st->setFetchMode(PDO::FETCH_ASSOC);

  log_debug('db_prepared_query() query: ' . $st->queryString);

  if (!$st->execute($params)) {

    db_set_error($db, $st);

    log_debug('db_prepared_query() ERROR: ' . implode(' ', $st->errorInfo()));

    return false;
  }

  return $st;
}

function db_query_fetch_one($db, $query, $trans=false)
{
  $st = db_query($db, $query, $trans);

  if ($st) {
    return $st->fetchColumn();
  }
  return false;
}

function db_query_fetch_row($db, $query, $params=array(), $trans=false)
{
  if (strstr($query, "?")) {
    $st = db_prepared_query($db, $query, $params);
  } else {
    $st = db_query($db, $query, $trans);
  }

  if ($st) {
    return $st->fetch();
  }
  return false;
}

function db_query_fetch_all($db, $query, $trans=false)
{
  $st = db_query($db, $query, $trans);

  if ($st) {
    return $st->fetchAll();
  }
  return false;
}

function db_set_error(&$db, &$st)
{
  $db->errorInfo = array('query' => $st->queryString, 'error' => $st->errorInfo());
}

function db_set_error_text(&$db, $text)
{
  $db->errorInfo['error'][2] = $text; // the structure used for consistency with $st->errorInfo
}

function db_get_error($db)
{
  if (isset($db->errorInfo)) {
    return $db->errorInfo['error'][2];
  }
  return null;
}

