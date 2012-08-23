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

  public function __construct($configIndex=0, $initialize=true)
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

  public function getDbo()
  {
    return $this->db;
  }

  public function getDriver()
  {
    return $this->baseDriverObject;
  }

  public function Connect($host, $dbname, $user, $cred, $extra=null)
  {
    try {
      $this->db = new \PDO($this->baseDriverObject->generateDSN($host, $dbname, $extra), $user, $cred);
    } catch (Exception $e) {
      echo 'Have you setup the database and set configuration?';

      Logger::log('Database: connection failed, ' . $e);
      return false;
    }
  }

  public function Close()
  {
    $this->db = null;
  }
  
  public function query($query, $trans=false)
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

  public function multiPreparedQuery($value='')
  {
    # code...
  }

  public function preparedQuery($query, $params=array(), $trans=false)
  {
    $st = $this->db->prepare($query);
    $st->setFetchMode(\PDO::FETCH_ASSOC);

    Logger::log('Database: preparedQuery QUERY  = ' . $st->queryString);

    if ($trans === false) $params = array(0 => $params);

    if ($trans) $this->db->beginTransaction();

    foreach ($params as $p) {
      $ret = $st->execute($p);
      Logger::log('Database: preparedQuery PARAMS = (' . implode(',', $p) . ')');
    }
    
    if ($trans) $ret = $this->db->commit();

    if (!$ret) {
      $this->errorInfo = $st->errorInfo();
      Logger::log('Database: preparedQuery ERROR: ' . implode(' ', $st->errorInfo()));
      return false;
    }
    return $st;
  }

  private function queryFetch($query, $params=array(), $trans=false, $fetch_type=self::FETCH_TYPE_ALL)
  {
    if (is_array($params) && count($params) > 0) {
      $st = $this->preparedQuery($query, $params, $trans);
    } else {
      $st = $this->query($query, $trans);
    }

    if ($st) {
      if ($fetch_type == self::FETCH_TYPE_ONE) return $st->fetchColumn();
      if ($fetch_type == self::FETCH_TYPE_ROW) return $st->fetch();
      if ($fetch_type == self::FETCH_TYPE_ALL) return $st->fetchAll();
    }
    return false;
  }

  public function queryFetchAll($query, $params=array(), $trans=false)
  {
    return $this->queryFetch($query, $params, $trans);
  }

  public function queryFetchOne($query, $params=array(), $trans=false)
  {
    return $this->queryFetch($query, $params, $trans, self::FETCH_TYPE_ONE);
  }

  public function queryFetchRow($query, $params=array(), $trans=false)
  {
    return $this->queryFetch($query, $params, $trans, self::FETCH_TYPE_ROW);
  }

  public function getErrorText()
  {
    return $this->errorInfo[2];
  }

  /* taken from http://stackoverflow.com/questions/574805/how-to-escape-strings-in-mssql-using-php */
  public function escapeInput($data) {
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

