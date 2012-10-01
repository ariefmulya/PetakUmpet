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

  public function findPagerData($page, $nRows, $filter=null, $colData = array(), $orderBy='id')
  {
    $offset = max(($page-1) * $nRows, 0);
    $limit  = $nRows;

    $query  =  "SELECT * FROM " . $this->tableName ;

    $params = array();
    if ($filter && count($filter->getQueryData()) > 0) {
      list($where, $params) = $this->generatePagerFilter($filter, $colData);
      $query .= $where;
    }

    $orderBy = $this->db->escapeInput($orderBy);
    $query .= " ORDER BY " . $orderBy;

    $query  = $this->db->getDriver()->generateLimit($query, $limit, $offset);

    return $this->db->queryFetchAll($query, $params);
  }

  private function prepareInsertQuery($data, $columns=null)
  {
    $marker = array();
    $params = array();

    foreach ($data as $k => $v) {
      if ($k == 'id') continue;
      if ($columns !== null && !in_array($k, $columns)) continue;
      $marker[$k] = ":$k";
      $params[$k] = $v;
    }

    $query =  "INSERT INTO " . $this->tableName 
            . " (" . implode(',', array_keys($marker)) . ") "
            . "VALUES ( " . implode(',', $marker) . ") " 
            . $this->db->getDriver()->getLastIdQuery() 
             ;

    return array($query, $params);
  }

  public function insert($data, $columns=null)
  {
    if (count($data) == 0) return false;

    $isMultiple = is_array(current($data));

    $firstRow = $isMultiple ? current($data) : $data;

    list($query, $params) = $this->prepareInsertQuery($firstRow, $columns);

    $res = $this->db->preparedQuery($query, $params, $isMultiple);

    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function update($data, $keyval, $columns=null)
  {
    $marker_data = array();
    $marker_keys = array();

    foreach ($keyval as $k=>$v) {
      $pkeys = $this->db->escapeInput($k);
    }

    $params = array();

    foreach ($data as $k => $v) {
      // make sure we are not updating 'id' column
      if ($k == 'id') continue;
      $k = $this->db->escapeInput($k);
      if ($k == 'id') continue;

      // make sure we are not updating unrequested columns
      if ($columns !== null && !in_array($k, $columns)) continue;

      $marker_data[] = "$k = :$k";
      $params[$k] = $v;
    }

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);

      // make sure we are not updating unrequested columns
      if ($columns !== null && !in_array($k, $columns)) continue;
      $marker_keys[] = "$k = :$k";

      $params[$k] = $v;
    }

    $query =  " UPDATE " . $this->tableName 
            . " SET " . implode(', ', $marker_data)
            . " WHERE " . implode(' AND ', $marker_keys) 
            . $this->db->getDriver()->getLastIdQuery() ;

    $res = $this->db->preparedQuery($query, $params);
    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function save($data, $pkeys, $columns=null)
  {
    $pkvals = array();
    $insertMode = false;

    foreach ($pkeys as $pk) {
      if (!isset($data[$pk]) || empty($data[$pk]) || $data[$pk] === null) {
        if (isset($data[$pk]) && $data[$pk] !== null) unset($data[$pk]);
        $insertMode=true;
        break;
      }
      $pkvals[$pk] = $data[$pk];
    }

    if ($insertMode) {
      return $this->insert($data, $columns);
    }
    return $this->update($data, $pkvals, $columns);
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
