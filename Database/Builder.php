<?php

namespace PetakUmpet\Database;
use PetakUmpet\Singleton;

class Builder {

  const SC_COLNUM  = 'colnum';
  const SC_COLNAME = 'column';
  const SC_COLTYPE = 'type';
  const SC_MAXLEN  = 'maxlen';
  const SC_VARLEN  = 'varlen';
  const SC_PRIMARY = 'primary';
  const SC_NOTNULL = 'notnull';

  private $db;
  private $dba;

  private $tableName;
  private $tableData;

  private $mc;      // schema model cache
  private $columns; // column names
  private $displayCols; // displayed Columns
  private $coldata; // column types indexed by names
  private $pkeys;   // primary keys
  private $fkeys;   // foreign keys

  function __construct($tableName, $db=null)
  {
    if ($db === null) 
      $db = Singleton::acquire('\\PetakUmpet\\Database');

    $this->tableName = $db->escapeInput($tableName);

    $this->db  = $db;
    $this->dba = new \PetakUmpet\Database\Accessor($tableName, $db);

    $this->buildTableSchema();
  }

  function isStringType($type)
  {
    $str_types = array(
          'varchar' 
        , 'text'
        , 'char'
      );

    return in_array($type, $str_types);
  }

  public function getDbo()
  {
    return $this->db;
  }
  
  private function buildTableSchema()
  {
    $db =& $this->db;

    $this->mc = $db->QueryFetchAll($db->getBaseDbo()->getTableSchemaQuery(), array($this->tableName));

    if (!$this->mc) throw new \Exception('Building database schema failed');

    foreach ($this->mc as $s) {
      if ($s[self::SC_PRIMARY]) {
        $this->pkeys[] = $s[self::SC_COLNAME];
      }
      $this->columns[] = $s[self::SC_COLNAME];

      $this->coldata[$s[self::SC_COLNAME]] = array(
                      'type' => $s[self::SC_COLTYPE], 
                      'string' => $this->isStringType($s[self::SC_COLTYPE])
                    );
    }
    $this->fkeys = $db->QueryFetchAll($db->getBaseDbo()->getForeignKeyQuery(), array($this->tableName));

  }

  public function getSchema()
  {
    return $this->mc;
  }

  public function getColumnNames()
  {
    return $this->columns;
  }

  public function setDisplayColumns($displayCols)
  {
    $this->displayCols = $displayCols;
  }

  public function getColumnRelation($colName)
  {
    foreach ($this->fkeys as $rel) {
      if ($rel['childcol'] == $colName) 
        return $rel;
    }
    return false;
  }

  public function getOptionsFromRelations($colName, $descriptionCol='nama')
  {
    $opt = array();
    $db =& $this->db;

    foreach ($this->fkeys as $rel) {
      if ($rel['childcol'] == $colName) {
        $parent_id = $db->escapeInput($rel['parentcol']);
        $parent_text = $db->escapeInput($descriptionCol);
        $parent_table = $db->escapeInput($rel['parenttable']);
        $query = sprintf("SELECT * FROM %s", $parent_table);

        if (($res = $db->QueryFetchAll($query))) {
          foreach ($res as $r) {
            if (isset($r[$parent_text])) {
              $opt[$r[$parent_id]] = $r[$parent_text]; 
            } else {
              // rather crude but simple hack
              // to get the parent column names
              // FIXME?
              foreach ($r as $k => $v) {
                if (is_string($v) && $k != $parent_id) {
                  $opt[$r[$parent_id]] = $v;
                  break;
                }
              }
            }
          }
        }
      }
    }
    return $opt;
  }

  public function getCountRows()
  {
    return $this->dba->CountAll();
  }

  public function import($data)
  {
    if (count($data) == 0) 
      return;

    if (!isset($data[0])) {
      $data = array ( 0 => $data );
    }
    $tableData = array();

    $columns = count($this->displayCols) > 0 ? $this->displayCols : $this->columns;

    foreach ($data as $d) {
      $filterData = array();

      foreach ($columns as $c) {
        if (isset($d[$c]) && $d[$c] !== null && ($d[$c] != '' || $d[$c] === false)) $filterData[$c] = $d[$c];
      }       

      $tableData[] = $filterData;

    }
    if (count($tableData) > 0) $this->tableData = $tableData;
  }

  public function getTableData()
  {
    return $this->tableData;
  }

  public function importById($value)
  {
    return $this->import($this->dba->findByPk(array('id'), array($value)));
  }

  public function importAll()
  {
    return $this->import($this->dba->findAll());
  }

  public function getCountPagerData($filter=null)
  {
    return $this->dba->CountPagerData($filter, $this->coldata);
  }

  public function importPagerData($page, $nRows, $filter=null)
  {
    return $this->import($this->dba->findPagerData($page, $nRows, $filter, $this->coldata));
  }

  public function save()
  {
    foreach ($this->tableData as $d) {
      $insertMode = false;

      $pkvals = array();

      foreach ($this->pkeys as $pk) {
        if (!isset($d[$pk]) || empty($d[$pk]) || $d[$pk] === null) {
          $insertMode=true;
          break;
        }
        $pkvals[$pk] = $d[$pk];
      }

      if ($insertMode) {
        $ret = $this->dba->insert($d);
      } else { 
        $ret = $this->dba->update($d, $pkvals);
      }

      unset($pkvals);
    }

    return $ret;
  }
}
