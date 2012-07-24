<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Accessor {

  private $db;
  private $tableName;

  function __construct($tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->db = $db;
    $this->tableName = $db->escapeInput($tableName);
  }

  function CountByPK($pkeys, $pkvals)
  {
    $marker = array();

    foreach($pkvals as $k => $v) {
      $marker[] = ":$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('findByPK on table '.$this->tableName.' without primary keys');

    if (count($marker) != count ((array) $pkvals)) 
      throw new \Exception('findByPK not enough pkey values');
    
    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->tableName 
            . " WHERE (" . implode(', ', $pkeys) . ") = (" . implode(', ', $marker). ") ; ";

    return $this->db->QueryFetchOne($query, (array) $pkvals);
  }

  function findByPK($pkeys, $pkvals)
  {
    $marker = array();

    foreach($pkvals as $k => $v) {
      $marker[] = ":$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('findByPK on table '.$this->tableName.' without primary keys');

    if (count($marker) != count ((array) $pkvals)) 
      throw new \Exception('findByPK not enough pkey values');
    
    $query =  "SELECT * FROM " . $this->tableName 
            . " WHERE (" . implode(', ', $pkeys) . ") = (" . implode(', ', $marker). ") ; ";

    return $this->db->QueryFetchRow($query, (array) $pkvals);
  }

  function findBy(array $keyval) 
  {
    $marker = array();
    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker[] = "$k = :$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->tableName.' with no params');

    $query =  "SELECT * FROM " . $this->tableName . " WHERE " . implode(' AND ', $marker) . ";" ;

    return $this->db->QueryFetchAll($query, $keyval);
  }

  function findOneBy(array $keyval) 
  {
    $marker = array();

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker[] = "$k = :$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->tableName.' with no params');

    $query =  "SELECT * FROM " . $this->tableName . " WHERE " . implode(' AND ', $marker) ;
    $query = $this->db->getBaseDbo()->generateLimit($query, 1);

    return $this->db->QueryFetchRow($query, $keyval);
  }

  function findAll()
  {
    $query =  "SELECT * FROM " . $this->tableName;
    return $this->db->QueryFetchAll($query);
  }

  function insert($data)
  {
    if (count($data) == 0) return false;
    
    $marker = array();
    foreach ($data as $k => $v) {
      $marker[] = ":$k";
    }

    $query =  "INSERT INTO " . $this->tableName 
            . " (" . implode(',', array_keys($data)) . ") "
            . "VALUES ( " . implode(',', $marker) . ") " 
            . $this->db->getBaseDbo()->getLastIdQuery() 
             ;

    $res = $this->db->preparedQuery($query, $data);
    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  function update($data, $keyval)
  {
    $marker_data = array();
    $marker_keys = array();
    $pkeys = array_keys($keyval);

    foreach ($data as $k => $v) {
      if (in_array($k, $pkeys)) continue;
      $k = $this->db->escapeInput($k);
      if (in_array($k, $pkeys)) continue;
      $marker_data[] = "$k = :$k";
    }

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker_keys[] = "$k = :$k";
    }

    $query =  " UPDATE " . $this->tableName 
            . " SET " . implode(', ', $marker_data)
            . " WHERE " . implode(' AND ', $marker_keys) 
            . $this->db->getBaseDbo()->getLastIdQuery() ;

    $params = array_merge($data, $keyval);

    $res = $this->db->preparedQuery($query, $params);
    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }
}