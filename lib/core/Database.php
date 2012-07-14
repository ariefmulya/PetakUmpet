<?php

namespace PetakUmpet;

class Database {

  const FETCH_TYPE_ONE = 1;
  const FETCH_TYPE_ROW = 2;
  const FETCH_TYPE_ALL = 4;

  private $db;
  private $baseDatabaseObject;
  private $errorInfo;

  function __construct($dbConfigIndex=0)
  {
    $db_type = Configuration::Database($dbConfigIndex, Configuration::DBTYPE);
    $db_host = Configuration::Database($dbConfigIndex, Configuration::DBHOST);
    $db_user = Configuration::Database($dbConfigIndex, Configuration::DBUSER);
    $db_cred = Configuration::Database($dbConfigIndex, Configuration::DBCRED);
    $db_name = Configuration::Database($dbConfigIndex, Configuration::DBNAME);

    $class_name = '\\PetakUmpet\\' .  $db_type . 'Database';

    $this->baseDatabaseObject = new $class_name;

    $this->db = $this->Connect($db_host, $db_name, $db_user, $db_cred);

    if ($this->db === false) {
      throw Exception;
    }

    $this->baseDatabaseObject->setDbo($this->db);
  }

  function Connect($host, $dbname, $user, $cred, $extra=null)
  {
    try {
      $db = new \PDO($this->baseDatabaseObject->generateDSN($host, $dbname, $extra), $user, $cred);
    } catch (Exception $e) {
      echo 'Have you setup the database and update Configuration class?';

      Logger::log('Database: connection failed, ' . $e);
      return false;
    }
    return $db;
  }

  function Query($query, $trans=false)
  {
    Logger::log('Database: Query-> ' . $query);

    if ($trans) {
      try {
        $this->db->beginTransaction();
        $this->db->exec($query);
        $this->db->commit();
      } catch (Exception $e) {
        $db->rollback();

        Logger::log('Database: Query FAIL');
        echo "Query Failed: " . $e->getMessage();
        return false; 
      }
    } 

    return $this->db->query($query, \PDO::FETCH_ASSOC);
  }

  function preparedQuery($query, $params=array())
  {
    $st = $this->db->prepare($query);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    Logger::log('Database: preparedQuery-> ' . $st->queryString);

    if (!$st->execute($params)) {
      $this->errorInfo = array('query' => $st->queryString, 'error' => $st->errorInfo());

      Logger::log('Database: preparedQuery ERROR: ' . implode(' ', $st->errorInfo()));

      return false;
    }

    return $st;
  }

  function QueryAndFetch($query, $fetch_type, $trans=false)
  {
    $st = $this->Query($query, $trans);

    if ($st) {
      if ($fetch_type == self::FETCH_TYPE_ONE) return $st->fetchColumn();
      if ($fetch_type == self::FETCH_TYPE_ROW) return $st->fetchRow();
      if ($fetch_type == self::FETCH_TYPE_ALL) return $st->fetchAll();
    }
    return false;
  }

  function getErrorText()
  {
    return $this->errorInfo['error'][2];
  }

}

