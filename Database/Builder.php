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
  private $dbm;

  private $tableName;
  private $tableData;

  private $mc;      // schema model cache
  private $columns; // column names
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
    }

    $this->fkeys = $db->QueryFetchAll($db->getBaseDbo()->getForeignKeyQuery(), array($this->tableName));

  }

  function getSchema()
  {
    return $this->mc;
  }

  public function import($data)
  {
    if (!isset($data[0])) {
      $data = array ( 0 => $data );
    }
    $tableData = array();

    foreach ($data as $d) {
      $selected = array();

      foreach ($this->columns as $c) {
        if (isset($d[$c]) && $d[$c] !== null && $d[$c] != '') $selected[$c] = $d[$c];
      }       

      $tableData[] = $selected;

    }
    if (count($tableData) > 0) $this->tableData = $tableData;
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

      if ($insertMode) $this->dba->insert($d);
      else $this->dba->update($d, $pkvals);

      unset($pkvals);
    }
  }
}
