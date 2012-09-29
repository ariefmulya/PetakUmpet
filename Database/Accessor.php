<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Accessor {

  private $db;
  private $tableName;

  public function __construct($tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->db = $db;
    $this->tableName = $db->escapeInput($tableName);
  }

  public function countByPK($pkeys, $pkvals)
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

    return $this->db->queryFetchOne($query, (array) $pkvals);
  }

  public function countAll()
  {
    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->tableName;
    return $this->db->queryFetchOne($query);
  }

  public function countBy(array $keyval)
  {
    $marker = array();
    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker[] = "$k = :$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('countBy on table '.$this->tableName.' with no params');

    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->tableName . " WHERE " . implode(' AND ', $marker) . ";" ;
    return $this->db->queryFetchOne($query, $keyval);
  }


  public function findByPK($pkeys, $pkvals)
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

    return $this->db->queryFetchRow($query, (array) $pkvals);
  }

  public function findBy(array $keyval) 
  {
    $marker = array();
    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker[] = "$k = :$k";
    }

    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->tableName.' with no params');

    $query =  "SELECT * FROM " . $this->tableName . " WHERE " . implode(' AND ', $marker) . ";" ;

    return $this->db->queryFetchAll($query, $keyval);
  }

  public function findOneBy(array $keyval) 
  {
    $marker = array();

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $marker[] = "$k = :$k";
    }


    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->tableName.' with no params');

    $query =  "SELECT * FROM " . $this->tableName . " WHERE " . implode(' AND ', $marker) ;
    $query = $this->db->getDriver()->generateLimit($query, 1);

    return $this->db->queryFetchRow($query, $keyval);
  }

  public function findAll()
  {
    $query =  "SELECT * FROM " . $this->tableName;
    return $this->db->queryFetchAll($query);
  }

  public function findAllForOptions($keyCol, $valCol)
  {
    $key = $this->db->escapeInput($keyCol);
    $val = $this->db->escapeInput($valCol);

    $query =  "SELECT $key, $val FROM " . $this->tableName;
    $res = $this->db->queryFetchAll($query);

    $options = array(null => '');
    if ($res) {
      foreach ($res as $r) {
        $options[$r[$key]] = $r[$val];
      }
    }
    return $options;
  }

  private function generatePagerFilter($filter, $colData)
  {
    $data = $filter->getQueryData();
    $marker = array();
    $params = array();

    foreach ($data as $c => $v) {
      $s = $c;
      if (!isset($colData[$c]) || $v === null || $v == '') continue;

      if (!$colData[$c][Schema::COL_IS_STRING]) {
        $s = "CAST ($c AS text) ";
      }
      $marker[] = $s ." ILIKE :$c" ;
      $params[$c] = $v;
    }

    $ret = array('', array());

    if (count($marker) > 0 ) {
      $where = " WHERE " . implode (' OR ', $marker);
      $ret = array($where, $params);
    }

    return $ret;
  } 

  public function countPagerData($filter=null, $colData=array())
  {
    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->tableName;

    $params = array();
    if ($filter && count($filter->getQueryData()) > 0) {
      list($where, $params) = $this->generatePagerFilter($filter, $colData);
      $query .= $where;
    }

    return $this->db->queryFetchOne($query, $params);
  }

  public function findPagerData($page, $nRows, $filter=null, $colData = array())
  {
    $offset = max(($page-1) * $nRows, 0);
    $limit  = $nRows;

    $query  =  "SELECT * FROM " . $this->tableName ;

    $params = array();
    if ($filter && count($filter->getQueryData()) > 0) {
      list($where, $params) = $this->generatePagerFilter($filter, $colData);
      $query .= $where;
    }

    $query .= " ORDER BY id ";

    $query  = $this->db->getDriver()->generateLimit($query, $limit, $offset);

    return $this->db->queryFetchAll($query, $params);
  }

  private function prepareInsertQuery($data)
  {
    $marker = array();
    foreach ($data as $k => $v) {
      $marker[] = ":$k";
    }

    $query =  "INSERT INTO " . $this->tableName 
            . " (" . implode(',', array_keys($data)) . ") "
            . "VALUES ( " . implode(',', $marker) . ") " 
            . $this->db->getDriver()->getLastIdQuery() 
             ;

    return $query;
  }

  public function insert($data)
  {
    if (count($data) == 0) return false;

    $isMultiple = is_array(current($data));

    $firstRow = $isMultiple ? current($data) : $data;

    $query = $this->prepareInsertQuery($firstRow);

    $res = $this->db->preparedQuery($query, $data, $isMultiple);

    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function update($data, $keyval)
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
            . $this->db->getDriver()->getLastIdQuery() ;

    $params = array_merge($data, $keyval);

    $res = $this->db->preparedQuery($query, $params);
    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function save($data, $pkeys)
  {
    $pkvals = array();
    $insertMode = false;

    foreach ($pkeys as $pk) {
      if (!isset($data[$pk]) || empty($data[$pk]) || $data[$pk] === null) {
        if (isset($data[$pk]) || $data[$pk] === null) unset($data[$pk]);
        $insertMode=true;
        break;
      }
      $pkvals[$pk] = $data[$pk];
    }

    if ($insertMode) {
      return $this->insert($data);
    }
    return $this->update($data, $pkvals);
  }
  
  public function delete($params = array())
  {
    foreach ($params as $k=>$v) {
      $marker[] = "$k = :$k";
    }

    $query = " DELETE FROM " . $this->tableName
            . " WHERE " . implode(' AND ', $marker)
            ;

    $res = $this->db->preparedQuery($query, $params);

    if ($res) return true;
    else return false;
  }

}
