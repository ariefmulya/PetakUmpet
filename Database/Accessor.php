<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Accessor {

  private $db;
  private $sourceData;  /* Can be tablename or a sub-query alias */
  private $schema;

  public function __construct($sourceData, $db=null, $schema=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->db = $db;
    $this->schema = $schema;
    $this->sourceData = $db->escapeInput($sourceData);
  }

  public function countAll()
  {
    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->sourceData;
    return $this->db->queryFetchOne($query);
  }

  public function countBy(array $keyval, $compareType=array())
  {
    $marker = array();
    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);
      $compare = '=';
      if (isset($compareType[$k])) $compare = $compareType[$k];
      $m = "$k $compare :$k";
      $marker[] = $m;
    }

    if (count($marker) == 0) 
      throw new \Exception ('countBy on table '.$this->sourceData.' with no params');

    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->sourceData . " WHERE " . implode(' AND ', $marker) . ";" ;
    return $this->db->queryFetchOne($query, $keyval);
  }

  public function findBy(array $keyval, $compareType=array()) 
  {
    $marker = array();
    $params = array();

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);

      $compare = '=';
      if (isset($compareType[$k])) $compare = $compareType[$k];
      $m = "$k $compare :$k";

      if ($v === null) {
        $m = "$k IS NULL";
      } else {
        $params[$k] = $v;
      }
      $marker[] = $m;
    }

    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->sourceData.' with no params');

    $query =  "SELECT * FROM " . $this->sourceData . " WHERE " . implode(' AND ', $marker) . ";" ;

    return $this->db->queryFetchAll($query, $params);
  }

  public function findOneBy(array $keyval, $compareType=array()) 
  {
    $marker = array();
    $params = array();

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);

      $compare = '=';
      if (isset($compareType[$k])) $compare = $compareType[$k];
      $m = "$k $compare :$k";

      if ($v === null) {
        $m = "$k IS NULL";
      } else {
        $params[$k] = $v;
      }
      $marker[] = $m;
    }

    if (count($marker) == 0) 
      throw new \Exception ('findBy on table '.$this->sourceData.' with no params');

    $query =  "SELECT * FROM " . $this->sourceData . " WHERE " . implode(' AND ', $marker) ;
    $query = $this->db->getDriver()->generateLimit($query, 1);

    return $this->db->queryFetchRow($query, $params);
  }

  public function findAll()
  {
    $query =  "SELECT * FROM " . $this->sourceData;
    return $this->db->queryFetchAll($query);
  }

  public function findAllForOptions($keyCol, $valCol)
  {
    $key = $this->db->escapeInput($keyCol);
    $val = $this->db->escapeInput($valCol);

    $query =  "SELECT $key, $val FROM " . $this->sourceData;
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

      if (!$colData[$c][Schema::SC_ISSTRING]) {
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

  public function countPagerData($filter=null)
  {
    $query =  "SELECT COUNT(*) AS cnt FROM " . $this->sourceData;

    $colData = array();
    if ($this->schema !== null) {
      $colData = $this->schema->getSchemaDetail();
    }

    $params = array();
    if ($filter && count($filter->getQueryData()) > 0) {
      list($where, $params) = $this->generatePagerFilter($filter, $colData);
      $query .= $where;
    }

    return $this->db->queryFetchOne($query, $params);
  }

  public function findPagerData($page, $nRows, $filter=null, $orderBy='id', $orderAsc=true)
  {
    $offset = max(($page-1) * $nRows, 0);
    $limit  = $nRows;

    $query  =  "SELECT * FROM " . $this->sourceData ;
    $colData = array();
    if ($this->schema !== null) {
      $colData = $this->schema->getSchemaDetail();
    }

    $params = array();
    if ($filter && count($filter->getQueryData()) > 0) {
      list($where, $params) = $this->generatePagerFilter($filter, $colData);
      $query .= $where;
    }

    $orderBy = $this->db->escapeInput($orderBy);
    $query .= " ORDER BY " . $orderBy . ($orderAsc ? " ASC " : " DESC ");

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

    $colQuote = $this->db->getDriver()->getColumnQuote();

    $query =  "INSERT INTO " . $this->sourceData 
            . " ($colQuote" .  implode("$colQuote,$colQuote", array_keys($marker)) . "$colQuote) "
            . "VALUES ( " . implode(',', $marker) . ") " 
            . $this->db->getDriver()->getLastIdQuery() 
             ;

    return array($query, $params);
  }

  private function prepareInsertParams($data, $columns=null)
  {
    $params = array();
    foreach ($data as $k => $v) {
      if ($k == 'id') continue;
      if ($columns !== null && !in_array($k, $columns)) continue;
      $params[$k] = $v;
    }

    return $params;
  }

  public function insert($data, $columns)
  {
    if ($this->schema === null) throw new \Exception("insert() called with no Schema defined");

    if (count($data) == 0) return false;

    $schema_columns = $this->schema->getColumnNames();

    $targetColumns = $schema_columns;
    if ($columns !== null) {
      $targetColumns = array_intersect($columns, $schema_columns);
    }

    $isMultiple = is_array(current($data));

    $firstRow = $isMultiple ? current($data) : $data;

    list($query, $params) = $this->prepareInsertQuery($firstRow, $targetColumns);

    if ($isMultiple) {
      $p1 = $params; 
      unset($params); 
      $params[] = $p1;
      array_shift($data);
      foreach ($data as $d) {
        $p = $this->prepareInsertParams($d, $targetColumns);
        $params[] = $p; 
      }
    }

    $res = $this->db->preparedQuery($query, $params, $isMultiple, $this->schema->getPdoTypes());

    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function update($data, $keyval, $columns)
  {
    if ($this->schema === null) throw new \Exception("update() called with no Schema defined");

    $marker_data = array();
    $marker_keys = array();

    $params = array();

    $colQuote = $this->db->getDriver()->getColumnQuote();

    foreach ($data as $k => $v) {
      // make sure we are not updating 'id' column
      if ($k == 'id') continue;
      $k = $this->db->escapeInput($k);
      if ($k == 'id') continue;
      // or primary keys
      if (in_array($k, array_keys($keyval))) continue;

      // make sure we are not updating unrequested columns
      if ($columns !== null && !in_array($k, $columns)) continue;

      $col = $colQuote . $k . $colQuote;
      $marker_data[] = "$col = :$k";
      $params[$k] = $v;
    }

    foreach ($keyval as $k => $v) {
      $k = $this->db->escapeInput($k);

      // make sure we are not updating unrequested columns
      if ($columns !== null && !in_array($k, $columns)) continue;
      $col = $colQuote . $k . $colQuote;
      $marker_keys[] = "$col = :$k";

      $params[$k] = $v;
    }

    $query =  " UPDATE " . $this->sourceData 
            . " SET " . implode(', ', $marker_data)
            . " WHERE " . implode(' AND ', $marker_keys) 
            . $this->db->getDriver()->getLastIdQuery() ;

    $res = $this->db->preparedQuery($query, $params, false, $this->schema->getPdoTypes());
    if ($res) {
      return $res->fetchColumn();
    }
    return false;
  }

  public function save($data, $pkeys, $columns=null)
  {
    $pkvals = array();

    foreach ($pkeys as $pk) {
      if (isset($data[$pk])) $pkvals[$pk] = $data[$pk];
    }

    $insertMode = true;
    if (count($pkvals) > 0 ) {
      $count = $this->countBy($pkvals);
      if ($count && $count > 0) $insertMode = false;
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

    $query = " DELETE FROM " . $this->sourceData
            . " WHERE " . implode(' AND ', $marker)
            ;

    $pdoTypes = array();
    if ($this->schema !== null) {
      $pdoTypes = $this->schema->getPdoTypes();
    }

    $res = $this->db->preparedQuery($query, $params, false, $pdoTypes);

    if ($res) return true;
    else return false;
  }

}
