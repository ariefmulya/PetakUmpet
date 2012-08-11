<?php

namespace PetakUmpet;

use PetakUmpet\Config;

class Database {

  const FETCH_TYPE_ONE = 1;
  const FETCH_TYPE_ROW = 2;
  const FETCH_TYPE_ALL = 4;

  private $db;
  private $baseDriverObject;
  private $errorInfo;

  function __construct($configIndex=0, $initialize=true)
  {
    $config  = Singleton::acquire('\\PetakUmpet\\Config');
    $dbConfig = $config->getDbConfig($configIndex);

    $db_type = $dbConfig[Config::DBTYPE];
    $db_host = $dbConfig[Config::DBHOST];
    $db_user = $dbConfig[Config::DBUSER];
    $db_cred = $dbConfig[Config::DBCRED];
    $db_name = $dbConfig[Config::DBNAME];

    $class_name = '\\PetakUmpet\\Database\\Driver\\' .  $db_type;

    $this->baseDriverObject = new $class_name;

    if ($initialize) {
      $this->Connect($db_host, $db_name, $db_user, $db_cred);
    }

    $this->baseDriverObject->setDbo($this);
  }

  function __call($name, $args)
  {
    if (substr($name, 0, 10) == 'QueryFetch') {
      $fetchName = strtolower(substr($name, 10));

      switch ($fetchName) {
        case 'row':
          $fetchType = self::FETCH_TYPE_ROW;
          break;
        case 'one':
          $fetchType = self::FETCH_TYPE_ONE;
          break;
        case 'all':
          $fetchType = self::FETCH_TYPE_ALL;
          break;
        default:
          throw new \Exception('Fetch type unknown');
      }

      return $this->QueryFetch(
          $args[0], 
          (isset($args[1]) ? $args[1] : null), 
          $fetchType, 
          (isset($args[2]) ? $args[2] : false)
        );
    }
  }

  function getDbo()
  {
    return $this->db;
  }

  function getBaseDbo()
  {
    return $this->baseDriverObject;
  }

  function Connect($host, $dbname, $user, $cred, $extra=null)
  {
    try {
      $this->db = new \PDO($this->baseDriverObject->generateDSN($host, $dbname, $extra), $user, $cred);
    } catch (Exception $e) {
      echo 'Have you setup the database and set configuration?';

      Logger::log('Database: connection failed, ' . $e);
      return false;
    }
  }

  function Close()
  {
    $this->db = null;
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

    if (! ($res = $this->db->query($query, \PDO::FETCH_ASSOC)) ) {
      $this->errorInfo = $this->db->errorInfo();
      Logger::log('Database: Query ERROR: ' . implode(' ', $this->errorInfo));
      return false;
    }
    return $res;
  }

  function preparedQuery($query, $params=array(), $trans=false)
  {
    $st = $this->db->prepare($query);
    $st->setFetchMode(\PDO::FETCH_ASSOC);

    Logger::log('Database: preparedQuery QUERY  = ' . $st->queryString);
    Logger::log('Database: preparedQuery PARAMS = (' . implode(',', $params) . ')');

    if (!$st->execute($params)) {
      $this->errorInfo = $st->errorInfo();

      Logger::log('Database: preparedQuery ERROR: ' . implode(' ', $st->errorInfo()));

      return false;
    }

    return $st;
  }

  private function QueryFetch($query, $params=array(), $fetch_type=self::FETCH_TYPE_ALL, $trans=false)
  {
    if (is_array($params) && count($params) > 0) {
      $st = $this->preparedQuery($query, $params, $trans);
    } else {
      $st = $this->Query($query, $trans);
    }

    if ($st) {
      if ($fetch_type == self::FETCH_TYPE_ONE) return $st->fetchColumn();
      if ($fetch_type == self::FETCH_TYPE_ROW) return $st->fetch();
      if ($fetch_type == self::FETCH_TYPE_ALL) return $st->fetchAll();
    }
    return false;
  }

  function getErrorText()
  {
    return $this->errorInfo[2];
  }

  /* taken from http://stackoverflow.com/questions/574805/how-to-escape-strings-in-mssql-using-php */
  function escapeInput($data) {
    if ( !isset($data) or empty($data) ) return '';
    if ( is_numeric($data) ) return $data;

    $non_displayables = array(
        '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/',             // url encoded 16-31
        '/[\x00-\x08]/',            // 00-08
        '/\x0b/',                   // 11
        '/\x0c/',                   // 12
        '/[\x0e-\x1f]/'             // 14-31
    );
    foreach ( $non_displayables as $regex )
        $data = preg_replace( $regex, '', $data );
    $data = str_replace("'", "''", $data );
    return $data;
  }

}

